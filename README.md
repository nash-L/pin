# Pin, by Nash

Pin 是一款零配置、无骨架、极小化的 Hyperf 发行版，它的灵感来源于 [hyperf/nano](https://github.com/hyperf/nano)。

## 设计思路

Hyperf 是一款十分优秀的基于 Swoole 或 Swow 的框架，但是对于初学者来说，可能稍微有些复杂了，nano 则为初学者使用 Hyperf 提供了极大的便利。但其对开发者依然存在一定的基础素质要求，某些场景的使用即使是老手也要愣一下神。于是，为了贯彻 nano 的设计理念，从而自行设计了本项目，真正实现忽略框架细节，专注业务代码的理念。

## 特性

* 无骨架
* 零配置
* 快速启动
* 闭包风格
* 支持注解外的全部 Hyperf 功能
* 兼容全部 Hyperf 组件
* Phar 友好

## 安装

```bash
composer require nash/pin
```

## 快速开始

### hello world 服务

在项目根目录打开命令行，执行以下命令

```bash
php -r "require 'vendor/autoload.php';" start
```

您已经开启了一个可以输出 hello world 的 HTTP 服务，在浏览器打开 `http://127.0.0.1:9501` 就可以看到您的项目在跟这个世界问好。

### 定义自己的服务

在项目根目录创建文件 `index.php` ，内容如下：

```php
<?php
require __DIR__ . '/vendor/autoload.php';

(function () {
    App::get('/', fn()=>'Hello World Too !!!');
})();
```

然后，在项目根目录打开命令行，执行以下命令

```bash
php index.php start
```

你的服务依然开起来了，在浏览器打开 `http://127.0.0.1:9501` 就可以看到您的项目再次跟这个世界问好。

可以看到，相比 nano ，pin 显得更为简洁，简洁到创建应用和启动应用的步骤也给您省略掉了，您只需要去定义你的服务拥有哪些业务接口，拥有哪些执行流程即可。

## 路由

pin 和 nano 一样，支持 Hyperf 路由器的所有方法

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::group('/api', function () {
        App::route(['GET', 'POST'], '/a', fn()=>['id' => 1]);
        App::get('/b', fn()=>['id' => 2]);
        App::post('/c', fn()=>['id' => 3]);
        App::put('/d', fn()=>['id' => 4]);
    });
})();
```

`Hyperf` 的 `addGroup` 方法在此处被定义为 `group` 方法，`addRoute` 方法在此处被定义为 `route` 方法。

## 中间件

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::addMiddleware(fn(callable $next) => $next());
    App::get('/', fn()=>['id' => 1], fn(callable $next)=>$next(), fn(callable $next)=>$next());
})();
```

路由定义的方法中，第三个参数以及之后的所有参数均作为路由中间件被使用，这与 `Hyper` 和 `nano` 有些许的不同，因为 `middleware` 这个单词太长了，而且在 `Hyperf` 中几乎就没有其他的可配置项了。

另外，中间件的方法入参也从原本的两个参数更换成一个回调方法的入参了，`Hyperf` 中间件的第一个参数 `Psr\Http\Message\ServerRequestInterface $request` 容易和控制器的 `Hyperf\HttpServer\Contract\RequestInterface $request` 混淆，这里也是我当初愣神的地方，因此，干脆将这个对象去掉了，将两个请求对象的操作差异抹平即可，所以也就不需要服务请求参数了。

## DI容器

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::get('/', function (\Nash\Pin\Server\Request $request) {
        $request = \Nash\Pin\Server\Request::instance();
        // or
        $request = \Nash\Pin\Core\Container::instance()->get(\Nash\Pin\Server\Request::class);
    });
})();
```

请求类使用 `\Nash\Pin\Server\Request` 的实例来操作，它抹平了 `Psr\Http\Message\ServerRequestInterface` 和 `Hyperf\HttpServer\Contract\RequestInterface` 的差异，因此，它既可以在路由方法中使用，也可以在中间件中使用。

所以系统提供的类都可以使用 `instance` 静态方法来获取，也可以通过 `Container` 实例的 `get` 方法获取，你甚至可以完全使用单例模式惯用的操作来获取实例。

因为支持这种模式，所以也就不支持像 `nano` 那样将回调方法绑定到 `Hyperf\Nano\ContainerProxy` 的操作，我总觉得这样对 `IDE` 不够友好。

## 命令行

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::command('demo:hello')
        ->setDescription('hello 命令')
        ->addArgument('name', \Nash\Pin\Command\Input::ARGUMENT_OPTIONAL, '名称', 'World')
        ->setCallback(function () {
            \Nash\Pin\Command\Output::instance()->writeln('Hello ' . \Nash\Pin\Command\Input::instance()->getArgument('name') . '!');
        });
})();
```

## 异常处理

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::addExceptionHandler(function (Throwable $throwable) {
        App::printThrowable($throwable);
    });
})();
```

## 事件监听

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {

    class MyEvent { public function __construct(public string $name) {} }

    App::listener(MyEvent::class)->setCallback(function (MyEvent $event) {
        var_dump($event);
    });

    App::command('demo')->setCallback(function () {
        \Nash\Pin\Listener\EventDispatcher::instance()->dispatch(new MyEvent('test'));
    });
})();
```

## 自定义进程

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::process('test')->setCallback(function () {
        while (true) {
            var_dump(date('Y-m-d H:i:s'));
            sleep(1);
        }
    });
})();
```

## 定时任务

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::crontab('test')->setRule('* * * * *')->setCallback(function () {
        var_dump(date('Y-m-d H:i:s'));
    });
})();
```

## 使用协程服务端

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::setMode(\Nash\Pin\Core\Application::MODE_COROUTINE);
})();
```

## 使用Swow

框架默认使用 `Swoole` 引擎，如果没有安装 `Swoole` 扩展，则会降级使用 `Swow`， 此时，只需要安装Swow兼容层（官方的Swow兼容在使用UDP协议服务时，存在BUG，正在与官方沟通解决）即可：

```bash
composer require hyperf/engine-swow
```

## 监听其他协议

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    // TCP
    App::setType(\Nash\Pin\Core\Server::TYPE_TCP);
    // UDP
    App::setType(\Nash\Pin\Core\Server::TYPE_UDP);
    // WEBSOCKET
    App::setType(\Nash\Pin\Core\Server::TYPE_WEBSOCKET);
})();
```

## 同时监听多个服务端口

```php
<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::server('default')->get('/', fn() => 'default server');
    App::server('api')->setPort(9502)->get('/', fn() => 'api server');
})();
```

