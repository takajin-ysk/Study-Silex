<?php

use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application;
$app['debug'] = true;

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
    'twig_options' => ['debug' => true]
]);

// Routingなど
$app->get('/member/register', function(Request $request) use ($app) {
    $app['request'] = $request;

    return $app['twig']->render('member/register.twig');
});

// Silexアプリケーションの実行
$app->run();
