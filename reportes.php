<?php
include('header.php');  // Incluir el header con la barra de navegación
require_once 'config.php';  // Incluir la configuración de la base de datos

// Verificar el rol del usuario
$user_role = $_SESSION['user_role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

// Definir permisos según el rol
$can_view_all_reports = ($user_role == 'Empleado' || $user_role == 'Administrador');
$can_delete_reports = ($user_role == 'Administrador');

// Eliminar reporte si se tiene permisos de administrador
if (isset($_GET['delete']) && $can_delete_reports) {
    $report_id = intval($_GET['delete']);
    $sql = "DELETE FROM Reportes WHERE id_reporte = $report_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Reporte eliminado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar el reporte: " . $conn->error . "</div>";
    }
}

// Consultar los reportes
$report_query = "SELECT R.id_reporte, R.tipo_reporte, R.fecha_generacion, R.formato_reporte, U.nombre AS usuario_genero 
                 FROM Reportes R 
                 JOIN Usuarios U ON R.usuario_genero = U.id_usuario";
$report_query .= $can_view_all_reports ? "" : " WHERE R.usuario_genero = $user_id";  // Mostrar solo reportes propios para Usuarios normales
$report_result = $conn->query($report_query);
?>

<div class="container mt-5">
    <h2>Gestión de Reportes</h2>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Reporte</th>
                <th>Tipo de Reporte</th>
                <th>Fecha de Generación</th>
                <th>Formato</th>
                <th>Usuario Generó</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($report_result->num_rows > 0): ?>
                <?php while ($row = $report_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_reporte']; ?></td>
                        <td><?php echo $row['tipo_reporte']; ?></td>
                        <td><?php echo $row['fecha_generacion']; ?></td>
                        <td><?php echo $row['formato_reporte']; ?></td>
                        <td><?php echo $row['usuario_genero']; ?></td>
                        <td>
                            <!-- Botón para descargar reporte -->
                            <a href="descargar_reporte.php?id_reporte=<?php echo $row['id_reporte']; ?>" class="btn btn-primary btn-sm">Descargar</a>
                            
                            <!-- Opciones adicionales para Administradores -->
                            <?php if ($can_delete_reports): ?>
                                <a href="reportes.php?delete=<?php echo $row['id_reporte']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este reporte?');">Eliminar</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No se encontraron reportes.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include('footer.php');  // Incluir el footer
?>
