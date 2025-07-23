<!-- app/Views/capas/barra.php -->
<?php $cambiar_contrasena = $cambiar_contrasena ?? true; ?>
<?php $sonido = $sonido ?? true; ?>
<?php $mostrar_logout = $mostrar_logout ?? true; ?>

<!-- BARRA DE NAVEGACIÓN -->

<header class="navbar">
        <div class="usuario-info">
            Bienvenid@, <?= esc($nombre)?>
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
        
            <?php if ($cambiar_contrasena): ?>
            <a href="<?= base_url('cambiar_contrasena') ?>">Cambiar Contraseña</a>
            <?php endif; ?>

            <?php if($sonido): ?>
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
            <?php endif; ?>
            
            <?php if($mostrar_logout): ?>
            <a href="<?= base_url('logout') ?>">
                <img src="<?= base_url('assets/img/cerrar_sesion.png') ?>" alt="Cerrar Sesión" class="imagen-logout"> Cerrar Sesión
            </a>
            <?php endif; ?>
        </div>
    </header>