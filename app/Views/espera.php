<!-- Una vez que se crea una partida, se debe esperar a que se unan los demás jugadores antes de comenzar el juego. -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Esperando Jugadores</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/estilos.css') ?>">
</head>
<body>
    <div class="body-formulario">
        <main class="main-content">
            <div class="formulario">
                <h1>Esperando Jugadores...</h1>
                <p>Partida #<?= esc($idPartida) ?> creada correctamente.</p>
                <p>Esperando que se unan los demás jugadores para comenzar el juego.</p>

                <!-- Futuro: mostrar lista de jugadores conectados -->
                <!-- Futuro: botón para cancelar partida -->

                <div class="loader"></div> <!-- Animación de carga opcional -->
            </div>
        </main>
    </div>
</body>
</html>
