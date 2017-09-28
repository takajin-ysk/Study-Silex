<?php

use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\DoctrineServiceProvider;

require_once __DIR__ . '/vendor/autoload.php';

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

$app->get('/member/register', function(Request $request) use ($app) {
    $app['request'] = $request;

    return $app['twig']->render('member/register.twig');
});

$app->post('member/register', function (Request $request) use ($app) {
   $member = $request->get('member');

   $sql = "INSERT INTO member SET email = :email, password = :password, created_at = now(), updated_at = now()";
   $stmt = $app['db']->prepare($sql);

   $stmt->bindParam(':email', $member['email']);
   $password = md5($member['password']);
   $stmt->bindParam(':password', $password);

   $stmt->execute();

   return $app['twig']->render('member/finish.twig', [
       'member' => $member
    ]);
});

$app->get('/', function () use ($app) {
    return "";
});

$app->run();
