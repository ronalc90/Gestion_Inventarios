<?php
include('header.php');  // Incluir el header con la barra de navegación

require_once 'config.php';  // Incluir la configuración de la base de datos

// Verificar si el usuario tiene el rol de administrador
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'Administrador') {
    header("Location: index.php");  // Redirigir a la página principal si no es administrador
    exit;
}

// Eliminar usuario si se recibe una solicitud de eliminación
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $sql = "DELETE FROM Usuarios WHERE id_usuario = $user_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Usuario eliminado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar el usuario: " . $conn->error . "</div>";
    }
}

// Crear o editar usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $nombre = $_POST['nombre'];
    $rol = $_POST['rol'];
    $estado = $_POST['estado'];
    
    // Editar usuario
    if (isset($_POST['id_usuario']) && !empty($_POST['id_usuario'])) {
        $id_usuario = intval($_POST['id_usuario']);
        $sql = "UPDATE Usuarios SET correo_electronico='$email', nombre='$nombre', rol='$rol', estado='$estado' WHERE id_usuario=$id_usuario";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>Usuario actualizado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al actualizar el usuario: " . $conn->error . "</div>";
        }
    } 
    // Crear nuevo usuario
    else {
        $sql = "INSERT INTO Usuarios (correo_electronico, nombre, rol, estado) VALUES ('$email', '$nombre', '$rol', '$estado')";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>Usuario creado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al crear el usuario: " . $conn->error . "</div>";
        }
    }
}

// Obtener usuarios
$sql = "SELECT id_usuario, correo_electronico, nombre, rol, estado FROM Usuarios";
$result = $conn->query($sql);

?>

<div class="container mt-5">
    <h2>Administrar Usuarios</h2>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal">Crear Usuario</button>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Correo Electrónico</th>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_usuario']; ?></td>
                        <td><?php echo $row['correo_electronico']; ?></td>
                        <td><?php echo $row['nombre']; ?></td>
                        <td><?php echo $row['rol']; ?></td>
                        <td><?php echo $row['estado']; ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal" data-id="<?php echo $row['id_usuario']; ?>" data-email="<?php echo $row['correo_electronico']; ?>" data-nombre="<?php echo $row['nombre']; ?>" data-rol="<?php echo $row['rol']; ?>" data-estado="<?php echo $row['estado']; ?>">Editar</button>
                            <a href="administrar_usuarios.php?delete=<?php echo $row['id_usuario']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No se encontraron usuarios.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para crear usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Crear Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="administrar_usuarios.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="rol">Rol</label>
                        <select class="form-control" id="rol" name="rol">
                            <option value="Administrador">Administrador</option>
                            <option value="Usuario">Usuario</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="estado">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="administrar_usuarios.php" method="post">
                <div class="modal-body">
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    <div class="form-group">
                        <label for="email_edit">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email_edit" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre_edit">Nombre</label>
                        <input type="text" class="form-control" id="nombre_edit" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="rol_edit">Rol</label>
                        <select class="form-control" id="rol_edit" name="rol">
                            <option value="Administrador">Administrador</option>
                            <option value="Usuario">Usuario</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado_edit">Estado</label>
                        <select class="form-control" id="estado_edit" name="estado">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Rellenar el modal de edición con los datos del usuario seleccionado
    document.addEventListener('DOMContentLoaded', function() {
        var editUserModal = document.getElementById('editUserModal');
        editUserModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var email = button.getAttribute('data-email');
            var nombre = button.getAttribute('data-nombre');
            var rol = button.getAttribute('data-rol');
            var estado = button.getAttribute('data-estado');

            document.getElementById('id_usuario').value = id;
            document.getElementById('email_edit').value = email;
            document.getElementById('nombre_edit').value = nombre;
            document.getElementById('rol_edit').value = rol;
            document.getElementById('estado_edit').value = estado;
        });
    });
</script>

<?php
include('footer.php');  // Incluir el footer
?>
