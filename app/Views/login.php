<!--Este es el formulario de inicio de sesión-->
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Login</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel='stylesheet' href='<?= base_url('assets/css/estilos.css') ?>'>
</head>
<body class="body-formulario">

<div class="formulario">
    <h1>Iniciar Sesión</h1>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="mensaje-error">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('exito')): ?>
        <div class="mensaje-exito">
            <?= session()->getFlashdata('exito') ?>
        </div>
    <?php endif; ?>
    
    <form action='<?= base_url('login/procesar') ?>' method='post'>
        <label for="email">Correo Electrónico:</label>
        <input type='email' name='email' id='email' required>
        
        <div class="password-container">
            <label for="password">Contraseña:</label>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword()"></i>
            <input type='password' name='password' id='password' maxlength="6" placeholder="Ingrese 6 caracteres numéricos" required>
        </div>

        <button type='submit'>Iniciar Sesión</button>
    </form>
    <p>¿No tienes una cuenta? <a href="<?= base_url('registro') ?>">Regístrate aquí</a></p>
</div>

<script>
function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.querySelector(".toggle-password");
    const isPassword = input.type === "password";
    input.type = isPassword ? "text" : "password";
    icon.classList.toggle("fa-eye-slash");
    icon.classList.toggle("fa-eye");
}
</script>
</body>
</html>