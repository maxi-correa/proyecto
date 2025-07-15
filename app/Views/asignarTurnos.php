<!-- Vista para la asignación de turnos -->

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
            <h1>Asignando Turnos...</h1>
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
        const ordenDescendente = [9, 6, 3, 0]; // máximos para turnos 1 a 4
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
                    elem.innerText = ordenDescendente[idx];
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

