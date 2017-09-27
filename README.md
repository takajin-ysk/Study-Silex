# Study-Silex
## 概要
Silex勉強用に作成したレポジトリ。
Hello Worldからログイン機能、セッション管理まで。

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

## 環境
macOS Sierra 10.12.6

PHP 5.6.30

Silex 2.0

Twig 2.4



## 参考
[Silex本家のDocumentation](https://silex.symfony.com/doc/2.0/)

[Twig本家のDocumentation](https://twig.symfony.com/doc/2.x/)
