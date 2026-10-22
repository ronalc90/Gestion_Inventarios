# Gestion_Inventarios
🌟 Sistema de Gestión Ultra-Premium: La Revolución en la Eficiencia Empresarial 🌟 ¡Bienvenido al futuro de la gestión empresarial! Este Sistema de Gestión es una plataforma robusta y poderosa diseñada para transformar la manera en que tu empresa opera, agilizando procesos y garantizando una experiencia de usuario impecable y elegante.

## Configuración

- **Base de datos:** crea `config.php` con la conexión `$conn` (no se sube al repositorio; está en `.gitignore`). El esquema está en `bd.txt`.
- **reCAPTCHA:** la clave secreta se lee de la variable de entorno `RECAPTCHA_SECRET_KEY` (por ejemplo, `SetEnv RECAPTCHA_SECRET_KEY ...` en la configuración de Apache). Nunca la escribas en el código. La clave de sitio de `login.php` y `register.php` es pública.
