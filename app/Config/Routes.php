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

//RUTAS PARA CONFIGURACIÓN DE SETUP
$routes->get('setup', 'SetupController::index');
$routes->get('setup/exito', 'SetupController::exito');
$routes->get('setup/error', 'SetupController::error');
