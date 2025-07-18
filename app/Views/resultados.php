<!-- Vista de resultados de partida -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de la Partida <?= esc($idPartida) ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>
<body>
    <h1>Resultados - Partida #<?= esc($idPartida) ?></h1>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Jugador</th>
                <th>Puntos</th>
                <th>Ganador</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($resultados as $r): ?>
                <tr>
                    <td><?= esc($r['nombre']) ?></td>
                    <td><?= esc($r['puntos']) ?></td>
                    <td><?= $r['esGanador'] ? '🏆' : '' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="<?= base_url('/partida') ?>">Volver al inicio</a>
</body>
</html>