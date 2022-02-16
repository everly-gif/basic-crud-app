# Utopia Swoole

[![Build Status](https://travis-ci.org/utopia-php/swoole.svg?branch=master)](https://travis-ci.org/utopia-php/preloader)
[![Discord](https://badgen.net/badge/discord/chat/green)](https://discord.gg/GSeTUeA)
![Total Downloads](https://img.shields.io/packagist/dt/utopia-php/swoole.svg)

An extension for Utopia Framework to work with [PHP Swoole](https://github.com/swoole/swoole-src) as a PHP FPM alternative. This library is aiming to be as simple and easy to learn and use.

This library is part of the [Utopia Framework](https://github.com/utopia-php/framework) project. You PHP should be compiled with the [PHP Swoole](https://github.com/swoole/swoole-src) extension for this library to work with Utopia PHP.

## Getting Started

Install using composer:
```bash
composer require utopia-php/swoole
```

```php
<?php

if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
}

use Utopia\App;
use Utopia\Swoole\Request;
use Utopia\Swoole\Response;
use Utopia\Swoole\Files;
use Swoole\Http\Server;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;

$http = new Server("0.0.0.0", 80);

Files::load(__DIR__ . '/../public'); // Static files location

$http->on('request', function (SwooleRequest $swooleRequest, SwooleResponse $swooleResponse) {
    $request = new Request($swooleRequest);
    $response = new Response($swooleResponse);

    if(Files::isFileLoaded($request->getURI())) { // output static files with cache headers
        $time = (60 * 60 * 24 * 365 * 2); // 45 days cache

        $response
            ->setContentType(Files::getFileMimeType($request->getURI()))
            ->addHeader('Cache-Control', 'public, max-age='.$time)
            ->addHeader('Expires', \date('D, d M Y H:i:s', \time() + $time).' GMT') // 45 days cache
            ->send(Files::getFileContents($request->getURI()))
        ;

        return;
    }

    $app = new App('Asia/Tel_Aviv');
    
    try {
        $app->run($request, $response);
    } catch (\Throwable $th) {
        $swooleResponse->end('500: Server Error');
    }
});

$http->start();

```

## System Requirements

Utopia Framework requires PHP 7.1 or later. We recommend using the latest PHP version whenever possible.

## Authors

**Eldad Fux**

+ [https://twitter.com/eldadfux](https://twitter.com/eldadfux)
+ [https://github.com/eldadfux](https://github.com/eldadfux)

## Copyright and license

The MIT License (MIT) [http://www.opensource.org/licenses/mit-license.php](http://www.opensource.org/licenses/mit-license.php)