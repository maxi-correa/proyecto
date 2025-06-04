<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='<?= base_url('assets/css/estilos.css') ?>'>
</head>
<body>
    <header class="navbar">
        <div class="usuario-info">
            Bienvenido, <?= esc($nombre) . ' ' . esc($apellido) ?>
        </div>
        <div class="audio-control">
            <audio id="player" src="<?= base_url('assets/audio/Tema_principal.mp3') ?>" autoplay loop></audio>
            <button onclick="togglePlay()">Play/Pause</button>
            <input type="range" id="volumen" min="0" max="1" step="0.01" value="0.5">
        </div>
        <div class="usuario-info">
            <a href="<?= base_url('logout') ?>">Cerrar sesión</a>
        </div>
    </header>

    <main>
        <h1>Página principal</h1>
        <!-- Acá iría el contenido del juego o lo que defina el profesor -->
    </main>

    <script>
        const player = document.getElementById('player');
        const volumen = document.getElementById('volumen');

        function togglePlay() {
            if (player.paused) {
                player.play();
            } else {
                player.pause();
            }
        }

        volumen.addEventListener('input', () => {
            player.volume = volumen.value;
        });
    </script>
</body>
</html>
