<?php

use Composer\Autoload\ClassLoader;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Engine\{DefaultOption, Extension};
use Hyperf\HttpServer\Contract\ResponseInterface;
use Nash\Pin\Core\{Application, Command, Config, Container, Crontab, Listener, Process, Server};
use Psr\Log\LogLevel;

$GLOBALS['__ENTER_POINT__'] = new class {
    public function __construct(private bool $hasError = false)
    {
        $this->catchThrowable();
        $this->checkExtension();
        $this->init();
    }

    function catchThrowable(): void
    {
        set_error_handler([$this, 'onThrowable']);
        set_exception_handler([$this, 'onThrowable']);
    }

    function checkExtension(): void
    {
        if (!extension_loaded('Swoole')) {
            if (!extension_loaded('Swow'))
                throw new RuntimeException('Need `Swow` or `Swoole` ext');
            if (!Extension::isLoaded())
                throw new RuntimeException('Need `hyperf/engine-swow` composer component. Use `composer require hyperf/engine-swow` to install it');
            define('SYSTEM_ENGINE', 'swow');
        } else {
            define('SYSTEM_ENGINE', 'swoole');
        }
        defined('BASE_PATH') || define('BASE_PATH', dirname((new ReflectionClass(ClassLoader::class))->getFileName(), 3));
        defined('SWOOLE_HOOK_FLAGS') || define('SWOOLE_HOOK_FLAGS', DefaultOption::hookFlags());
    }

    function init(): void
    {
        ini_set('display_errors', 'on');
        ini_set('display_startup_errors', 'on');
        ini_set('upload_max_filesize', '128M');
        ini_set('post_max_size', '128M');
        ini_set('memory_limit', '1G');
        ini_set('swoole.use_shortname', 'Off');
        error_reporting(E_ALL);
        class_alias('Nash\\Pin\\App', 'App');
    }

    function onThrowable(Throwable $throwable): void
    {
        App::printThrowable($throwable);
        $this->hasError = true;
    }

    /**
     * @param string $className
     * @return self
     */
    function applyConfig(string $className): self
    {
        array_map(
            fn(Listener|Command|Process|Crontab $classObject)=>$classObject->applyConfig(),
            Container::instance()->getMap($className)
        );
        Container::instance()->unbindMap($className);
        return $this;
    }

    function applyServer():self
    {
        if (empty(array_map(
            fn(Server $server)=>$server->addExceptionHandler([App::class, 'printThrowable'])->applyConfig(),
            Container::instance()->getMap(Server::class)))
        ) // 如果没有定义任何服务，将添加一个Hello world服务
            Server::instance(Server::DEFAULT_SERVER)->addExceptionHandler([App::class, 'printThrowable'])
                ->get('/', fn(ResponseInterface $response)=>$response->write('Hello World!'))->applyConfig();
        Container::instance()->unbindMap(Server::class);
        return $this;
    }

    function __destruct()
    {
        restore_error_handler();
        restore_exception_handler();
        if ($this->hasError)
            return;
        Application::instance()->applyConfig();
        $this->applyServer()->applyConfig(Listener::class)->applyConfig(Command::class)
            ->applyConfig(Process::class)->applyConfig(Crontab::class);
        Config::instance()->append(StdoutLoggerInterface::class . '.log_level', LogLevel::INFO);
        Application::instance()->run();
    }
};
