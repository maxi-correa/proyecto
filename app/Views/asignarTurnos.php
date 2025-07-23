<!-- Vista para la asignación de turnos -->
<?php use Config\Constants; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
    <title>Asignación de Turnos</title>
    <style>
        body {
            text-align: center;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<div class="body-formulario">
    <main class="main-content">
        <div class="formulario">
            <h1>¡Bienvenidos a <?= Constants::getNombre() ?>!</h1>
            <!-- Tabla resumen del historial -->
            <?php if ($resumen['tipo'] === 'historial'): ?>
                <table class="tabla-exito">
                    <thead>
                    <tr><th colspan="2">Última partida entre jugadores</th></tr>
                    </thead>
                    <tbody>
                    <tr><td>Fecha</td><td><?= esc($resumen['fecha']) ?></td></tr>
                    <tr><td>Hora</td><td><?= esc($resumen['hora']) ?></td></tr>
                    <tr><td>Ganador</td><td><?= esc($resumen['ganador']) ?></td></tr>
                    </tbody>
                </table>
            <!-- Si no es historial, mostrar ranking -->
            <?php elseif ($resumen['tipo'] === 'ranking'): ?>
                <table class="tabla-exito">
                    <thead>
                    <tr><th colspan="2">Victorias históricas</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($resumen['victorias'] as $nombre => $cant): ?>
                        <tr>
                            <td><?= esc($nombre) ?></td>
                            <td><?= $cant ?> victoria<?= $cant == 1 ? '' : 's' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <p>¡Si mayor es el número, jugarás primero!</p>
            <div class="numeros-container" id="numeros"></div>
            <div id="lista-turnos"></div>
        </div>
    </main>
    <?= view('capas/pie') ?>
</div>
<script>
window.onload = function () {
    let jugadores = <?= json_encode($jugadores) ?>;
    const numerosContainer = document.getElementById('numeros');
    const listaTurnos = document.getElementById('lista-turnos');

    // Ordenar por ordenTurnos ascendente (ya asignado en el backend)
    jugadores.sort((a, b) => a.turno - b.turno);

    // Mostrar cajas con nombres y números (desorden visual)
    const jugadoresVisuales = [...jugadores].sort(() => Math.random() - 0.5);
    jugadoresVisuales.forEach(jugador => {
        const box = document.createElement('div');
        box.className = 'jugador-box';

        const numDiv = document.createElement('div');
        numDiv.id = 'num-' + jugador.turno;
        numDiv.innerText = '';

        const nameDiv = document.createElement('div');
        nameDiv.className = 'nombre-jugador';
        nameDiv.innerText = jugador.nombre;

        box.appendChild(numDiv);
        box.appendChild(nameDiv);
        numerosContainer.appendChild(box);
    });

    // Función que genera un número según la posición en el orden de turnos
    function generarNumeroPorTurno(turnoIndex, totalJugadores) {
        const rangoMin = 0;
        const rangoMax = 9;
        const tamanoBloque = Math.floor((rangoMax - rangoMin + 1) / totalJugadores);

        // Invertimos el índice: el jugador con menor ordenTurno recibe bloque más alto
        const invertido = totalJugadores - 1 - turnoIndex;

        const desde = rangoMin + invertido * tamanoBloque;
        let hasta = desde + tamanoBloque - 1;

        // Asegurar que el último rango llegue hasta el máximo
        if (invertido === totalJugadores - 1) {
            hasta = rangoMax;
        }

        return Math.floor(Math.random() * (hasta - desde + 1)) + desde;
    }

    // Simular animación y luego fijar valores
    let count = 0;
    const interval = setInterval(() => {
        count++;
        jugadores.forEach(j => {
            const elem = document.getElementById('num-' + j.turno);
            elem.innerText = Math.floor(Math.random() * 10);
        });

        if (count > 20) {
            clearInterval(interval);

            jugadores.forEach((j, idx) => {
                const elem = document.getElementById('num-' + j.turno);
                const valor = generarNumeroPorTurno(idx, jugadores.length);
                elem.innerText = valor;

                listaTurnos.innerHTML += `<p>#${idx + 1}: ${j.nombre}</p>`;
            });

            setTimeout(() => {
                window.location.href = "<?= base_url('partida/jugar/') . $idPartida ?>";
            }, 10000);
        }
    }, 100);
};
</script>
</body>
</html>

