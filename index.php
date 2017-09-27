<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application;
$app['debug'] = true;

// Routingなど
$app->get('/', function() use ($app) {
    return 'Hello, World!!';
});

// Silexアプリケーションの実行
$app->run();
