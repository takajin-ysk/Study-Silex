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

            return $app['twig']->render('member/finish.twig', [
                'member' => $member
            ]);

        });
        return $controllers;
    }
}