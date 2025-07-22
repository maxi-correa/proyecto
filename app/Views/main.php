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
html, body {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

body > .contenedor-juego {
    flex: 1;
}
/* Contenedor principal en 5 columnas */
.contenedor-juego {
    display: grid;
    grid-template-columns: 1fr 1.5fr 3fr 1.5fr 1fr;
    gap: 15px;
    margin: 20px;
    align-items: flex-start; /* Alinea todas las columnas al mismo alto */
    justify-items: center; /* Centra el contenido de cada columna */
}

.celda-ana {
    border: 2px solid gold;
    background-color: #fffde7; /* fondo amarillo claro para destacar */
}

/* ===== COL 1: Princesa ===== */
.columna {
    padding: 10px;
    box-sizing: border-box;
    align-items: center; /* Alinea el contenido al centro */
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

.mensaje-fin-consenso {
    display:none;
    background-color: white;
    color: red;
    border: 1px solid black;
    padding: 10px;
    margin-top: 10px;
    font-size: 14px;
    text-align: center;
    border-radius: 4px;
    box-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    max-width: 100%;
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
    <h1>👑 <?= Constants::getNombre() ?> 👑</h1>
    <h2>Partida #<?= esc($idPartida) ?></h2>

    <div class="contenedor-juego">
    <!-- Columna 1: Princesa -->
    <div class="columna princesa">
        <img src="<?= base_url('assets/img/princesa.png') ?>" alt="Princesa" class="imagen-princesa">
    </div>

    <!-- Columna 2: Cuadro de diálogo -->
    <div class="columna dialogo-contenedor">
        <div class="dialogo">
            <p id="mensaje-turno" style="text-align: center;">Cargando información de turno...</p>
            <p>Completa el tablero con letras A o N. ¡Forma la palabra "ANA" para sumar puntos!</p>
            <p>"Retirarse": Te permite abandonar la partida sin poder ser el ganador.</p>
            <p>"Terminar partida": Propones finalizar la partida. Si todos están de acuerdo, se termina.</p>
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
        <button id="btnRetirarse" class="boton-volver">Retirarse</button><br>
        <button id="btnTerminar" class="boton-partida">Terminar partida</button>
        <div id="mensaje-fin-consenso" class="mensaje-fin-consenso"></div>
    </div>

    <!-- Modal Retiro -->
    <div id="modalRetiro" class="modal">
        <div class="formulario">
            <h3>¿Seguro que querés abandonar la partida?</h3>
            <h3>Si abandonas no podrás ganar ni volver a conectarte a la partida.</h3>
            <button class="boton-confirmar" onclick="confirmarRetiro()">Sí, retirarme</button><br><br>
            <button class="boton-volver" onclick="cerrarModal('modalRetiro')">Cancelar</button>
        </div>
    </div>

</div>
    <?= view('capas/pie') ?>

    <script src="<?= base_url('assets/js/barra.js') ?>"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
    const idPartida = <?= $idPartida ?>;
    const nombreJugador = "<?= esc($nombre) ?>";

    // Estado y renderizado del juego
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

    if (!data.jugador_turno) {
        mensajeTurno.innerText = "Esperando siguiente turno...";
    } else if (data.jugador_turno === nombreJugador) {
        mensajeTurno.innerText = "¡Es tu turno!";
    } else {
        mensajeTurno.innerText = "Turno de " + data.jugador_turno;
    }

    const filas = data.filas;
    const columnas = data.columnas;
    const tablero = data.tablero;

    // 🔹 Guardar celda con foco actual (si existe)
    const active = document.activeElement;
    const activeFila = active?.dataset?.fila;
    const activeColumna = active?.dataset?.columna;

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

            // Verifica si esta celda forma parte de "ANA"
            if (data.celdasANA?.some(c => c.fila === i && c.columna === j)) {
                input.classList.add('celda-ana');
            }

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

            // 🔹 Restaurar foco si era esta celda
            if (String(i) === activeFila && String(j) === activeColumna) {
                setTimeout(() => input.focus(), 10); // Evita conflicto con el redibujado
            }

            tableroContainer.appendChild(input);
        }
    }

    puntajesContainer.innerHTML = '<h3>Puntajes</h3>';
    data.puntajes.forEach(j => {
        puntajesContainer.innerHTML += `<p>${j.nombre}: ${j.puntos}</p>`;
    });

    const mensajeFin = document.getElementById('mensaje-fin-consenso');
    const btnTerminar = document.getElementById('btnTerminar');

    if (data.consenso) {
        if (data.consenso.yoPropuse) {
            mensajeFin.textContent = "Has elegido terminar la partida. Esperando respuesta de los demás jugadores. Podés seguir jugando.";
            mensajeFin.style.display = 'block';
            btnTerminar.disabled = true;
        } else if (!data.consenso.yaVote) {
            mensajeFin.textContent = "Un jugador quiere terminar la partida. ¿Estás de acuerdo? Haz clic en 'Terminar partida' para votar.";
            mensajeFin.style.display = 'block';
            btnTerminar.disabled = false;
        } else {
            mensajeFin.textContent = "Tu decisión fue registrada. Esperando al resto.";
            mensajeFin.style.display = 'block';
            btnTerminar.disabled = true;
        }
    } else {
        mensajeFin.textContent = "";
        mensajeFin.style.display = 'none'; // No mostrar mensaje si no hay consenso
        btnTerminar.disabled = false;
    }
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
        input.style.backgroundColor = '#ccc';

        fetch("<?= base_url('partida/jugarAJAX') ?>", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idPartida, fila: parseInt(fila), columna: parseInt(columna), letra })
        })
        .then(res => res.json())
        .then(res => {
            if (res.finalizada && res.redirect) {
                window.location.href = res.redirect;
                return;
            }

            if (res.success) {
                obtenerEstadoPartida();
            } else {
                alert(res.message);
            }
        });
    }

    // BOTONES FUNCIONALES
    document.getElementById('btnRetirarse')?.addEventListener('click', () => abrirModal('modalRetiro'));
    document.getElementById('btnTerminar')?.addEventListener('click', () => proponerFin());

    // MODALES
    function abrirModal(id) {
        document.getElementById(id)?.classList.add('activo');
    }

    function cerrarModal(id) {
        document.getElementById(id)?.classList.remove('activo');
    }

    window.cerrarModal = cerrarModal;

    // RETIRO
    window.confirmarRetiro = function() {
        fetch("<?= base_url('partida/retirarse') ?>", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idPartida })
        })
        .then(r => r.json())
        .then(r => {
            if (r.redirect) window.location.href = r.redirect;
        });
    }

    // Fin consensuado
    function proponerFin() {
    // Enviamos el voto positivo e informamos que este jugador lo propuso
    fetch("<?= base_url('partida/fin') ?>", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idPartida, acepto: true })
    })
    .then(r => r.json())
    .then(r => {
        const mensaje = document.getElementById('mensaje-fin-consenso');
        const botonTerminar = document.getElementById('btnTerminar');

        if (r.finalizada && r.redirect) {
            window.location.href = r.redirect;
        } else if (r.cancelado) {
            mensaje.textContent = "Un jugador rechazó terminar la partida. La opción ya no está disponible.";
            botonTerminar.disabled = true;
        } else {
            mensaje.textContent = "Has elegido terminar la partida. Esperando respuesta de los demás jugadores. Podés seguir jugando.";
            botonTerminar.disabled = true;
        }
    });
    }

    // Iniciar estado y polling
    obtenerEstadoPartida();
    setInterval(obtenerEstadoPartida, 3000);
});
</script>
</body>
</html>
