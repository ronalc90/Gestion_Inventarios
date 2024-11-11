<?php
require_once 'config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$user_logged_in = isset($_SESSION['user_id']);
$user_role = $user_logged_in ? $_SESSION['user_role'] : null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión</title>
    <link rel="stylesheet" href="styles/header.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <a class="navbar-brand" href="#">Sistema de Gestión</a>
            <div class="nav-items">
                <a class="nav-item" href="index.php">Inicio</a>
                <?php if ($user_logged_in): ?>
                    <a class="nav-item" href="perfil.php">Mi Perfil</a>
                    <a class="nav-item" href="gestion_inventario.php">Gestión de Inventario</a>
                    <a class="nav-item" href="gestion_ventas.php">Gestión de Ventas</a>
                    <a class="nav-item" href="gestion_compras.php">Gestión de Compras</a>
                    <a class="nav-item" href="soporte_mantenimiento.php">Soporte y Mantenimiento</a>
                    
                    <?php if ($user_role == 'Administrador'): ?>
                        <div class="dropdown">
                            <a class="nav-item dropdown-toggle" href="#">Administración</a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="administrar_usuarios.php">Administrar Usuarios</a>
                                <a class="dropdown-item" href="reportes.php">Reportes</a>
                                <a class="dropdown-item" href="configuracion_sistema.php">Configuración del Sistema</a>
                                <a class="dropdown-item" href="integraciones_externas.php">Integraciones Externas</a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a class="nav-item" href="login.php">Iniciar Sesión</a>
                    <a class="nav-item" href="register.php">Registrarse</a>
                <?php endif; ?>
            </div>

            <?php if ($user_logged_in): ?>
                <a class="logout-button" href="logout.php">Cerrar Sesión</a>
            <?php endif; ?>
        </nav>
    </header>
</body>
</html>
