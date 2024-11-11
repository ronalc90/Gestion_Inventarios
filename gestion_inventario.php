<?php
include('header.php');  // Incluir el header con la barra de navegación
require_once 'config.php';  // Incluir la configuración de la base de datos

// Verificar si el usuario tiene permisos específicos
$user_role = $_SESSION['user_role'] ?? null;

// Definir permisos basados en el rol
$can_view_stock = ($user_role == 'Empleado' || $user_role == 'Administrador');
$can_edit_inventory = ($user_role == 'Empleado' || $user_role == 'Administrador');
$can_manage_inventory = ($user_role == 'Administrador');  // Solo Administrador puede eliminar o añadir

// Eliminar producto si se recibe una solicitud de eliminación y el usuario tiene permisos de administrador
if (isset($_GET['delete']) && $can_manage_inventory) {
    $product_id = intval($_GET['delete']);
    $sql = "DELETE FROM Inventario WHERE id_producto = $product_id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Producto eliminado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar el producto: " . $conn->error . "</div>";
    }
}

// Crear o editar producto si se tiene permisos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $can_edit_inventory) {
    $nombre_producto = $_POST['nombre_producto'];
    $cantidad_disponible = $_POST['cantidad_disponible'] ?? null;
    $stock_minimo = $_POST['stock_minimo'] ?? null;
    $fecha_ultima_actualizacion = date('Y-m-d H:i:s');  // Fecha actual

    // Editar producto
    if (isset($_POST['id_producto']) && !empty($_POST['id_producto'])) {
        $id_producto = intval($_POST['id_producto']);
        $sql_update = "UPDATE Inventario SET nombre_producto='$nombre_producto'";
        
        // Solo Administrador puede actualizar cantidad y stock mínimo
        if ($user_role == 'Administrador') {
            $sql_update .= ", cantidad_disponible='$cantidad_disponible', stock_minimo='$stock_minimo'";
        }
        $sql_update .= ", fecha_ultima_actualizacion='$fecha_ultima_actualizacion' WHERE id_producto=$id_producto";
        
        if ($conn->query($sql_update) === TRUE) {
            echo "<div class='alert alert-success'>Producto actualizado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al actualizar el producto: " . $conn->error . "</div>";
        }
    } 
    // Crear nuevo producto (solo para Administrador)
    elseif ($can_manage_inventory) {
        $sql_insert = "INSERT INTO Inventario (nombre_producto, cantidad_disponible, stock_minimo, fecha_ultima_actualizacion) VALUES ('$nombre_producto', '$cantidad_disponible', '$stock_minimo', '$fecha_ultima_actualizacion')";
        if ($conn->query($sql_insert) === TRUE) {
            echo "<div class='alert alert-success'>Producto creado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al crear el producto: " . $conn->error . "</div>";
        }
    }
}

// Listar productos del inventario
$sql = "SELECT id_producto, nombre_producto, cantidad_disponible, stock_minimo, fecha_ultima_actualizacion FROM Inventario";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario</title>
    <link rel="stylesheet" href="styles/gestion_inventario.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLR2yTY1F6WRwXDi8ODhph8a1GOeNfF5Xq0j7FpcUK" crossorigin="anonymous"></script>
</head>
<body>

<div class="container mt-5">
    <h2>Gestión de Inventario</h2>
    
    <?php if ($can_manage_inventory): ?>
        <button id="btnAddProduct" class="btn btn-success mb-3">Añadir Producto</button>
    <?php endif; ?>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Producto</th>
                <?php if ($can_view_stock): ?>
                    <th>Cantidad Disponible</th>
                    <th>Stock Mínimo</th>
                    <th>Fecha Última Actualización</th>
                <?php endif; ?>
                <?php if ($can_edit_inventory): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id_producto']; ?></td>
                        <td><?php echo $row['nombre_producto']; ?></td>
                        
                        <?php if ($can_view_stock): ?>
                            <td><?php echo $row['cantidad_disponible']; ?></td>
                            <td><?php echo $row['stock_minimo']; ?></td>
                            <td><?php echo $row['fecha_ultima_actualizacion']; ?></td>
                        <?php endif; ?>
                        
                        <?php if ($can_edit_inventory): ?>
                            <td>
                                <button class="btn btn-primary btn-sm btn-edit" 
                                        data-id="<?php echo $row['id_producto']; ?>" 
                                        data-nombre="<?php echo $row['nombre_producto']; ?>" 
                                        data-cantidad="<?php echo $row['cantidad_disponible']; ?>" 
                                        data-stock="<?php echo $row['stock_minimo']; ?>">
                                    Editar
                                </button>
                                
                                <?php if ($can_manage_inventory): ?>
                                    <a href="gestion_inventario.php?delete=<?php echo $row['id_producto']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="<?php echo $can_view_stock ? 6 : 2; ?>" class="text-center">No se encontraron productos en el inventario.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal para crear producto -->
<div class="modal" id="createProductModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Añadir Producto</h5>
                <button type="button" class="btn-close" onclick="$('#createProductModal').fadeOut();"></button>
            </div>
            <form action="gestion_inventario.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre_producto">Nombre del Producto</label>
                        <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required>
                    </div>
                    <div class="form-group">
                        <label for="cantidad_disponible">Cantidad Disponible</label>
                        <input type="number" class="form-control" id="cantidad_disponible" name="cantidad_disponible" required>
                    </div>
                    <div class="form-group">
                        <label for="stock_minimo">Stock Mínimo</label>
                        <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#createProductModal').fadeOut();">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Crear Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar producto -->
<div class="modal" id="editProductModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Producto</h5>
                <button type="button" class="btn-close" onclick="$('#editProductModal').fadeOut();"></button>
            </div>
            <form action="gestion_inventario.php" method="post">
                <div class="modal-body">
                    <input type="hidden" id="id_producto" name="id_producto">
                    <div class="form-group">
                        <label for="nombre_producto_edit">Nombre del Producto</label>
                        <input type="text" class="form-control" id="nombre_producto_edit" name="nombre_producto" required>
                    </div>
                    <div class="form-group">
                        <label for="cantidad_disponible_edit">Cantidad Disponible</label>
                        <input type="number" class="form-control" id="cantidad_disponible_edit" name="cantidad_disponible">
                    </div>
                    <div class="form-group">
                        <label for="stock_minimo_edit">Stock Mínimo</label>
                        <input type="number" class="form-control" id="stock_minimo_edit" name="stock_minimo">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="$('#editProductModal').fadeOut();">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Abrir modal de creación de producto
    $('#btnAddProduct').on('click', function() {
        $('#createProductModal').fadeIn();
    });

    // Abrir modal de edición de producto y cargar datos
    $('.btn-edit').on('click', function() {
        const productId = $(this).data('id');
        const productName = $(this).data('nombre');
        const productQuantity = $(this).data('cantidad');
        const productStock = $(this).data('stock');

        $('#id_producto').val(productId);
        $('#nombre_producto_edit').val(productName);
        $('#cantidad_disponible_edit').val(productQuantity);
        $('#stock_minimo_edit').val(productStock);

        $('#editProductModal').fadeIn();
    });
    
    // Cerrar modal al hacer clic fuera del contenido del modal
    $('.modal').on('click', function(e) {
        if ($(e.target).is('.modal')) {
            $(this).fadeOut();
        }
    });
});
</script>

<?php
include('footer.php');  // Incluir el footer
?>
</body>
</html>
