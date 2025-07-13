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
                <h1>Esperando jugadores...</h1>
                <p>Estás conectado a la partida #<?= esc($idPartida) ?>.</p>
                <p>Cuando se hayan conectado todos los jugadores, el juego comenzará automáticamente.</p>

                <p id="estado-jugadores">Jugadores conectados: ...</p> <!-- Muestra el número de jugadores conectados -->
                
                <div class="loader"></div> <!-- Animación de carga? -->
                
                <?php if(!$esCreador): ?>
                <form action="<?= base_url('partida/salir_espera/' . $idPartida) ?>" method="get">
                    <button type="submit" class="btn-volver">Volver a la lista de partidas</button>
                </form>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script>
        const idPartida = <?= esc($idPartida) ?>;

        function verificarEstado() {
            fetch("<?= base_url('partida/estado/') ?>" + idPartida)
                .then(response => response.json())
                .then(data => { //data es un objeto JSON que contiene el estado de la partida
                    document.getElementById('estado-jugadores').innerText =
                        `Jugadores conectados: ${data.conectados} / ${data.limite}`;

                if (data.completo) {
                    window.location.href = "<?= base_url('partida/turnos/') ?>" + idPartida;
                }
            });
        }

        setInterval(verificarEstado, 3000); // cada 3 segundos
</script>
</body>
</html>
