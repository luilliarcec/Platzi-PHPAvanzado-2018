<?php

require_once('../vendor/autoload.php');

session_start();

$dotenv = Dotenv\Dotenv::create(__DIR__ . '/..');
$dotenv->load();

// Inicio bloque para mostrar errores (Solos ambiente de DESARROLLO)
if (getenv('DEBUG') === 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
// Fin bloque de errores

define('BASE_URL', '/');

use App\Middlewares\AuthenticationMiddleware;
use Franzl\Middleware\Whoops\WhoopsMiddleware;
use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;
use Middlewares\AuraRouter;
use WoohooLabs\Harmony\Harmony;
use WoohooLabs\Harmony\Middleware\DispatcherMiddleware;
use WoohooLabs\Harmony\Middleware\HttpHandlerRunnerMiddleware;
use Zend\Diactoros\Response;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('app');
$log->pushHandler(new StreamHandler(__DIR__ . '/../logs/app.log', Logger::WARNING));

$container = new DI\Container();
$errorsResponse = new \App\Controllers\BaseController();

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
    $emitter = new SapiEmitter();
    $emitter->emit($errorsResponse->renderHTML('errors.twig', [
        'code' => 404,
        'title' => 'Página no encontrada',
        'message' => 'Está página está fuera de nuestra galaxia :O. ¡No podemos acceder a ella :C!.'
    ]), 404);
} else {
    try {
        $harmony = new Harmony($request, new Response());
        $harmony
            ->addMiddleware(new HttpHandlerRunnerMiddleware(new SapiEmitter()));
        if (getenv('DEBUG') === 'true') {
            $harmony->addMiddleware(new WhoopsMiddleware());
        }
        $harmony
            ->addMiddleware(new AuthenticationMiddleware())
            ->addMiddleware(new AuraRouter($routerContainer))
            ->addMiddleware(new DispatcherMiddleware($container, 'request-handler'));
        $harmony();
    } catch (Exception $e) {
        $log->warning($e->getMessage());
        $emitter = new SapiEmitter();
        $emitter->emit($errorsResponse->renderHTML('errors.twig', [
            'code' => 400,
            'title' => 'Página no encontrada',
            'message' => 'Se a especificado un valor que no se a encontrado.'
        ]), 400);
    } catch (Error $e) {
        $log->error($e->getMessage());
        $emitter = new SapiEmitter();
        $emitter->emit($errorsResponse->renderHTML('errors.twig', [
            'code' => 500,
            'title' => 'Error en el servidor',
            'message' => 'Estamos trabajando para corregir este error.'
        ]), 400);
    }
}