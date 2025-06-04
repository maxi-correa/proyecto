<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>
<body class="body-formulario">

<div class="formulario">
    <h1>Registro de Usuario</h1>

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

    <form action="<?= base_url('registro/procesarRegistro') ?>" method="post">

        <label for="nombre">Nombre*:</label>
        <input type="text" name="nombre" id="nombre" value="<?= old('nombre') ?>">
    
        <label for="apellido">Apellido*:</label>
        <input type="text" name="apellido" id="apellido" value="<?= old('apellido') ?>">

        <label for="email">Correo electrónico*:</label>
        <input type="email" name="email" id="email" value="<?= old('email') ?>">

        <div class="password-container">
            <label for="password">Contraseña*:</label>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('password', this)"></i>
            <input type="password" name="password" id="password" maxlength="6" placeholder="Ingrese 6 caracteres numéricos">
        </div>
        <div class="password-container">
            <label for="confirmar_password">Confirmar contraseña*:</label>
            <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('confirmar_password', this)"></i>
            <input type="password" name="confirmar_password" id="confirmar_password" maxlength="6" placeholder="Repita la contraseña">
        </div>
        <p class="nota">(*): Campos obligatorios</p>
        
        <button type="submit">Registrarse</button>
        <br><br>
        <button type="button"  class="boton-volver" onclick="window.location.href='<?= base_url('login') ?>'">Volver</button>
    </form>
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
