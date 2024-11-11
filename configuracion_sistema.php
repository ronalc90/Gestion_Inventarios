<?php
include('header.php');  // Incluir el header con la barra de navegación
require_once 'config.php';  // Incluir la configuración de la base de datos

// Verificar si el usuario tiene permisos de administrador
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'Administrador') {
    header("Location: index.php");  // Redirigir a la página principal si no tiene permisos
    exit;
}

// Manejo de configuración del sistema
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tema_color = $_POST['tema_color'];
    $tamano_fuente = $_POST['tamano_fuente'];
    $modo_oscuro = isset($_POST['modo_oscuro']) ? 1 : 0;

    // Verificar si existe una configuración para el usuario
    $sql_check = "SELECT * FROM Configuracion_Interfaz WHERE id_usuario = " . $_SESSION['user_id'];
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // Actualizar configuración existente
        $sql_update = "UPDATE Configuracion_Interfaz 
                       SET tema_color = '$tema_color', tamano_fuente = '$tamano_fuente', modo_oscuro = '$modo_oscuro' 
                       WHERE id_usuario = " . $_SESSION['user_id'];
        $conn->query($sql_update);
        echo "<div class='alert alert-success'>Configuración guardada correctamente.</div>";
    } else {
        // Insertar nueva configuración si no existe
        $sql_insert = "INSERT INTO Configuracion_Interfaz (id_usuario, tema_color, tamano_fuente, modo_oscuro) 
                       VALUES (" . $_SESSION['user_id'] . ", '$tema_color', '$tamano_fuente', '$modo_oscuro')";
        $conn->query($sql_insert);
        echo "<div class='alert alert-success'>Configuración guardada correctamente.</div>";
    }
}

// Consultar configuración actual del sistema
$sql = "SELECT tema_color, tamano_fuente, modo_oscuro FROM Configuracion_Interfaz WHERE id_usuario = " . $_SESSION['user_id'];
$result = $conn->query($sql);
$config = $result->fetch_assoc();

// Valores predeterminados si no hay configuración existente
$tema_color = isset($config['tema_color']) ? $config['tema_color'] : 'claro';
$tamano_fuente = isset($config['tamano_fuente']) ? $config['tamano_fuente'] : 'mediano';
$modo_oscuro = isset($config['modo_oscuro']) ? $config['modo_oscuro'] : 0;
?>

<div class="container mt-5">
    <h2>Configuración del Sistema</h2>
    <form action="configuracion_sistema.php" method="post">
        <div class="form-group">
            <label for="tema_color">Tema de Color:</label>
            <select class="form-control" id="tema_color" name="tema_color">
                <option value="claro" <?php if ($tema_color == 'claro') echo 'selected'; ?>>Claro</option>
                <option value="oscuro" <?php if ($tema_color == 'oscuro') echo 'selected'; ?>>Oscuro</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="tamano_fuente">Tamaño de Fuente:</label>
            <select class="form-control" id="tamano_fuente" name="tamano_fuente">
                <option value="pequeño" <?php if ($tamano_fuente == 'pequeño') echo 'selected'; ?>>Pequeño</option>
                <option value="mediano" <?php if ($tamano_fuente == 'mediano') echo 'selected'; ?>>Mediano</option>
                <option value="grande" <?php if ($tamano_fuente == 'grande') echo 'selected'; ?>>Grande</option>
            </select>
        </div>
        
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="modo_oscuro" name="modo_oscuro" <?php if ($modo_oscuro) echo 'checked'; ?>>
            <label class="form-check-label" for="modo_oscuro">Activar Modo Oscuro</label>
        </div>
        
        <button type="submit" class="btn btn-primary mt-3">Guardar Configuración</button>
    </form>
</div>

<?php include('footer.php');  // Incluir el footer ?>
