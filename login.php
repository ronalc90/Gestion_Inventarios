<?php
include('header.php');  // Incluye el header que maneja la sesión
require_once 'config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Validar reCAPTCHA
    $secretKey = '6Lc27XoqAAAAAI1DJxNXWx05SF3ZXeM-Q0T-62YN'; // Clave secreta de reCAPTCHA
    $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';

    $response = file_get_contents($recaptchaUrl . '?secret=' . $secretKey . '&response=' . $recaptchaResponse);
    $responseKeys = json_decode($response, true);

    if (!$responseKeys["success"]) {
        $message = 'Por favor, completa el reCAPTCHA.';
    } else {
        // Comprobar si el usuario existe con el email proporcionado
        $sql = "SELECT id_usuario, correo_electronico, contrasena, rol FROM Usuarios WHERE correo_electronico = '$email' AND estado = 'Activo'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verificar que la contraseña proporcionada sea correcta
            if (password_verify($password, $user['contrasena'])) {
                // Iniciar sesión y guardar datos necesarios en variables de sesión
                session_start();
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['username'] = $user['correo_electronico'];
                $_SESSION['user_role'] = $user['rol'];

                // Redireccionar al usuario a la página de inicio
                header("location: index.php");
                exit;
            } else {
                $message = 'La contraseña es incorrecta.';
            }
        } else {
            $message = 'No se encontró una cuenta con ese correo electrónico.';
        }
    }
}
?>

<link rel="stylesheet" href="styles/login.css">
<script src="https://www.google.com/recaptcha/api.js" async defer></script> <!-- reCAPTCHA script -->

<div class="container">
    <h2>Iniciar Sesión</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?php echo $message; ?></div>
    <?php endif; ?>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <!-- reCAPTCHA Widget -->
        <div class="g-recaptcha" data-sitekey="6Lc27XoqAAAAAEm_-07HK2NeJWo4tcNw7klZbbuK"></div> <!-- Usa la clave de sitio correcta -->

        <button type="submit">Iniciar Sesión</button>
    </form>
</div>

<?php
include('footer.php');  // Incluye el footer
?>
