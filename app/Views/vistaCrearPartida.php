<!--Página para elección de opciones de partida-->
<?php use Config\Constants; ?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Login</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel='stylesheet' href='<?= base_url('assets/css/estilos.css') ?>'>
</head>
<body>
<div class="body-formulario">
    <main class="main-content">
        <div class="formulario">
            <h1>Crear Partida</h1>

            <!-- Sección de cantidad de jugadores -->
            <label for="cantidadJugadores">¿Cuántos jugadores?</label>
            <div class="opciones-imagenes">
                <input type="radio" name="jugadores" id="jug2" value="2">
                <label for="jug2">
                    <img src="<?= base_url('assets/img/jugadores2.png') ?>" alt="2 jugadores">
                </label>

                <input type="radio" name="jugadores" id="jug3" value="3">
                <label for="jug3">
                    <img src="<?= base_url('assets/img/jugadores3.png') ?>" alt="3 jugadores">
                </label>

                <input type="radio" name="jugadores" id="jug4" value="4">
                <label for="jug4">
                    <img src="<?= base_url('assets/img/jugadores4.png') ?>" alt="4 jugadores">
                </label>
            </div>

            <!-- Sección de tamaño de tablero -->
            <label for="tamanoTablero">Tamaño del tablero</label>
            <div class="opciones-imagenes">
                <input type="radio" name="tablero" id="tab4x4" value="1">
                <label for="tab4x4">
                    <img src="<?= base_url('assets/img/tablero4x4.png') ?>" alt="Tablero 4x4">
                </label>

                <input type="radio" name="tablero" id="tab6x6" value="2">
                <label for="tab6x6">
                    <img src="<?= base_url('assets/img/tablero6x6.png') ?>" alt="Tablero 6x6">
                </label>

                <input type="radio" name="tablero" id="tab10x10" value="3">
                <label for="tab10x10">
                    <img src="<?= base_url('assets/img/tablero10x10.png') ?>" alt="Tablero 10x10">
                </label>
            </div>

            <!-- Botón para continuar -->
            <form action="<?= base_url('partida/crear/procesar') ?>" method="post">
                <input type="hidden" name="cantidad_jugadores" id="jugadoresSeleccionado">
                <input type="hidden" name="tamano_tablero" id="tableroSeleccionado">
                <button type="submit">Crear Partida</button>
            </form>
            <br>
            <button class="boton-volver" onclick="location.href='<?= base_url('partida') ?>'">Volver</button>
        </div>
    </main>
    <?= view('capas/pie') ?>
</div>

<script>
    const jugadorRadios = document.querySelectorAll('input[name="jugadores"]');
    const tableroRadios = document.querySelectorAll('input[name="tablero"]');
    const jugadoresSeleccionado = document.getElementById('jugadoresSeleccionado');
    const tableroSeleccionado = document.getElementById('tableroSeleccionado');

    jugadorRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            jugadoresSeleccionado.value = radio.value;
        });
    });

    tableroRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            tableroSeleccionado.value = radio.value;
        });
    });
</script>
</body>
</html>