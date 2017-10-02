<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application;
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
    'twig_options' => ['debug' => true]
]);

$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'driver' => 'pdo_mysql',
        'dbname' => 'silex',
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => null
    ]
]);

$app->register(new StudySilex\Provider\MemberServiceProvider());
$app->mount('/member', new StudySilex\Provider\MemberControllerProvider());

$app->get('/', function () use ($app) {
    return "";
});

$app->run();
