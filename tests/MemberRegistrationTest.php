<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Provider\DoctrineServiceProvider;
use Silex\WebTestCase;

class MemberRegistrationTest extends WebTestCase
{

    private $db;
    private $member;

    public function __construct()
    {
        $app = new Silex\Application();
        $app->register(new DoctrineServiceProvider(), [
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

    /**
     * @test
     */
    public function memberRegistration()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/member/register');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(1, $crawler->filter('title:contains("会員登録")')->count());

        $form = $crawler->filter('#register_submit')->form();
        $data = [
            'member[email]' => 'test@test.com',
            'member[password]' => 'testpassword',
        ];
        $crawler = $client->submit($form, $data);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertSame(1, $crawler->filter('title:contains("会員登録完了")')->count());
    }
}
