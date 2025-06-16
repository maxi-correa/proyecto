<!--Este es el formulario de recuperar contraseña-->
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Recuperar Contraseña</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel='stylesheet' href='<?= base_url('assets/css/estilos.css') ?>'>
</head>
<body>
<div class="body-formulario">
    <main class="main-content">
    <div class="formulario">
        <h1>Recuperar Contraseña</h1>
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

    <form action="<?= site_url('recuperar/procesar') ?>" method="post">
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" value="<?= old('email') ?>" id="email">
        <button type="submit">Enviar e-mail</button>
        <br><br>
        <button type="button"  class="boton-volver" onclick="window.location.href='<?= base_url('login') ?>'">Volver</button>
    </form>
    </div>
    </main>

    <?= view('capas/pie') ?>
</div>

</body>
</html>