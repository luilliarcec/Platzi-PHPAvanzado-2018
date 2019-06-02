<?php
// Inicio bloque para mostrar errores (Solos ambiente de DESARROLLO)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Fin bloque de errores

require_once('../vendor/autoload.php');

session_start();

$dotenv = Dotenv\Dotenv::create(__DIR__ . '/..');
$dotenv->load();

define('BASE_URL', '/');

use App\Middlewares\AuthenticationMiddleware;
use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;
use Middlewares\AuraRouter;
use WoohooLabs\Harmony\Harmony;
use WoohooLabs\Harmony\Middleware\DispatcherMiddleware;
use WoohooLabs\Harmony\Middleware\HttpHandlerRunnerMiddleware;
use Zend\Diactoros\Response;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

$container = new DI\Container();

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => getenv('DB_DRIVER'),
    'host' => getenv('DB_HOST'),
    'port' => getenv('DB_PORT'),
    'database' => getenv('DB_NAME'),
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASS'),
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new RouterContainer();

$map = $routerContainer->getMap();

$map->get('index', BASE_URL, [
    'App\Controllers\IndexController',
    'IndexAction'
]);

$map->get('loginForm', BASE_URL . 'login', [
    'App\Controllers\AuthController',
    'getLogin'
]);

$map->post('auth', BASE_URL . 'auth', [
    'App\Controllers\AuthController',
    'postLogin'
]);

$map->get('logout', BASE_URL . 'logout', [
    'App\Controllers\AuthController',
    'getLogout'
]);

// RUTAS DE ADMIN

/** RUTA DE JOBS **/
$map->get('indexJobs', BASE_URL . 'jobs', [
    'App\Controllers\JobsController',
    'getIndex'
]);

$map->get('addJobs', BASE_URL . 'jobs/add', [
    'App\Controllers\JobsController',
    'getAddJobAction'
]);

$map->post('saveJobs', BASE_URL . 'jobs/add', [
    'App\Controllers\JobsController',
    'getAddJobAction'
]);

$map->get('deleteJobs', BASE_URL . 'jobs/delete', [
    'App\Controllers\JobsController',
    'deleteAction'
]);

$map->get('restoreJobs', BASE_URL . 'jobs/restore', [
    'App\Controllers\JobsController',
    'restoreAction'
]);

$map->get('hardDeleteJobs', BASE_URL . 'jobs/remove', [
    'App\Controllers\JobsController',
    'hardDeleteAction'
]);
/** FIN DE RUTA DE JOBS **/

/** RUTA DE PROJECTS **/
$map->get('addProjects', BASE_URL . 'projects/add', [
    'App\Controllers\ProjectsController',
    'getAddProject'
]);

$map->post('saveProjects', BASE_URL . 'projects/add', [
    'App\Controllers\ProjectsController',
    'postSaveProject'
]);
/** FIN DE RUTA DE PROJECTS **/

$map->get('addUser', BASE_URL . 'users/add', [
    'App\Controllers\UsersController',
    'getAddUser'
]);

$map->post('saveUsers', BASE_URL . 'users/add', [
    'App\Controllers\UsersController',
    'postSaveUser'
]);

$map->get('admin', BASE_URL . 'admin', [
    'App\Controllers\AdminController',
    'getIndex'
]);
// FIN RUTAS DE ADMIN


$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if (!$route) {
    echo('No route'); //Poner pag 404
} else {
//    $handlerData = $route->handler;
//    $controllerName = $handlerData['controller'];
//    $actionName = $handlerData['action'];

    $harmony = new Harmony($request, new Response());
    $harmony
        ->addMiddleware(new HttpHandlerRunnerMiddleware(new SapiEmitter()))
        ->addMiddleware(new AuthenticationMiddleware())
        ->addMiddleware(new AuraRouter($routerContainer))
        ->addMiddleware(new DispatcherMiddleware($container, 'request-handler'));
    $harmony();
}