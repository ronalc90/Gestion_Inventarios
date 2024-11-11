<?php
include('header.php');  // Incluir el header con la barra de navegación
require_once 'config.php';  // Incluir la configuración de la base de datos

// Verificar el rol del usuario
$user_role = $_SESSION['user_role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// Definir permisos según el rol
$can_report_incidents = ($user_role == 'Usuario' || $user_role == 'Empleado' || $user_role == 'Administrador');
$can_manage_tasks = ($user_role == 'Empleado' || $user_role == 'Administrador');
$can_assign_tasks = ($user_role == 'Administrador');

// Consultar incidentes
$incident_query = "SELECT I.id_incidente, I.descripcion_incidente, I.fecha_reporte, I.prioridad, I.estado_incidente, U.nombre AS usuario_reporta 
                   FROM Incidentes_Soporte I 
                   JOIN Usuarios U ON I.usuario_reporta = U.id_usuario";
$incident_query .= $user_role == 'Usuario' ? " WHERE I.usuario_reporta = $user_id" : "";  // Solo mostrar incidentes propios para Usuarios
$incident_result = $conn->query($incident_query);

// Consultar tareas de mantenimiento
$task_query = "SELECT T.id_tarea, T.descripcion_tarea, T.fecha_programada, T.estado_tarea, U.nombre AS usuario_responsable 
               FROM Tareas_Mantenimiento T 
               JOIN Usuarios U ON T.usuario_responsable = U.id_usuario";
$task_result = $conn->query($task_query);
?>

<div class="container mt-5">
    <h2>Soporte y Mantenimiento</h2>

    <!-- Sección de Incidentes de Soporte -->
    <h3>Incidentes de Soporte</h3>
    <?php if ($can_report_incidents): ?>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#reportIncidentModal">Reportar Incidente</button>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Incidente</th>
                <th>Descripción</th>
                <th>Fecha de Reporte</th>
                <th>Prioridad</th>
                <th>Estado</th>
                <th>Usuario Reporta</th>
                <?php if ($can_manage_tasks): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($incident_result->num_rows > 0): ?>
                <?php while ($row = $incident_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_incidente']; ?></td>
                        <td><?php echo $row['descripcion_incidente']; ?></td>
                        <td><?php echo $row['fecha_reporte']; ?></td>
                        <td><?php echo $row['prioridad']; ?></td>
                        <td><?php echo $row['estado_incidente']; ?></td>
                        <td><?php echo $row['usuario_reporta']; ?></td>
                        <?php if ($can_manage_tasks): ?>
                            <td>
                                <?php if ($can_assign_tasks): ?>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editIncidentModal" data-id="<?php echo $row['id_incidente']; ?>">Asignar</button>
                                <?php endif; ?>
                                <?php if ($can_assign_tasks): ?>
                                    <a href="soporte_mantenimiento.php?delete_incident=<?php echo $row['id_incidente']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este incidente?');">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No se encontraron incidentes de soporte.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Sección de Tareas de Mantenimiento -->
    <h3>Tareas de Mantenimiento</h3>
    <?php if ($can_manage_tasks): ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTaskModal">Agregar Tarea de Mantenimiento</button>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Tarea</th>
                <th>Descripción</th>
                <th>Fecha Programada</th>
                <th>Estado</th>
                <th>Responsable</th>
                <?php if ($can_manage_tasks): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($task_result->num_rows > 0): ?>
                <?php while ($row = $task_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_tarea']; ?></td>
                        <td><?php echo $row['descripcion_tarea']; ?></td>
                        <td><?php echo $row['fecha_programada']; ?></td>
                        <td><?php echo $row['estado_tarea']; ?></td>
                        <td><?php echo $row['usuario_responsable']; ?></td>
                        <?php if ($can_manage_tasks): ?>
                            <td>
                                <?php if ($can_assign_tasks): ?>
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editTaskModal" data-id="<?php echo $row['id_tarea']; ?>">Editar</button>
                                <?php endif; ?>
                                <?php if ($can_assign_tasks): ?>
                                    <a href="soporte_mantenimiento.php?delete_task=<?php echo $row['id_tarea']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta tarea?');">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No se encontraron tareas de mantenimiento.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para reportar incidente -->
<?php if ($can_report_incidents): ?>
<div class="modal fade" id="reportIncidentModal" tabindex="-1" aria-labelledby="reportIncidentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportIncidentModalLabel">Reportar Incidente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="soporte_mantenimiento.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="descripcion_incidente">Descripción</label>
                        <textarea class="form-control" id="descripcion_incidente" name="descripcion_incidente" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="prioridad">Prioridad</label>
                        <select class="form-control" id="prioridad" name="prioridad" required>
                            <option value="Alta">Alta</option>
                            <option value="Media">Media</option>
                            <option value="Baja">Baja</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Reportar Incidente</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal para agregar tarea de mantenimiento -->
<?php if ($can_manage_tasks): ?>
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskModalLabel">Agregar Tarea de Mantenimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="soporte_mantenimiento.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="descripcion_tarea">Descripción de la Tarea</label>
                        <textarea class="form-control" id="descripcion_tarea" name="descripcion_tarea" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="fecha_programada">Fecha Programada</label>
                        <input type="datetime-local" class="form-control" id="fecha_programada" name="fecha_programada" required>
                    </div>
                    <div class="form-group">
                        <label for="usuario_responsable">Responsable</label>
                        <select class="form-control" id="usuario_responsable" name="usuario_responsable" required>
                            <?php
                            $usuarios_sql = "SELECT id_usuario, nombre FROM Usuarios WHERE rol IN ('Empleado', 'Administrador')";
                            $usuarios_result = $conn->query($usuarios_sql);
                            while ($usuario = $usuarios_result->fetch_assoc()) {
                                echo "<option value='{$usuario['id_usuario']}'>{$usuario['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Agregar Tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
include('footer.php');  // Incluir el footer
?>
