<?php
use Config\Constants;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= Constants::getNombre() ?></title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel='stylesheet' href='<?= base_url('assets/css/estilos.css') ?>'>
</head>
<body>
    <?= view('capas/barra') ?>

    <main>
        <h1><?= Constants::getNombre() ?></h1>
        <!-- Acá iría el contenido del juego o lo que defina el profesor -->
        
    </main>
    
    <?= view('capas/pie') ?>

    <script src="<?= base_url('assets/js/barra.js') ?>"></script>
</body>
</html>
