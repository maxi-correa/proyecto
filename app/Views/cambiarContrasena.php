<!--Este es el formulario de cambio de contraseña-->
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Cambiar Contraseña</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel='stylesheet' href='<?= base_url('assets/css/estilos.css') ?>'>
</head>
<body>
<div class="body-formulario">
    <main class="main-content">
    <div class="formulario">
        <h1>Cambiar Contraseña</h1>
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


    <form action='<?= base_url('cambiar_contrasena/procesar') ?>' method='post'>
        <div class="password-container">
            <label for="password_actual">Contraseña Actual:</label>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('password_actual', this)"></i>
            <input type='password' name='password_actual' id='password_actual' maxlength="6" placeholder="Ingrese su contraseña actual">
        </div>

        <div class="password-container">
            <label for="password_nueva">Nueva Contraseña:</label>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('password_nueva', this)"></i>
            <input type='password' name='password_nueva' id='password_nueva' maxlength="6" placeholder="Ingrese 6 caracteres numéricos">
        </div>

        <div class="password-container">
            <label for="password_confirmar">Confirmar Contraseña:</label>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('password_confirmar', this)"></i>
            <input type='password' name='password_confirmar' id='password_confirmar' maxlength="6" placeholder="Repita la nueva contraseña">
        </div>

        <button type='submit'>Cambiar Contraseña</button>
        <br><br>
        <button type="button" class="boton-volver" onclick="window.location.href='<?= base_url('/partida') ?>'">Volver</button>
    </form>
    </div>
    </main>

    <?= view('capas/pie') ?>
</div>
<script>
function togglePassword(fieldId, icon) {
    const input = document.getElementById(fieldId);
    const isPassword = input.type === "password";
    input.type = isPassword ? "text" : "password";
    icon.classList.toggle("fa-eye-slash");
    icon.classList.toggle("fa-eye");
}
</script>
</body>
</html>