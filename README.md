# Study-Silex
## 概要
Silex勉強用に作成したレポジトリ。
Hello WorldからDBへの登録、ログイン機能、セッション管理までが目標。

## 環境
macOS Sierra 10.12.6

PHP 5.6.30

Silex 2.0

Twig 2.4

MySQL 5.7.19

### 準備
 `composer.json` を作成し、silexとtwigをインストールする。

```json
{
  "require": {
    "silex/silex": "~1.2|~2.0",
    "twig/twig": "~1.34|~2.4"
  }
}
```

```bash 
$ composer install
```

## Hello World
`index.php` を作成する。

```php
<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application;

// Routingなど
$app->get('/', function() use ($app) {
  return 'Hello, World!!';
});

// Silexアプリケーションの実行
$app->run();
?>
```

#### Twigを使ってViewを作ってみる
view用のファイルをtwigを使って作成する。

```
{# layout.twig #}
<!DOCTYPE html>
<html lang="ja">
  <head>
    <!-- head情報を記述 -->
  </head>
  <body>
    {% block body %}{% endblock %}
  </body>
</html>
```

```
{# index.twig #}
{% extends 'layout.twig' %}

{% block body %}
  <h1>こんにちは、{{ name }}さん！</h1>
  <p>indexファイルが表示されています。</p>
{% endblock %}
```

twigを使うように`index.php`を書き換える。
`$app['twig']`にService Providerが登録されていて、
`render`メソッドで対象テンプレート(`index.twig`)を指定して
テンプレートをレンダリングする。	
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application;
$app['debug'] = true;

$app->register(new \Silex\Provider\TwigServiceProvider(), [
    'twig.path' => '.'
]);

$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig',[
        'name' => 'やまだたろう'
    ]);
});

$app->run();
```

## DBへの登録・更新・参照
### DB設定

```mysql
-- データベース作成
CREATE DATABASE silex DEFAULT CHARACTER SET utf8;
 
-- テーブル作成
CREATE TABLE `silex`.`member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
-- ユーザー作成
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' IDENTIFIED BY 'admin' WITH GRANT OPTION;
```

### 会員登録フォームの表示
/member/register に GET でリクエストした際に会員登録フォームを表示する。

会員登録用のテンプレートを作成する。

```twig
{# /views/member/register.twig #}

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <title>会員登録</title>
</head>
<body>
<form action="{{ app.request.baseUrl }}/member/register" method="post" class="well">
    <div>
        メールドレス
        <input type="text" name="member[email]" value="" />
    </div>
    <div>
        パスワード
        <input type="password" name="member[password]" value="" />
    </div>
    <button type="submit" class="btn">登録</button>
</form>
</body>
</html>
```

`index.php`を下記のように変更する。

```php
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

$app->get('/member/register', function(Request $request) use ($app) {
    $app['request'] = $request;

    return $app['twig']->render('member/register.twig');
});

$app->run();
```

http://localhost/member/register にアクセスして動作確認を行う。

### 会員登録ロジックの作成
この画面から POST リクエストを送信し、DBに情報を保存するロジックを作成する。

#### 準備
DBアクセスにはDoctrineを使用するため、`composer.json`を編集する。

```json
{
  "require": {
    "silex/silex": "~1.2|~2.0",
    "twig/twig": "~1.34|~2.4",
    "doctrine/dbal": "~2.5",
    "doctrine/common": "~2.7"
  }
}
```

`index.php`を編集する。

```php
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

$app->run();
```

会員登録完了画面のテンプレートを作成する。

```twig
{# /views/member/finish.twig #}

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <title>会員登録完了</title>
</head>
<body>
<div>
    メールドレス
    {{ member.email }}
</div>
<div>
    パスワード
    {{ member.password }}
</div>
</body>
</html>
```

http://localhost/member/register にアクセスし、登録フォームからPOSTの動作確認を行う。

DBの中身も確認しておく。

```bash
mysql> select * from member;
+----+-----------------+----------------------------------+---------------------+---------------------+
| id | email           | password                         | created_at          | updated_at          |
+----+-----------------+----------------------------------+---------------------+---------------------+
|  1 | admin@localhost | 21232f297a57a5a743894a0e4a801fc3 | 2017-09-28 11:32:27 | 2017-09-28 11:32:27 |
+----+-----------------+----------------------------------+---------------------+---------------------+
1 row in set (0.00 sec)
```

## テスト＆リファクタリング
`index.php`にロジックが集中してしまいメンテナンスしづらくなるので、
テストを書いた上でMVCモデルにリファクタリングする。

