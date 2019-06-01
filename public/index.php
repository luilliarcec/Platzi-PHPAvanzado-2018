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

use Illuminate\Database\Capsule\Manager as Capsule;
use Aura\Router\RouterContainer;

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
    'controller' => 'App\Controllers\IndexController',
    'action' => 'IndexAction'
]);

$map->get('loginForm', BASE_URL . 'login', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogin'
]);

$map->post('auth', BASE_URL . 'auth', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'postLogin'
]);

$map->get('logout', BASE_URL . 'logout', [
    'controller' => 'App\Controllers\AuthController',
    'action' => 'getLogout'
]);

// RUTAS DE ADMIN

/** RUTA DE JOBS **/
$map->get('indexJobs', BASE_URL . 'jobs', [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getIndex',
    'auth' => true
]);

$map->get('addJobs', BASE_URL . 'jobs/add', [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJobAction',
    'auth' => true
]);

$map->post('saveJobs', BASE_URL . 'jobs/add', [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJobAction',
    'auth' => true
]);
/** FIN DE RUTA DE JOBS **/

/** RUTA DE PROJECTS **/
$map->get('addProjects', BASE_URL . 'projects/add', [
    'controller' => 'App\Controllers\ProjectsController',
    'action' => 'getAddProject',
    'auth' => true
]);

$map->post('saveProjects', BASE_URL . 'projects/add', [
    'controller' => 'App\Controllers\ProjectsController',
    'action' => 'postSaveProject',
    'auth' => true
]);
/** FIN DE RUTA DE PROJECTS **/

$map->get('addUser', BASE_URL . 'users/add', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'getAddUser',
//    'auth' => true
]);

$map->post('saveUsers', BASE_URL . 'users/add', [
    'controller' => 'App\Controllers\UsersController',
    'action' => 'postSaveUser',
//    'auth' => true
]);

$map->get('admin', BASE_URL . 'admin', [
    'controller' => 'App\Controllers\AdminController',
    'action' => 'getIndex',
    'auth' => true
]);
// FIN RUTAS DE ADMIN


$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if (!$route) {
    echo('No route'); //Poner pag 404
} else {
    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];

    $needsAuth = $handlerData['auth'] ?? false;
    $session = $_SESSION['userId'] ?? null;

    if ($needsAuth && !$session) {
        $controllerName = 'App\Controllers\AuthController';
        $actionName = 'getLogout';
    } elseif ($actionName == 'getLogin' && $session) {
        $controllerName = 'App\Controllers\AuthController';
        $actionName = 'getAdmin';
    }

    $controller = new $controllerName;
    $response = $controller->$actionName($request);

    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }

    http_response_code($response->getStatusCode());
    echo($response->getBody());
}