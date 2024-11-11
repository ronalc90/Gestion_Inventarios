<?php
include('header.php');  // Incluir el header con la barra de navegación
require_once 'config.php';  // Incluir la configuración de la base de datos

// Verificar si el usuario tiene un rol específico
$user_role = $_SESSION['user_role'] ?? null;

// Definir permisos según el rol
$can_view_details = ($user_role == 'Empleado' || $user_role == 'Administrador');
$can_add_sales = ($user_role == 'Empleado' || $user_role == 'Administrador');
$can_manage_sales = ($user_role == 'Administrador');  // Solo el administrador puede eliminar ventas

// Eliminar venta si se tiene permisos de administrador
if (isset($_GET['delete']) && $can_manage_sales) {
    $sale_id = intval($_GET['delete']);
    $sql = "DELETE FROM Ventas WHERE id_venta = $sale_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Venta eliminada correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar la venta: " . $conn->error . "</div>";
    }
}

// Crear nueva venta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $can_add_sales) {
    $id_cliente = $_POST['id_cliente'];
    $monto_total = $_POST['monto_total'];
    $fecha_venta = date('Y-m-d H:i:s');  // Fecha actual

    // Crear la venta
    $sql_insert = "INSERT INTO Ventas (id_cliente, fecha_venta, monto_total) VALUES ('$id_cliente', '$fecha_venta', '$monto_total')";
    if ($conn->query($sql_insert) === TRUE) {
        $venta_id = $conn->insert_id;  // Obtener el ID de la venta creada
        echo "<div class='alert alert-success'>Venta registrada correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al registrar la venta: " . $conn->error . "</div>";
    }
}

// Listar ventas
$sql = "SELECT V.id_venta, U.nombre AS cliente, V.fecha_venta, V.monto_total FROM Ventas V JOIN Usuarios U ON V.id_cliente = U.id_usuario";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <h2>Gestión de Ventas</h2>
    
    <?php if ($can_add_sales): ?>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createSaleModal">Registrar Venta</button>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Venta</th>
                <th>Cliente</th>
                <th>Fecha de Venta</th>
                <th>Monto Total</th>
                <?php if ($can_view_details): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_venta']; ?></td>
                        <td><?php echo $row['cliente']; ?></td>
                        <td><?php echo $row['fecha_venta']; ?></td>
                        <td><?php echo $row['monto_total']; ?></td>
                        <?php if ($can_view_details): ?>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewDetailsModal" data-id="<?php echo $row['id_venta']; ?>">Ver Detalles</button>
                                
                                <?php if ($can_manage_sales): ?>
                                    <a href="gestion_ventas.php?delete=<?php echo $row['id_venta']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta venta?');">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo $can_view_details ? 5 : 4; ?>" class="text-center">No se encontraron ventas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para registrar nueva venta -->
<?php if ($can_add_sales): ?>
<div class="modal fade" id="createSaleModal" tabindex="-1" aria-labelledby="createSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSaleModalLabel">Registrar Nueva Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="gestion_ventas.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="id_cliente">Cliente</label>
                        <select class="form-control" id="id_cliente" name="id_cliente" required>
                            <?php
                            $clientes_sql = "SELECT id_usuario, nombre FROM Usuarios WHERE rol = 'Usuario'";
                            $clientes_result = $conn->query($clientes_sql);
                            while ($cliente = $clientes_result->fetch_assoc()) {
                                echo "<option value='{$cliente['id_usuario']}'>{$cliente['nombre']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="monto_total">Monto Total</label>
                        <input type="number" step="0.01" class="form-control" id="monto_total" name="monto_total" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Registrar Venta</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal para ver detalles de la venta -->
<?php if ($can_view_details): ?>
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Detalles de la Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="saleDetailsContent"></div> <!-- Contenedor para los detalles de la venta -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    // Rellenar el modal con los detalles de la venta seleccionada
    document.addEventListener('DOMContentLoaded', function() {
        var viewDetailsModal = document.getElementById('viewDetailsModal');
        viewDetailsModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var saleId = button.getAttribute('data-id');
            
            // Realizar una petición AJAX para obtener los detalles de la venta
            fetch(`get_sale_details.php?id_venta=${saleId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('saleDetailsContent').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>

<?php
include('footer.php');  // Incluir el footer
?>
