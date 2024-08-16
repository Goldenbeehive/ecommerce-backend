<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
 
 function handleGraphQL() {
    $config = [
        'database' => [
            'driver' => 'mysql',
            'host' => '',

            'database' => '',
            'username' => '',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]
    ];
    $dbConfig = $config['database'];
    $dsn = "{$dbConfig['driver']}:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
    $db = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $graphql = new App\Controller\GraphQL($db);
    return $graphql->handle();
}

// Route all requests to GraphQL
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute(['GET', 'POST'], '{path:.*}', 'handleGraphQL');
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
    case FastRoute\Dispatcher::FOUND:
        echo handleGraphQL();
        break;
}