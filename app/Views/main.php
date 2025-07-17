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
<style>
        .tablero {
            display: grid;
            gap: 5px;
            margin-top: 20px;
        }

        .celda {
            width: 40px;
            height: 40px;
            font-size: 20px;
            text-align: center;
        }

        .no-turno {
            background-color: #eee;
        }

        .puntajes {
            margin-top: 20px;
            font-weight: bold;
        }

        #mensaje-turno {
            margin-top: 10px;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
    <?= view('capas/barra') ?>

    <h1>Partida #<?= esc($idPartida) ?></h1>
    <p id="mensaje-turno">Cargando información de turno...</p>

    <div id="tablero-container"></div>
    <div class="puntajes" id="puntajes-container"></div>
    
    <?= view('capas/pie') ?>

    <script src="<?= base_url('assets/js/barra.js') ?>"></script>
    <script>
        const idPartida = <?= $idPartida ?>;
        const nombreJugador = "<?= esc($nombre) ?>";

        function obtenerEstadoPartida() {
            fetch("<?= base_url('partida/estadoAJAX/') ?>" + idPartida)
                .then(response => response.json())
                .then(data => {
                    actualizarVista(data);
                });
        }

        function actualizarVista(data) {
            const tableroContainer = document.getElementById('tablero-container');
            const puntajesContainer = document.getElementById('puntajes-container');
            const mensajeTurno = document.getElementById('mensaje-turno');

            // Mostrar mensaje de turno
            if (data.jugador_turno === nombreJugador) {
                mensajeTurno.innerText = "¡Es tu turno!";
            } else {
                mensajeTurno.innerText = "Turno de " + data.jugador_turno;
            }

            // Crear tablero
            const filas = data.filas;
            const columnas = data.columnas;
            const tablero = data.tablero;

            tableroContainer.innerHTML = '';
            tableroContainer.style.gridTemplateColumns = `repeat(${columnas}, 1fr)`;
            tableroContainer.className = 'tablero';

            for (let i = 0; i < filas; i++) {
                for (let j = 0; j < columnas; j++) {
                    const input = document.createElement('input');
                    input.className = 'celda';
                    input.maxLength = 1;
                    input.dataset.fila = i;
                    input.dataset.columna = j;

                    const letra = tablero[i][j];
                    if (letra) {
                        input.value = letra;
                        input.disabled = true;
                    } else if (data.jugador_turno !== nombreJugador) {
                        input.classList.add('no-turno');
                        input.disabled = true;
                    } else {
                        input.addEventListener('input', enviarLetra);
                    }

                    tableroContainer.appendChild(input);
                }
            }

            // Mostrar puntajes
            puntajesContainer.innerHTML = '<h3>Puntajes</h3>';
            data.puntajes.forEach(j => {
                puntajesContainer.innerHTML += `<p>${j.nombre}: ${j.puntos}</p>`;
            });
        }

        function enviarLetra(e) {
            const input = e.target;
            const letra = input.value.toUpperCase();

            if (letra !== 'A' && letra !== 'N') {
                input.value = '';
                alert('Solo se permiten letras A o N');
                return;
            }

            const fila = input.dataset.fila;
            const columna = input.dataset.columna;

            input.disabled = true;
            input.style.backgroundColor = '#ccc'; // Cambiar el color de fondo del input a gris

            fetch("<?= base_url('partida/jugarAJAX') ?>", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    idPartida: idPartida,
                    fila: parseInt(fila),
                    columna: parseInt(columna),
                    letra: letra
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    obtenerEstadoPartida();
                } else {
                    alert(res.message);
                }
            });
        }

        // Cargar estado inicial y actualizar cada 3 segundos
        obtenerEstadoPartida();
        setInterval(obtenerEstadoPartida, 3000);
    </script>
</body>
</html>
