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
</head>
<body>
    <header class="navbar">
        <div class="usuario-info">
            Bienvenido, <?= esc($nombre) . ' ' . esc($apellido) ?>
        </div>
        <div class="audio-control" id="audio-control" style="display: none;">
            <audio id="musica" src="<?= base_url('assets/audio/Tema_principal.mp3') ?>" loop></audio>
            <button onclick="reproducir()">
                <i class="fa-solid fa-play" id="icono_reproducir" title="Reproducir/Pausar"></i>
            </button>
            <input type="range" id="volumen" min="0" max="1" step="0.01" value="0.2">
        </div>
        <div>
            <img src="<?= base_url('assets/img/menu.png') ?>" alt="menu" class="imagen-menu" onclick="menu_desplegable()">
        </div>
        <div id="menu" class="menu-desplegable">
            <a href="<?= base_url('cambiar_contrasena') ?>">Cambiar Contraseña</a>
            <a href="#" onclick="habilitar_sonido()">Sonido</a>
            <a href="<?= base_url('logout') ?>">
                <img src="<?= base_url('assets/img/cerrar_sesion.png') ?>" alt="Cerrar Sesión" class="imagen-logout"> Cerrar Sesión
            </a>
        </div>
    </header>

    <main>
        <h1><?= Constants::getNombre() ?></h1>
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

        function menu_desplegable() {
            const menu = document.getElementById('menu');
            menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('menu');
            if (!menu.contains(event.target) && !event.target.matches('.imagen-menu')) {
                menu.style.display = 'none';
            }
        });

        function habilitar_sonido() {
            const audioControl = document.getElementById('audio-control');
            audioControl.style.display = (audioControl.style.display === 'none') ? 'block' : 'none';
        }
    </script>
</body>
</html>
