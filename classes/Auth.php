<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Autenticar usuario con username/email y contraseña
     */
    public function login($username, $password, $remember = false) {
        try {
            // Buscar usuario por username o email
            $stmt = $this->db->prepare("
                SELECT id, username, email, password_hash, first_name, last_name, role, is_active 
                FROM users 
                WHERE (username = :username OR email = :username) AND is_active = 1
            ");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch();
            
            if (!$user || !verify_password($password, $user['password_hash'])) {
                return [
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ];
            }
            
            // Crear sesión
            $this->createSession($user['id'], $remember);
            
            // Actualizar último login
            $this->updateLastLogin($user['id']);
            
            // Registrar en logs
            $this->logUserAction($user['id'], 'LOGIN', null, null, ['username' => $username]);
            
            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'role' => $user['role']
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }
    
    /**
     * Crear sesión de usuario
     */
    private function createSession($userId, $remember = false) {
        // Regenerar ID de sesión por seguridad
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $userId;
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Configurar tiempo de sesión
        if ($remember) {
            $_SESSION['remember_me'] = true;
            // Extender tiempo de sesión a 30 días
            ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60);
        } else {
            $_SESSION['remember_me'] = false;
            // Sesión normal de 2 horas
            ini_set('session.gc_maxlifetime', 2 * 60 * 60);
        }
        
        // Crear registro en tabla de sesiones
        $sessionId = session_id();
        $expiresAt = date('Y-m-d H:i:s', time() + ini_get('session.gc_maxlifetime'));
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt = $this->db->prepare("
            INSERT INTO user_sessions (id, user_id, ip_address, user_agent, expires_at) 
            VALUES (:session_id, :user_id, :ip_address, :user_agent, :expires_at)
            ON DUPLICATE KEY UPDATE 
            expires_at = :expires_at, ip_address = :ip_address, user_agent = :user_agent
        ");
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':ip_address', $ipAddress);
        $stmt->bindParam(':user_agent', $userAgent);
        $stmt->bindParam(':expires_at', $expiresAt);
        $stmt->execute();
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            // Registrar logout en logs
            $this->logUserAction($_SESSION['user_id'], 'LOGOUT', null, null, []);
            
            // Eliminar sesión de la base de datos
            $sessionId = session_id();
            $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE id = :session_id");
            $stmt->bindParam(':session_id', $sessionId);
            $stmt->execute();
        }
        
        // Destruir sesión
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function isAuthenticated() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
            return false;
        }
        
        // Verificar si la sesión existe en la base de datos
        $sessionId = session_id();
        $stmt = $this->db->prepare("
            SELECT id FROM user_sessions 
            WHERE id = :session_id AND user_id = :user_id AND expires_at > NOW()
        ");
        $stmt->bindParam(':session_id', $sessionId);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        return $stmt->fetch() !== false;
    }
    
    /**
     * Obtener información del usuario actual
     */
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        $stmt = $this->db->prepare("
            SELECT id, username, email, first_name, last_name, role, document_type, document_number, 
                   is_active, created_at, last_login
            FROM users 
            WHERE id = :user_id AND is_active = 1
        ");
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($role) {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $role;
    }
    
    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin() {
        return $this->hasRole('admin');
    }
    
    /**
     * Requerir autenticación
     */
    public function requireAuth() {
        if (!$this->isAuthenticated()) {
            header('Location: /cci-surveys/auth/login.php');
            exit();
        }
    }
    
    /**
     * Requerir rol específico
     */
    public function requireRole($role) {
        $this->requireAuth();
        
        if (!$this->hasRole($role)) {
            header('HTTP/1.1 403 Forbidden');
            include __DIR__ . '/../includes/403.php';
            exit();
        }
    }
    
    /**
     * Requerir rol de administrador
     */
    public function requireAdmin() {
        $this->requireRole('admin');
    }
    
    /**
     * Actualizar último login
     */
    private function updateLastLogin($userId) {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
    }
    
    /**
     * Registrar acción del usuario en logs
     */
    private function logUserAction($userId, $action, $tableName = null, $recordId = null, $data = []) {
        try {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            $stmt = $this->db->prepare("
                INSERT INTO system_logs (user_id, action, table_name, record_id, new_values, ip_address, user_agent) 
                VALUES (:user_id, :action, :table_name, :record_id, :new_values, :ip_address, :user_agent)
            ");
            
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':table_name', $tableName);
            $stmt->bindParam(':record_id', $recordId);
            $stmt->bindParam(':new_values', json_encode($data));
            $stmt->bindParam(':ip_address', $ipAddress);
            $stmt->bindParam(':user_agent', $userAgent);
            
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error logging user action: " . $e->getMessage());
        }
    }
    
    /**
     * Limpiar sesiones expiradas
     */
    public function cleanExpiredSessions() {
        try {
            $stmt = $this->db->prepare("DELETE FROM user_sessions WHERE expires_at < NOW()");
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Error cleaning expired sessions: " . $e->getMessage());
        }
    }
    
    /**
     * Cambiar contraseña
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Verificar contraseña actual
            $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            $user = $stmt->fetch();
            if (!$user || !verify_password($currentPassword, $user['password_hash'])) {
                return [
                    'success' => false,
                    'message' => 'La contraseña actual es incorrecta'
                ];
            }
            
            // Actualizar contraseña
            $newHash = hash_password($newPassword);
            $stmt = $this->db->prepare("UPDATE users SET password_hash = :new_hash WHERE id = :user_id");
            $stmt->bindParam(':new_hash', $newHash);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            // Registrar en logs
            $this->logUserAction($userId, 'CHANGE_PASSWORD', 'users', $userId, []);
            
            return [
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ];
            
        } catch (Exception $e) {
            error_log("Error changing password: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor'
            ];
        }
    }
}
?>
