<!-- PAGINA DE SETUPERROR -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error de Setup</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>
<body>
    <header>
        <h1 class="h1-error">Error de Setup</h1>
    </header>
    
    <table class="tabla-error">
    <tr>
        <td rowspan="2">
        <img src="<?= base_url('assets/img/error.png') ?>" alt="Error" class="imagen-central" aria-placeholder="Error">
        </td>
        <td>
        <div class="mensaje-error">
            <?= esc($mensaje) ?>
        </div>
        </td>
    </tr>
    <tr>
        <td>
            <a href="<?= base_url('setup') ?>" class="boton-reintentar">Volver a intentar</a>
        </td>
    </tr>
    </table>
    </main>
</body>
</html>