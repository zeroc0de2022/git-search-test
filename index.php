<?php
declare(strict_types = 1);
/*
Author: zeroc0de <98693638+zeroc0de2022@users.noreply.github.com>
*/
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

use DI\ContainerBuilder;
use DI\NotFoundException;
use Routim\Session;
use Slim\Factory\AppFactory;
use DevCoder\DotEnv;


require __DIR__ . '/vendor/autoload.php';
// Loading environment variables
(new DotEnv(__DIR__ . '/.env'))->load();

try {
    // Create new container builder DI
    $builder = new ContainerBuilder();
    // Passing dependency settings to the container
    $builder->addDefinitions('config/di.php');
    // Create new container
    $container = $builder->build();
    // For Slim to see and Use the container to create new objects
    AppFactory::setContainer($container);
    $app = AppFactory::create();
    $app->addBodyParsingMiddleware(); // middleware for POST
    $app->add(Session::class . ':sessionInit');

    // Defining handlers
    $routes = require __DIR__ . '/config/routes.php';
    foreach($routes as $route => $handler) {
        if(is_array($handler)) {
            [$class, $method] = $handler;
            $app->any($route, $class . ':' . $method);
        }
        else {
            $app->get($route, $handler);
        }
    }
    $app->run();
}
catch(Exception $exception) {
    echo __LINE__ . ': ' . $exception->getMessage();
}
