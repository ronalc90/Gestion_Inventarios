<?php
include('header.php');  // Incluye el header modificado
require_once 'config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $passwordConfirm = $_POST['passwordConfirm'];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Validar reCAPTCHA
    $secretKey = '6Lc27XoqAAAAAI1DJxNXWx05SF3ZXeM-Q0T-62YN'; // Clave secreta de reCAPTCHA
    $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';

    $response = file_get_contents($recaptchaUrl . '?secret=' . $secretKey . '&response=' . $recaptchaResponse);
    $responseKeys = json_decode($response, true);

    if (!$responseKeys["success"]) {
        $message = 'Por favor, completa el reCAPTCHA.';
    } elseif ($password !== $passwordConfirm) {
        $message = 'Las contraseñas no coinciden.';
    } else {
        // Comprobar si el email ya está registrado
        $result = $conn->query("SELECT id_usuario FROM Usuarios WHERE correo_electronico = '$email'");
        if ($result->num_rows > 0) {
            $message = 'El correo electrónico ya está registrado.';
        } else {
            // Insertar el nuevo usuario
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Usuarios (correo_electronico, contrasena, rol, estado) VALUES ('$email', '$passwordHash', 'Usuario', 'Activo')";

            if ($conn->query($sql) === TRUE) {
                $message = 'Registro completado exitosamente. Puede <a href="login.php">iniciar sesión</a>.';
            } else {
                $message = 'Error: ' . $conn->error;
            }
        }
    }
}
?>

<link rel="stylesheet" href="styles/register.css">
<script src="https://www.google.com/recaptcha/api.js" async defer></script> <!-- reCAPTCHA script -->

<div class="container">
    <h2>Registrarse</h2>
    <?php if ($message != ''): ?>
        <div class="alert <?php echo strpos($message, 'completado') !== false ? 'alert-success' : 'alert-danger'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="register.php" method="post">
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" minlength="8" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="passwordConfirm">Confirmar Contraseña:</label>
            <input type="password" minlength="8" id="passwordConfirm" name="passwordConfirm" required>
        </div>
        
        <!-- reCAPTCHA Widget -->
        <div class="g-recaptcha" data-sitekey="6Lc27XoqAAAAAEm_-07HK2NeJWo4tcNw7klZbbuK"></div> 

        <button type="submit">Registrarse</button>
    </form>
</div>

<?php
include('footer.php');
?>
