/**----------------------------------
 * SCRIPT PARA LA BARRA DE NAVEGACIÓN
------------------------------------*/

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