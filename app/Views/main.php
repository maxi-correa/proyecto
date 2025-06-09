<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel='stylesheet' href='<?= base_url('assets/css/estilos.css') ?>'>
</head>
<body>
    <header class="navbar">
        <div class="usuario-info">
            Bienvenido, <?= esc($nombre) . ' ' . esc($apellido) ?>
        </div>
        <div class="audio-control">
            <audio id="musica" src="<?= base_url('assets/audio/Tema_principal.mp3') ?>" autoplay loop></audio>
            <button onclick="reproducir()">
                <i class="fa-solid fa-pause" id="icono_reproducir" title="Reproducir/Pausar"></i>
            </button>
            <input type="range" id="volumen" min="0" max="1" step="0.01" value="0.2">
        </div>
        <a href="<?= base_url('/logout') ?>" class="imagen-logout">
            <img src="<?= base_url('assets/img/cerrar_sesion.png') ?>" alt="Cerrar sesión" title="Cerrar sesión">
        </a>
    </header>

    <main>
        <h1>Página principal</h1>
        <!-- Acá iría el contenido del juego o lo que defina el profesor -->
        
    </main>
    
    <?= view('capas/pie') ?>

    <script>
        const musica = document.getElementById('musica');
        const volumen = document.getElementById('volumen');

        function reproducir() {
            if (musica.paused) {
                musica.play();
                const icono_reproducir = document.getElementById('icono_reproducir');
                icono_reproducir.classList.remove('fa-play');
                icono_reproducir.classList.add('fa-pause');
            } else {
                musica.pause();
                const icono_reproducir = document.getElementById('icono_reproducir');
                icono_reproducir.classList.remove('fa-pause');
                icono_reproducir.classList.add('fa-play');
            }
        }

        volumen.addEventListener('input', () => {
            musica.volume = volumen.value;
        });
    </script>
</body>
</html>
