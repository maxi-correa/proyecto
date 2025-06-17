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
        <div class="audio-control" id="musica-control" style="display: none;">
            <span class="usuario-info">Música de Fondo:</span>
            <audio id="musica" src="<?= base_url('assets/audio/Tema_principal.mp3') ?>" loop></audio>
            <button onclick="reproducir()">
                <i class="fa-solid fa-play" id="icono_reproducir" title="Reproducir/Pausar"></i>
            </button>
            <input type="range" id="volumen" min="0" max="1" step="0.01" value="0.2">
        </div>
        <div class="audio-control" id="sonido-control" style="display: none;">
            <span class="usuario-info">Efectos de Sonido:</span>
            <audio id="sonido" src="<?= base_url('assets/audio/sonido_click.mp3') ?>"></audio>
            <input type="range" id="volumen" min="0" max="1" step="0.01" value="0.2">
        </div>
        <div>
            <img src="<?= base_url('assets/img/menu.png') ?>" alt="menu" class="imagen-menu" onclick="menu_desplegable()">
        </div>
        <div id="menu" class="menu-desplegable" style="display: none;">
            <a href="<?= base_url('cambiar_contrasena') ?>">Cambiar Contraseña</a>

            <div class="submenu-contenedor">
                <a href="#" onclick="habilitar_sonido(event)">Sonido</a>
                <div id="subMenuSonido" class="submenu-sonido oculto">
                    <a href="#">
                        <label><input type="checkbox" id="activarMusica" onchange="mostrarMusicaControl()"> Música de Fondo</label>
                    </a>
                    <a href="#">
                        <label><input type="checkbox" id="activarEfectos" onchange="mostrarEfectoControl()"> Efectos de Sonido</label>
                    </a>
                </div>
            </div>

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
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }

        document.addEventListener('click', function(event) {
            // Cierra el submenú de sonido si se hace clic fuera de él
            const submenu = document.getElementById('subMenuSonido');
            const sonidoLink = event.target.closest('a');
            if (!submenu.contains(event.target) && (!sonidoLink || sonidoLink.textContent.trim() !== 'Sonido')) {
                submenu.classList.add('oculto');
            }
            // Cierra el menú si se hace clic fuera de él
            const menu = document.getElementById('menu');
            if (!menu.contains(event.target) && !event.target.matches('.imagen-menu')) {
                menu.style.display = 'none';
            }
        });

        function habilitar_sonido(event) 
        {
            event.preventDefault(); //evita que <a> se recargue
            const submenu = document.getElementById('subMenuSonido');
            submenu.classList.toggle('oculto');
        }

        function mostrarMusicaControl() {
            const musicaControl = document.getElementById('musica-control');
            const activarMusica = document.getElementById('activarMusica');

            if (activarMusica.checked) {
                musicaControl.style.display = 'block';
            } else {
                musicaControl.style.display = 'none';
            }
        }

        function mostrarEfectoControl() {
            const sonidoControl = document.getElementById('sonido-control');
            const activarEfectos = document.getElementById('activarEfectos');

            if (activarEfectos.checked) {
                sonidoControl.style.display = 'block';
            } else {
                sonidoControl.style.display = 'none';
            }
        }
    </script>
</body>
</html>
