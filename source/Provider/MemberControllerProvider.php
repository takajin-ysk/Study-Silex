<?php

namespace StudySilex\Provider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class MemberControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/register', function (Request $request) use ($app) {
            $app['request'] = $request;
            return $app['twig']->render('member/register.twig');
        });

        $controllers->post('/register', function (Request $request) use ($app) {
            $member = $request->get('member');

            $app['member']->register($member);
//            $sql = "INSERT INTO member SET email = :email, password = :password, created_at = now(), updated_at = now()";
//            $statement = $app['db']->prepare($sql);
//            $statement->bindParam(':email', $member['email']);
//            $password = md5($member['password']);
//            $statement->bindParam(':password', $password);
//
//            $statement->execute();

            return $app['twig']->render('member/finish.twig', [
                'member' => $member
            ]);

        });
        return $controllers;
    }
}