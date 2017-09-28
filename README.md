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

## 簡単なWebページの作成
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

### ディレクトリ構成
```
Study-Silex
├── README.md
├── composer.json
├── composer.lock
├── index.php
├── vendor
└── views
    ├── index.twig
    └── layout.twig
```

### Hello World
#### とりあえず表示
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


## 参考
[Silex本家のDocumentation](https://silex.symfony.com/doc/2.0/)

[Twig本家のDocumentation](https://twig.symfony.com/doc/2.x/)
