<!-- Vista para la asignación de turnos -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignar Turnos</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>
<body>
<div class="body-formulario">
    <main class="main-content">
        <div class="formulario">
            <h2>Asignación de Turnos</h2>
            <ul>
                <?php foreach ($jugadores as $jugador): ?>
                    <li>
                        <?= esc($jugador['nombre']) ?> <?= $jugador['turno'] ? "- Turno: " . esc($jugador['turno']) : "" ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p>Redirigiendo al juego en 5 segundos...</p>
        </div>
    </main>
</div>

<script>
    setTimeout(function () {
        window.location.href = "<?= base_url('partida/jugar/' . $idPartida) ?>";
    }, 5000);
</script>
</body>
</html>
