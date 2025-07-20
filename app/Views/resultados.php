<!-- Vista de resultados de partida -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de la Partida #<?= esc($idPartida) ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>
<body class="body-formulario">

    <main class="main-content">

        <div class="formulario">

            <h1 class="h1-exito">🎉 Resultados - Partida #<?= esc($idPartida) ?> 🎉</h1>

            <table class="tabla-exito">
                <thead>
                    <tr>
                        <th>Jugador</th>
                        <th>Puntos</th>
                        <th>🏆</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados as $r): ?>
                        <tr>
                            <td><?= esc($r['nombre']) ?></td>
                            <td><?= esc($r['puntos']) ?></td>
                            <td><?= $r['esGanador'] ? '🥇' : '' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <br>
            <a href="<?= base_url('/ranking') ?>">
                <button class="boton-ranking">Ver Ranking</button>
            </a>
            <br><br>
            <a href="<?= base_url('/partida') ?>">
                <button class="boton-volver">Volver a menú principal</button>
            </a>
        </div>

    </main>

<?= view('capas/pie') ?>

</body>
</html>