<?php
include('header.php');  // Incluir el header con la barra de navegación

require_once 'config.php';  // Incluir la configuración de la base de datos

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirigir a la página de login si no está logueado
    exit;
}

// Obtener los datos del usuario logueado
$user_id = $_SESSION['user_id'];
$sql = "SELECT id_usuario, correo_electronico, nombre, rol, contrasena FROM Usuarios WHERE id_usuario = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Actualizar perfil
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    
    // Solo actualizar la contraseña si el campo no está vacío
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql_update = "UPDATE Usuarios SET nombre='$nombre', correo_electronico='$correo', contrasena='$password' WHERE id_usuario=$user_id";
    } else {
        $sql_update = "UPDATE Usuarios SET nombre='$nombre', correo_electronico='$correo' WHERE id_usuario=$user_id";
    }

    // Permitir que el administrador pueda cambiar el rol
    if ($user['rol'] == 'Administrador' && isset($_POST['rol'])) {
        $rol = $_POST['rol'];
        $sql_update = "UPDATE Usuarios SET nombre='$nombre', correo_electronico='$correo', rol='$rol' WHERE id_usuario=$user_id";
    }

    if ($conn->query($sql_update) === TRUE) {
        $message = "<div class='alert alert-success'>Perfil actualizado correctamente.</div>";
        // Actualizar la sesión con los nuevos datos
        $_SESSION['username'] = $correo;
    } else {
        $message = "<div class='alert alert-danger'>Error al actualizar el perfil: " . $conn->error . "</div>";
    }
}
?>

<div class="container mt-5">
    <h2>Mi Perfil</h2>
    <?php echo $message; ?>
    <form action="perfil.php" method="post">
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $user['nombre']; ?>" required>
        </div>
        <div class="form-group">
            <label for="correo">Correo Electrónico</label>
            <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $user['correo_electronico']; ?>" required>
        </div>
        
        <!-- Campo para cambiar la contraseña -->
        <div class="form-group">
            <label for="password">Contraseña (dejar en blanco para mantener la actual)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <!-- Si el usuario es Administrador, mostrar campos adicionales -->
        <?php if ($user['rol'] == 'Administrador'): ?>
            <div class="form-group">
                <label for="rol">Rol</label>
                <select class="form-control" id="rol" name="rol">
                    <option value="Administrador" <?php echo $user['rol'] == 'Administrador' ? 'selected' : ''; ?>>Administrador</option>
                    <option value="Usuario" <?php echo $user['rol'] == 'Usuario' ? 'selected' : ''; ?>>Usuario</option>
                </select>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
    </form>
</div>

<?php
include('footer.php');  // Incluir el footer
?>
