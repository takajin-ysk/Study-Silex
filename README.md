# Study-Silex
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

### Hello World
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

## 環境
macOS Sierra 10.12.6

PHP 5.6.30

Silex 2.0

Twig 2.4



## 参考
[Silex本家のDocumentation](https://silex.symfony.com/doc/2.0/)

[Twig本家のDocumentation](https://twig.symfony.com/doc/2.x/)
