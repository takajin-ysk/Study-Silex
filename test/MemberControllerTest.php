<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\WebTestCase;

class MemberControllerTest extends WebTestCase
{
    private $db;

    public function construct()
    {
        $app = new Silex\Application();
        $app->register(new Silex\Provider\DoctrineServiceProvider(), [
            'db.options' => [
                'driver' => 'pdo_mysql',
                'dbname' => 'silex',
                'host' => '127.0.0.1',
                'user' => 'root',
                'password' => null
            ],
        ]);

        $this->db = $app['db'];
        $this->db->exec("TRUNCATE TABLE member");
    }

    public function createApplication()
    {
        require __DIR__ . '/../index.php';
        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
    }

    public function testMemberRegistration()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', 'member/register');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(1, $crawler->filter('title:contains("会員登録")')->count());

        $form = $crawler->filter('#register_submit')->form();
        $data = [
            'member[email]' => 'sample@example.com',
            'member[password]' => 'sample',
        ];
        $crawler = $client->submit($form, $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(1, $crawler->filter('title:contains("会員登録完了")')->count());
    }
}