<!-- Vista para la asignación de turnos -->
<?php use Config\Constants; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
    <title>Ruleta de Turnos</title>
    <style>
        body {
            text-align: center;
            background-color: #f5f5f5;
        }
        .numeros-container {
            margin-top: 40px;
            font-size: 40px;
            font-weight: bold;
            display: flex;
            justify-content: center;
            gap: 30px;
        }
        .jugador-box {
            text-align: center;
            width: 100px;
        }
        .nombre-jugador {
            font-size: 18px;
            margin-top: 10px;
        }
        #lista-turnos {
            margin-top: 40px;
            font-size: 20px;
            font-weight: bold;
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

        // Mezclar visualmente, pero respetar el campo 'turno'
        jugadores = jugadores.sort(() => Math.random() - 0.5);

        // Mostrar cajas con nombres y números
        jugadores.forEach(jugador => {
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

        // Simular animación de números y luego fijar valor
        function generarNumeroPorTurno(turnoIndex) {
            switch (turnoIndex) {
                case 0: return Math.floor(Math.random() * 2) + 8; // 8–9
                case 1: return Math.floor(Math.random() * 2) + 6; // 6–7
                case 2: return Math.floor(Math.random() * 2) + 4; // 4–5
                case 3: return Math.floor(Math.random() * 2) + 2; // 2–3
                default: return Math.floor(Math.random() * 2);    // 0–1 si hay más jugadores
            }
        }
        let count = 0;
        const interval = setInterval(() => {
            count++;
            jugadores.forEach(j => {
                const elem = document.getElementById('num-' + j.turno);
                elem.innerText = Math.floor(Math.random() * 10);
            });
            if (count > 20) {
                clearInterval(interval);
                // Mostrar resultados definitivos
                jugadores.sort((a, b) => a.turno - b.turno).forEach((j, idx) => {
                    const elem = document.getElementById('num-' + j.turno);
                    const valor = generarNumeroPorTurno(idx);
                    elem.innerText = valor;
                    listaTurnos.innerHTML += `<p>#${idx + 1}: ${j.nombre}</p>`;
                });
                setTimeout(() => {
                    window.location.href = "<?= base_url('partida/jugar/') ?><?= $idPartida ?>";
                }, 10000);
            }
        }, 100);
    };
</script>
</body>
</html>

