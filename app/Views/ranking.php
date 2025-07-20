<!--Vista de RANKING de la Princesa Ana-->

<?php use Config\Constants; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking <?= Constants::getNombre() ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>
<body>
    <div class="body-formulario">
        <main class="main-content">
            <div class="formulario">
                <h1>👑 <?= Constants::getNombre() ?> 👑</h1>

                <?php foreach ($rankingPorTablero as $dimension => $ranking): ?>
                    <h2>Ranking Tablero (<?= esc($dimension) ?>) 🏆</h2>
                    <table class="tabla-exito">
                        <thead>
                            <tr>
                                <th>Puesto</th>
                                <th>Jugador</th>
                                <th>Partidas Jugadas</th>
                                <th>Partidas Ganadas</th>
                                <th>Última Partida</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($ranking) === 0): ?>
                                <tr>
                                    <td colspan="5">No existen jugadores para mostrar</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ranking as $i => $jugador): 
                                    $medalla = '';
                                    if ($i === 0) $medalla = '🥇';
                                    elseif ($i === 1) $medalla = '🥈';
                                    elseif ($i === 2) $medalla = '🥉';

                                    $fecha = new DateTime($jugador['ultima_partida']);
                                ?>
                                    <tr>
                                        <td><?= $i + 1 ?> <?= $medalla ?></td>
                                        <td><?= esc($jugador['nombreUsuario']) ?></td>
                                        <td><?= esc($jugador['jugadas']) ?></td>
                                        <td><?= esc($jugador['ganadas']) ?></td>
                                        <td><?= $fecha->format('d/m/Y H:i:s') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
                <button class="boton-volver" onclick="location.href='<?= base_url('partida') ?>'">Volver al menú principal</button>
            </div>
        </main>
        <?= view('capas/pie') ?>
    </div>
</body>
</html>
