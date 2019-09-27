<?php
use DI\ContainerBuilder;
use League\Plates\Engine;
use Aura\SqlQuery\QueryFactory;
use JasonGrimes\Paginator;
use app\controllers\register;
use app\models\QueryBuilder;
use Intervention\Image\ImageManager;



use Delight\Auth\Auth;


$builder = new ContainerBuilder();
$builder->addDefinitions([
    PDO::class => function () {
        include '../core/db.php';
        return new PDO("mysql:host={$config['connection']};dbname={$config['database']};charset={$config['charset']};", "{$config['username']}", "{$config['password']}");
    },
    Auth::class => function ($container) {
        return new Auth($container->get('PDO'));
    },
    Engine::class => function () {
        return new Engine('../app/views');
    },
    QueryBuilder::class => function () {
        return new QueryBuilder('../app/models');
    },
    ImageManager::class => function () {
        return new ImageManager(array('driver' => 'imagick'));
    },
    
    QueryFactory::class => function () {
        return new QueryFactory('mysql', QueryFactory::COMMON);
    },   
]);
$container = $builder->build();
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    include '../core/routes.php';
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        include '../app/views/404.php';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        include '../app/views/405.php';
        break;
    case FastRoute\Dispatcher::FOUND:
        $controller = $routeInfo[1];
        $vars = $routeInfo[2];
        $container->call($controller, array($vars));
        break;
}

?>