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
    

    <link rel="stylesheet" href="index.css">

</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Sistema de Gestión</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-nav">
                <a class="nav-item nav-link" href="index.php">Inicio</a>
            </div>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                <div class="navbar-nav">

                    <?php if ($user_logged_in): ?>
                        <a class="nav-item nav-link" href="perfil.php">Mi Perfil</a>
                        <a class="nav-item nav-link" href="gestion_inventario.php">Gestión de Inventario</a>
                        <a class="nav-item nav-link" href="gestion_ventas.php">Gestión de Ventas</a>
                        <a class="nav-item nav-link" href="gestion_compras.php">Gestión de Compras</a>
                        <a class="nav-item nav-link" href="soporte_mantenimiento.php">Soporte y Mantenimiento</a>
                        
                        <!-- Si el usuario es administrador, mostrar opciones en un desplegable -->
                        <?php if ($user_role == 'Administrador'): ?>
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Administración
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li><a class="dropdown-item" href="administrar_usuarios.php">Administrar Usuarios</a></li>
                                    <li><a class="dropdown-item" href="reportes.php">Reportes</a></li>
                                    <li><a class="dropdown-item" href="configuracion_sistema.php">Configuración del Sistema</a></li>
                                    <li><a class="dropdown-item" href="integraciones_externas.php">Integraciones Externas</a></li>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <a class="nav-item nav-link" href="logout.php">Cerrar Sesión</a>
                    <?php else: ?>
                        <a class="nav-item nav-link" href="login.php">Iniciar Sesión</a>
                        <a class="nav-item nav-link" href="register.php">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap 5 Bundle que incluye Popper.js -->

    
    
</body>

</html>