### 準備
`composer.json`を下記のように編集する。

```json
{
  "repositories": [
    {
      "type": "pear",
      "url": "http://pear.symfony-project.com"
    },
    {
      "type": "pear",
      "url": "http://pear.phpunit.de"
    }
  ],
  "require": {
    "silex/silex": "~1.2|~2.0",
    "twig/twig": "~1.34|~2.4",
    "doctrine/dbal": "~2.5",
    "doctrine/common": "~2.7",
    "symfony/browser-kit": "~3.3",
    "symfony/css-selector": "~3.3"
  },
  "require-dev": {
    "phpunit/phpunit": "*"
  },
  "autoload": {
    "psr-0": {
      "StudySilex": "source/"
    }
  }
}
```

### テストの作成

```php
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
```

なぜか / のルーティングがないとエラーになるので`index.php`に下記を追記する。
```php
$app->get('/', function () use ($app) {
    return "";
});
```

テストを実行する。
```bash
$ ./vendor/bin/phpunit test
PHPUnit 5.7.22 by Sebastian Bergmann and contributors.

.                                                                   1 / 1 (100%)

Time: 107 ms, Memory: 10.00MB

OK (1 test, 4 assertions)
```

### リファクタリング
#### コントローラ
##### ControllerProviderの作成
`MemberControllerProvider`を作成する。

```php
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

            $sql = "INSERT INTO member SET email = :email, password = :password, created_at = now(), updated_at = now()";
            $statement = $app['db']->prepare($sql);
            $statement->bindParam(':email', $member['email']);
            $password = md5($member['password']);
            $statement->bindParam(':password', $password);

            $statement->execute();
                      
            return $app['twig']->render('member/finish.twig', [
                'member' => $member
            ]);

        });
        return $controllers;
    }
}
```

`index.php`を、`mount`メソッドを使用してコントローラを読み込むよう修正する。

```php
<?php

use Silex\Provider\TwigServiceProvider;
use StudySilex\Provider\MemberControllerProvider;
use Silex\Provider\DoctrineServiceProvider;
use StudySilex\Provider\MemberServiceProvider;

require_once __DIR__ . '/vendor/autoload.php';

//TODO namespaceが効いたら下記は削除
require_once __DIR__ . '/source/Provider/MemberControllerProvider.php';

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

$app->mount('/member', new MemberControllerProvider());

$app->get('/', function () use ($app) {
    return "";
});

$app->run();
```

#### モデル
`index.php`にDB更新ロジックが書かれているので、これをモデルに移動させる。

##### サービスモデルの作成
ここで、ついでにパスワードのハッシュ化を行うメソッドも分離させる。

```php
<?php

namespace StudySilex\Service;

class Member
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function register($data)
    {
        try{
            $this->db->beginTransaction();
            $id = $this->db->lastInsertId();
            $sql = "INSERT INTO member SET email = :email, password = :password, created_at = now(), updated_at = now()";
            $statement = $this->db->prepare($sql);
            $statement->bindParam(':email', $data['email']);
            $statement->bindParam(':password', $this->hashPassword($id, $data['password']));

            $statement->execute();

        }
        catch (Exception $e)
        {
            $this->db->rollback();
            throw $e;
        }

        $this->db->commit();
    }

    /**
     * @param $id
     * @return string
     */
    private function getSalt($id)
    {
        return md5($id);
    }

    /**
     * @param $id
     * @param $password
     * @return string
     */
    private function hashPassword($id, $password)
    {
        $salt = $this->getSalt($id);
        $hash = '';
        for($i = 0; $i < 1024; $i++)
        {
            $hash = hash('sha256', $hash . $password . $salt);
        }

        return $hash;
    }
}
```
##### ServiceProviderの作成
`MemberServiceProvider`を作成する。

```php
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
```

`index.php`に`MemberServiceProvider`を登録する。
```php
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
```

`MemberControllerProvider.php`で`Member.php`の`register`メソッドを呼び出す。
```php
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
```

最終的なディレクトリ構成は下記のようになった。
```
.
├── README.md
├── composer.json
├── composer.lock
├── index.php
├── source
│   ├── Provider
│   │   ├── MemberControllerProvider.php
│   │   └── MemberServiceProvider.php
│   └── Service
│       └── Member.php
├── test
│   └── MemberControllerTest.php
├── vendor
└── views
    └── member
        ├── finish.twig
        └── register.twig
```
## 参考
[Silex本家のDocumentation](https://silex.symfony.com/doc/2.0/)

[Twig本家のDocumentation](https://twig.symfony.com/doc/2.x/)
