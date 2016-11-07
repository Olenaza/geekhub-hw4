<?php

require_once 'vendor/autoload.php';
require_once 'configuration.php';

$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'universities';
$controllerName = ucfirst($controllerName).'Controller';
$controllerName = 'Controllers\\'.$controllerName;

$connector = new Repositories\Connector(
    $configuration['database'],
    $configuration['user'],
    $configuration['password']
);

$controller = new $controllerName($connector);

$actionName = isset($_GET['action']) ? $_GET['action'] : 'index';
$actionName = $actionName.'Action';

$response = $controller->$actionName();

echo $response;
