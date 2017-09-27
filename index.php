<?php

use Silex\Provider\TwigServiceProvider;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application;
$app['debug'] = true;

$app->register(new TwigServiceProvider(), [
    'twig.path' => 'views'
]);

// Routingなど
$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig',[
        'name' => 'やまだたろう'
    ]);
});

// Silexアプリケーションの実行
$app->run();
