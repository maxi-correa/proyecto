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
/* Contenedor principal en 5 columnas */
.contenedor-juego {
    display: grid;
    grid-template-columns: 1fr 1.5fr 3fr 1.5fr 1fr;
    gap: 15px;
    margin: 20px;
    align-items: flex-start;
}

/* ===== COL 1: Princesa ===== */
.columna {
    padding: 10px;
    box-sizing: border-box;
}

.princesa-container {
    text-align: center;
}

.imagen-princesa {
    width: 100%;
    max-width: 200px;
}

/* ===== COL 2: Cuadro de diálogo ===== */
.dialogo-contenedor {
    position: relative;
}

.dialogo {
    position: relative;
    background-color: #f8f8f8;
    border: 2px solid #ccc;
    border-radius: 10px;
    padding: 10px;
    font-size: 14px;
    margin-top: 10px;
    text-align: left;
}

/* Punta que simula que la princesa habla de lado */
.dialogo::after {
    content: '';
    position: absolute;
    top: 20px;
    left: -20px;
    width: 0;
    height: 0;
    border: 10px solid transparent;
    border-right-color: #f8f8f8;
}

.dialogo::before {
    content: '';
    position: absolute;
    top: 19px;
    left: -22px;
    width: 0;
    height: 0;
    border: 10px solid transparent;
    border-right-color: #ccc;
}

.dialogo p {
    margin: 5px 0;
}

.instrucciones {
    font-size: 12px;
    color: #666;
}

/* ===== COL 3: Tablero ===== */
.tablero-contenedor {
    display: flex;
    justify-content: center;
}

.tablero {
    display: grid;
    gap: 5px;
    margin-top: 20px;
    justify-content: center;
}

.celda {
    width: 40px;
    height: 40px;
    font-size: 20px;
    text-align: center;
    box-sizing: border-box;
    padding: 0;
}

.no-turno {
    background-color: #eee;
}

/* ===== COL 4: Puntajes ===== */
.puntajes-contenedor .puntajes {
    margin-top: 20px;
    font-weight: bold;
    font-size: 16px;
}

/* ===== COL 5: Botones de acciones ===== */
.acciones {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.acciones button {
    padding: 8px 12px;
    font-size: 14px;
    background-color: #d9534f;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.acciones button:disabled {
    background-color: #aaa;
    cursor: not-allowed;
}

/* Mensaje de turno */
#mensaje-turno {
    margin-top: 10px;
    font-size: 18px;
    color: #333;
}

/* ====== RESPONSIVE ====== */
@media (max-width: 768px) {
    .contenedor-juego {
        grid-template-columns: 1fr;
        grid-template-rows: auto;
    }

    .tablero {
        margin: 10px auto;
    }

    .acciones {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
    }

    .dialogo::after,
    .dialogo::before {
        display: none;
    }

    .dialogo {
        text-align: center;
    }

    .imagen-princesa {
        max-width: 150px;
    }

    .celda {
        width: 35px;
        height: 35px;
        font-size: 18px;
    }

    .puntajes-contenedor .puntajes {
        text-align: center;
    }
}
    </style>
</head>
<body>
    <?= view('capas/barra') ?>

    <h1>Partida #<?= esc($idPartida) ?></h1>

    <div class="contenedor-juego">
    <!-- Columna 1: Princesa -->
    <div class="columna princesa">
        <img src="<?= base_url('assets/img/princesa.png') ?>" alt="Princesa" class="imagen-princesa">
    </div>

    <!-- Columna 2: Cuadro de diálogo -->
    <div class="columna dialogo-contenedor">
        <div class="dialogo">
            <p id="mensaje-turno">Cargando información de turno...</p>
            <p>Completa el tablero con letras A o N. ¡Forma la palabra "ANA" para sumar puntos!</p>
        </div>
    </div>

    <!-- Columna 3: Tablero -->
    <div class="columna tablero-contenedor">
        <div id="tablero-container" class="tablero"></div>
    </div>

    <!-- Columna 4: Puntajes -->
    <div class="columna puntajes-contenedor">
        <div class="puntajes" id="puntajes-container"></div>
    </div>

    <!-- Columna 5: Acciones -->
    <div class="columna acciones">
        <button id="boton-retirarse" disabled>Retirarse</button> <!-- por ahora deshabilitado -->
    </div>
</div>

    <?= view('capas/pie') ?>

    <script src="<?= base_url('assets/js/barra.js') ?>"></script>
    <script>
        const idPartida = <?= $idPartida ?>;
        const nombreJugador = "<?= esc($nombre) ?>";

        function obtenerEstadoPartida() {
            fetch("<?= base_url('partida/estadoAJAX/') ?>" + idPartida)
            .then(response => response.json())
            .then(data => {
                if (data.finalizada && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
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
            tableroContainer.style.gridTemplateColumns = `repeat(${columnas}, 40px)`;
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

            if (letra !== 'A' && letra !== 'N') { //Si no es A o N
                input.value = ''; // Limpia el input
                alert('Solo se permiten letras A o N'); // Muestra el mensaje
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
                if (res.finalizada && res.redirect) { // Si la partida ha finalizado
                    window.location.href = res.redirect; // Redirigir a resultados si la partida ha finalizado
                    return;
                }

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
