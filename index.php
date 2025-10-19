<?php
session_start();
require_once 'config/database.php';
require_once 'config/config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

// Redirigir según el rol del usuario
switch ($_SESSION['role']) {
    case 'admin':
        header('Location: admin/dashboard.php');
        break;
    case 'surveyor':
        header('Location: surveyor/dashboard.php');
        break;
    default:
        header('Location: auth/login.php');
        break;
}
?>
