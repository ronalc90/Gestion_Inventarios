<?php
include('header.php');  // Incluir el header con la barra de navegación
require_once 'config.php';  // Incluir la configuración de la base de datos

// Verificar si el usuario tiene permisos de administrador
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'Administrador') {
    header("Location: index.php");  // Redirigir a la página principal si no tiene permisos
    exit;
}

// Eliminar integración si se recibe una solicitud de eliminación
if (isset($_GET['delete'])) {
    $integration_id = intval($_GET['delete']);
    $sql = "DELETE FROM Integracion_Externa WHERE id_integracion = $integration_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Integración eliminada correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar la integración: " . $conn->error . "</div>";
    }
}

// Guardar o actualizar integración externa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_integracion = $_POST['nombre_integracion'];
    $api_key = $_POST['api_key'];
    $estado_integracion = $_POST['estado_integracion'];
    $frecuencia_sincronizacion = $_POST['frecuencia_sincronizacion'];
    $ultimo_fallo = !empty($_POST['ultimo_fallo']) ? $_POST['ultimo_fallo'] : null;

    // Si se recibe un id_integracion, actualizar; de lo contrario, crear nuevo
    if (!empty($_POST['id_integracion'])) {
        $id_integracion = intval($_POST['id_integracion']);
        $sql_update = "UPDATE Integracion_Externa 
                       SET nombre_integracion = '$nombre_integracion', api_key = '$api_key', 
                           estado_integracion = '$estado_integracion', frecuencia_sincronizacion = '$frecuencia_sincronizacion',
                           ultimo_fallo = '$ultimo_fallo'
                       WHERE id_integracion = $id_integracion";
        if ($conn->query($sql_update) === TRUE) {
            echo "<div class='alert alert-success'>Integración actualizada correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al actualizar la integración: " . $conn->error . "</div>";
        }
    } else {
        $sql_insert = "INSERT INTO Integracion_Externa (nombre_integracion, api_key, estado_integracion, frecuencia_sincronizacion, ultimo_fallo) 
                       VALUES ('$nombre_integracion', '$api_key', '$estado_integracion', '$frecuencia_sincronizacion', '$ultimo_fallo')";
        if ($conn->query($sql_insert) === TRUE) {
            echo "<div class='alert alert-success'>Integración creada correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al crear la integración: " . $conn->error . "</div>";
        }
    }
}

// Consultar integraciones externas existentes
$sql = "SELECT * FROM Integracion_Externa";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <h2>Integraciones Externas</h2>
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createIntegrationModal">Añadir Integración</button>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>API Key</th>
                <th>Estado</th>
                <th>Frecuencia de Sincronización</th>
                <th>Último Fallo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_integracion']; ?></td>
                        <td><?php echo $row['nombre_integracion']; ?></td>
                        <td><?php echo $row['api_key']; ?></td>
                        <td><?php echo $row['estado_integracion']; ?></td>
                        <td><?php echo $row['frecuencia_sincronizacion']; ?></td>
                        <td><?php echo $row['ultimo_fallo']; ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editIntegrationModal"
                                    data-id="<?php echo $row['id_integracion']; ?>"
                                    data-nombre="<?php echo $row['nombre_integracion']; ?>"
                                    data-api="<?php echo $row['api_key']; ?>"
                                    data-estado="<?php echo $row['estado_integracion']; ?>"
                                    data-frecuencia="<?php echo $row['frecuencia_sincronizacion']; ?>"
                                    data-fallo="<?php echo $row['ultimo_fallo']; ?>">Editar</button>
                            <a href="integraciones_externas.php?delete=<?php echo $row['id_integracion']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta integración?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No se encontraron integraciones externas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para crear integración -->
<div class="modal fade" id="createIntegrationModal" tabindex="-1" aria-labelledby="createIntegrationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createIntegrationModalLabel">Añadir Integración</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="integraciones_externas.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre_integracion">Nombre de la Integración</label>
                        <input type="text" class="form-control" id="nombre_integracion" name="nombre_integracion" required>
                    </div>
                    <div class="form-group">
                        <label for="api_key">API Key</label>
                        <input type="text" class="form-control" id="api_key" name="api_key" required>
                    </div>
                    <div class="form-group">
                        <label for="estado_integracion">Estado</label>
                        <select class="form-control" id="estado_integracion" name="estado_integracion">
                            <option value="Activa">Activa</option>
                            <option value="Inactiva">Inactiva</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="frecuencia_sincronizacion">Frecuencia de Sincronización</label>
                        <input type="text" class="form-control" id="frecuencia_sincronizacion" name="frecuencia_sincronizacion" required>
                    </div>
                    <div class="form-group">
                        <label for="ultimo_fallo">Último Fallo (Opcional)</label>
                        <input type="datetime-local" class="form-control" id="ultimo_fallo" name="ultimo_fallo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Integración</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar integración -->
<div class="modal fade" id="editIntegrationModal" tabindex="-1" aria-labelledby="editIntegrationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editIntegrationModalLabel">Editar Integración</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="integraciones_externas.php" method="post">
                <input type="hidden" id="id_integracion" name="id_integracion">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre_integracion_edit">Nombre de la Integración</label>
                        <input type="text" class="form-control" id="nombre_integracion_edit" name="nombre_integracion" required>
                    </div>
                    <div class="form-group">
                        <label for="api_key_edit">API Key</label>
                        <input type="text" class="form-control" id="api_key_edit" name="api_key" required>
                    </div>
                    <div class="form-group">
                        <label for="estado_integracion_edit">Estado</label>
                        <select class="form-control" id="estado_integracion_edit" name="estado_integracion">
                            <option value="Activa">Activa</option>
                            <option value="Inactiva">Inactiva</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="frecuencia_sincronizacion_edit">Frecuencia de Sincronización</label>
                        <input type="text" class="form-control" id="frecuencia_sincronizacion_edit" name="frecuencia_sincronizacion" required>
                    </div>
                    <div class="form-group">
                        <label for="ultimo_fallo_edit">Último Fallo (Opcional)</label>
                        <input type="datetime-local" class="form-control" id="ultimo_fallo_edit" name="ultimo_fallo">
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
    // Script para rellenar el modal de edición con los datos del producto seleccionado
    document.addEventListener('DOMContentLoaded', function() {
        var editIntegrationModal = document.getElementById('editIntegrationModal');
        editIntegrationModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('id_integracion').value = button.getAttribute('data-id');
            document.getElementById('nombre_integracion_edit').value = button.getAttribute('data-nombre');
            document.getElementById('api_key_edit').value = button.getAttribute('data-api');
            document.getElementById('estado_integracion_edit').value = button.getAttribute('data-estado');
            document.getElementById('frecuencia_sincronizacion_edit').value = button.getAttribute('data-frecuencia');
            document.getElementById('ultimo_fallo_edit').value = button.getAttribute('data-fallo');
        });
    });
</script>

<?php include('footer.php');  // Incluir el footer ?>
