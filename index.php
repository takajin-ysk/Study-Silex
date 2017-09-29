<?php

use Silex\Provider\TwigServiceProvider;
use StudySilex\Provider\MemberControllerProvider;
use Silex\Provider\DoctrineServiceProvider;
use StudySilex\Provider\MemberServiceProvider;

require_once __DIR__ . '/vendor/autoload.php';

//TODO namespaceが効いたら下記は削除
require_once __DIR__ . '/source/Provider/MemberControllerProvider.php';
require_once __DIR__ . '/source/Provider/MemberServiceProvider.php';

$app = new Silex\Application;
$app['debug'] = true;

$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/views',
    'twig_options' => ['debug' => true]
]);

$app->register(new DoctrineServiceProvider(), [
    'db.options' => [
        'driver' => 'pdo_mysql',
        'dbname' => 'silex',
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => null
    ]
]);

$app->register(new MemberServiceProvider());
$app->mount('/member', new MemberControllerProvider());

$app->get('/', function () use ($app) {
    return "";
});

$app->run();
