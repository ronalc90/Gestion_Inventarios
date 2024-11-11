<?php
include('header.php');  // Incluir el header con la barra de navegación
require_once 'config.php';  // Incluir la configuración de la base de datos

// Verificar el rol del usuario
$user_role = $_SESSION['user_role'] ?? null;

// Definir permisos según el rol
$can_view_details = ($user_role == 'Empleado' || $user_role == 'Administrador');
$can_add_purchases = ($user_role == 'Empleado' || $user_role == 'Administrador');
$can_manage_purchases = ($user_role == 'Administrador');  // Solo el administrador puede eliminar compras

// Eliminar compra si se tiene permisos de administrador
if (isset($_GET['delete']) && $can_manage_purchases) {
    $purchase_id = intval($_GET['delete']);
    $sql = "DELETE FROM Compras WHERE id_compra = $purchase_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Compra eliminada correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar la compra: " . $conn->error . "</div>";
    }
}

// Registrar nueva compra
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $can_add_purchases) {
    $id_proveedor = $_POST['id_proveedor'];
    $monto_total = $_POST['monto_total'];
    $fecha_compra = date('Y-m-d H:i:s');  // Fecha actual

    // Crear la compra
    $sql_insert = "INSERT INTO Compras (id_proveedor, fecha_compra, monto_total) VALUES ('$id_proveedor', '$fecha_compra', '$monto_total')";
    if ($conn->query($sql_insert) === TRUE) {
        $compra_id = $conn->insert_id;  // Obtener el ID de la compra creada
        echo "<div class='alert alert-success'>Compra registrada correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al registrar la compra: " . $conn->error . "</div>";
    }
}

// Listar compras
$sql = "SELECT C.id_compra, P.nombre_proveedor AS proveedor, C.fecha_compra, C.monto_total FROM Compras C JOIN Proveedores P ON C.id_proveedor = P.id_proveedor";
$result = $conn->query($sql);
?>

<div class="container mt-5">
    <h2>Gestión de Compras</h2>
    
    <?php if ($can_add_purchases): ?>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createPurchaseModal">Registrar Compra</button>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Compra</th>
                <th>Proveedor</th>
                <th>Fecha de Compra</th>
                <?php if ($can_view_details): ?>
                    <th>Monto Total</th>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_compra']; ?></td>
                        <td><?php echo $row['proveedor']; ?></td>
                        <td><?php echo $row['fecha_compra']; ?></td>
                        <?php if ($can_view_details): ?>
                            <td><?php echo $row['monto_total']; ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewDetailsModal" data-id="<?php echo $row['id_compra']; ?>">Ver Detalles</button>
                                
                                <?php if ($can_manage_purchases): ?>
                                    <a href="gestion_compras.php?delete=<?php echo $row['id_compra']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta compra?');">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo $can_view_details ? 5 : 3; ?>" class="text-center">No se encontraron compras registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para registrar nueva compra -->
<?php if ($can_add_purchases): ?>
<div class="modal fade" id="createPurchaseModal" tabindex="-1" aria-labelledby="createPurchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPurchaseModalLabel">Registrar Nueva Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="gestion_compras.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="id_proveedor">Proveedor</label>
                        <select class="form-control" id="id_proveedor" name="id_proveedor" required>
                            <?php
                            $proveedores_sql = "SELECT id_proveedor, nombre_proveedor FROM Proveedores";
                            $proveedores_result = $conn->query($proveedores_sql);
                            while ($proveedor = $proveedores_result->fetch_assoc()) {
                                echo "<option value='{$proveedor['id_proveedor']}'>{$proveedor['nombre_proveedor']}</option>";
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
                    <button type="submit" class="btn btn-primary">Registrar Compra</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal para ver detalles de la compra -->
<?php if ($can_view_details): ?>
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Detalles de la Compra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="purchaseDetailsContent"></div> <!-- Contenedor para los detalles de la compra -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    // Rellenar el modal con los detalles de la compra seleccionada
    document.addEventListener('DOMContentLoaded', function() {
        var viewDetailsModal = document.getElementById('viewDetailsModal');
        viewDetailsModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var purchaseId = button.getAttribute('data-id');
            
            // Realizar una petición AJAX para obtener los detalles de la compra
            fetch(`get_purchase_details.php?id_compra=${purchaseId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('purchaseDetailsContent').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>

<?php
include('footer.php');  // Incluir el footer
?>
