<!--Página para elección de partida (nueva/existente)-->
<?php use Config\Constants; ?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Menú Principal</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel='stylesheet' href='<?= base_url('assets/css/estilos.css') ?>'>
</head>
<body>
    <?= view('capas/barra', ['cambiar_contrasena' => true, 'sonido' => false, 'mostrar_logout' => true]); ?>
    <div class="body-formulario">
        <main class="main-content">
            <div class="formulario formulario-menu">
                <h1>👑 <?= Constants::getNombre() ?> 👑</h1>
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
                <button class="boton-partida" onclick="location.href='<?= base_url('partida/crear') ?>'">Crear Partida</button>
                <br><br>
                <button class="boton-partida" onclick="location.href='<?= base_url('partida/unirse') ?>'">Unirse a Partida Existente</button>
                <br><br>
                <button class="boton-ranking" onclick="location.href='<?= base_url('ranking') ?>'">Ranking</button>
                <br><br>
                <button class="boton-volver" onclick="location.href='<?= base_url('logout') ?>'">Cerrar Sesión</button>
            </div>
        </main>
        <?= view('capas/pie') ?>
    </div>
    <script src="<?= base_url('assets/js/barra.js') ?>"></script>
</body>
</html>