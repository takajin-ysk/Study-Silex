<?php

namespace StudySilex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use StudySilex\Service\Member;

require_once __DIR__ . '/../../source/Service/Member.php';

class MemberServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['member'] = function () use ($app) {
            return new Member($app['db']);
        };
    }
}