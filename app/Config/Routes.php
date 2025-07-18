<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 //RUTA PARA EL CONTROLADOR PRINCIPAL
$routes->get('/', 'MainController::index');

//RUTAS PARA EL INICIO DE SESIÓN Y REGISTRO
$routes->get('login', 'AuthController::login');
$routes->post('login/procesar', 'AuthController::procesarLogin');
$routes->get('registro', 'AuthController::registro');
$routes->post('registro/procesar', 'AuthController::procesarRegistro');

//RUTA PARA CERRAR SESIÓN
$routes->get('logout', 'MainController::logout');

//RUTA PARA RECUPERAR CONTRASEÑA
$routes->get('recuperar', 'AuthController::recuperarContrasena');
$routes->post('recuperar/procesar', 'AuthController::procesarRecuperarContrasena');

//RUTA PARA CAMBIAR CONTRASEÑA
$routes->get('cambiar_contrasena', 'PasswordController::cambiarContrasena');
$routes->post('cambiar_contrasena/procesar', 'PasswordController::procesarCambiarContrasena');

//RUTA PARA ELECCIÓN DE PARTIDA
$routes->get('partida', 'PartidaController::eleccionPartida');

//RUTAS PARA CREAR PARTIDA
$routes->get('partida/crear', 'PartidaController::vistaCrearPartida');
$routes->post('partida/crear/procesar', 'PartidaController::crearPartida');
$routes->get('partida/espera/(:num)', 'PartidaController::espera/$1');

//RUTAS PARA UNIRSE A PARTIDA EXISTENTE
$routes->get('partida/unirse', 'PartidaController::listarPartidas');
$routes->post('partida/unirse/procesar', 'PartidaController::unirsePartida');
$routes->get('partida/verificar_espera/(:num)', 'PartidaController::verificarEspera/$1');
$routes->get('partida/estado/(:num)', 'PartidaController::estadoPartida/$1');

//RUTA PARA LA ASIGNACIÓN DE TURNOS
$routes->get('partida/turnos/(:num)', 'PartidaController::asignarTurnos/$1');

//RUTAS PARA EL JUEGO
$routes->get('partida/jugar/(:num)', 'MainController::jugar/$1');
$routes->get('partida/estadoAJAX/(:num)', 'MainController::estadoAJAX/$1');
$routes->post('partida/jugarAJAX', 'MainController::jugarAJAX');

//RUTAS PARA RESULTADOS DE PARTIDA
$routes->get('partida/resultados/(:num)', 'PartidaController::mostrarResultados/$1');

//RUTAS PARA SALIR DE PARTIDA
$routes->get('partida/salir_espera/(:num)', 'PartidaController::salirDeEspera/$1');

//RUTAS PARA CONFIGURACIÓN DE SETUP (NO SE USARÁN)
$routes->get('setup', 'SetupController::index');
$routes->get('setup/exito', 'SetupController::exito');
$routes->get('setup/error', 'SetupController::error');
