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
    <style>
        /* Fondo de princesa para login */
        html, body {
            background-image: url('<?= base_url("assets/img/fondo_princesa.jpg") ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .body-formulario {
            background-color: rgba(242, 242, 242, 0.7); /* Decolora el fondo */
        }
        .main-content {
            background-color: rgba(242, 242, 242, 0); /* Fondo transparente */
        }
    </style>
</head>
<body>
<div class="body-formulario">
    <main class="main-content">
    <div class="formulario formulario-menu">
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
    <?php if (isset($mensaje)): ?>
        <div class="mensaje-exito">
            <?= esc($mensaje) ?>
        </div>
    <?php endif; ?>

    <form action='<?= base_url('login/procesar') ?>' method='post'>
        <label for="nombre">Nombre de Usuario:</label>
        <input type='text' name='nombre' id='nombre' value="<?= old('nombre') ?>">

        <div class="password-container">
            <label for="password">Contraseña:</label>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword()"></i>
            <input type='password' name='password' id='password' maxlength="6" placeholder="Ingrese 6 caracteres numéricos">
        </div>

        <button type='submit'>Iniciar Sesión</button>
    </form>
    <p>¿No tienes una cuenta? <a href="<?= base_url('registro') ?>">Regístrate aquí</a></p>
    <p>¿Olvidaste tu contraseña? <a href="<?= base_url('recuperar') ?>">Recuperar</a></p>
    </div>
    </main>

    <?= view('capas/pie') ?>
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