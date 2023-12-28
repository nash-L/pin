<?php

namespace Nash\Pin;

use Error, Throwable, Hyperf\Server\Event;
use Nash\Pin\Core\{Application, Command, Config, Container, Crontab, Listener, Process, Server};
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};
use Nash\Pin\Command\Output;

/**
 * addCrontab
 */
final class App
{
    /** @var int */
    const MODE_BASE = Application::MODE_BASE, MODE_PROCESS = Application::MODE_PROCESS, MODE_COROUTINE = Application::MODE_COROUTINE;
    const TYPE_HTTP = Server::TYPE_HTTP, TYPE_WEBSOCKET = Server::TYPE_WEBSOCKET, TYPE_TCP = Server::TYPE_TCP, TYPE_UDP = Server::TYPE_UDP;

    /** @var string */
    const DEFAULT_SERVER = Server::DEFAULT_SERVER;
    const EVENT_ON_START = Event::ON_START, EVENT_ON_WORKER_START = Event::ON_WORKER_START, EVENT_ON_WORKER_STOP = Event::ON_WORKER_STOP
        , EVENT_ON_WORKER_EXIT = Event::ON_WORKER_EXIT, EVENT_ON_WORKER_ERROR = Event::ON_WORKER_ERROR, EVENT_ON_BEFORE_START = Event::ON_BEFORE_START
        , EVENT_ON_PIPE_MESSAGE = Event::ON_PIPE_MESSAGE, EVENT_ON_TASK = Event::ON_TASK, EVENT_ON_FINISH = Event::ON_FINISH
        , EVENT_ON_SHUTDOWN = Event::ON_SHUTDOWN, EVENT_ON_MANAGER_START = Event::ON_MANAGER_START, EVENT_ON_MANAGER_STOP = Event::ON_MANAGER_STOP
        , EVENT_ON_REQUEST = Event::ON_REQUEST, EVENT_ON_RECEIVE = Event::ON_RECEIVE, EVENT_ON_CONNECT = Event::ON_CONNECT
        , EVENT_ON_HAND_SHAKE = Event::ON_HAND_SHAKE, EVENT_ON_OPEN = Event::ON_OPEN, EVENT_ON_MESSAGE = Event::ON_MESSAGE
        , EVENT_ON_CLOSE = Event::ON_CLOSE, EVENT_ON_PACKET = Event::ON_PACKET;

    /**
     * @param int $mode
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function setMode(int $mode): void
    {
        if (in_array($mode, [self::MODE_BASE, self::MODE_COROUTINE, self::MODE_PROCESS]))
            self::application()->setMode($mode);
    }

    /**
     * @param int $type
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function setType(int $type): void
    {
        if (in_array($type, [self::TYPE_HTTP, self::TYPE_WEBSOCKET, self::TYPE_UDP, self::TYPE_TCP]))
            self::server()->setType($type);
    }

    /**
     * @param int $port
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function setPort(int $port): void
    {
        self::server()->setPort($port);
    }

    /**
     * @param string $host
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function setHost(string $host): void
    {
        self::server()->setHost($host);
    }

    /**
     * @param string $event
     * @param callable $call
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function on(string $event, callable $call): void
    {
        if (in_array($event, [
            self::EVENT_ON_START, self::EVENT_ON_WORKER_START, self::EVENT_ON_WORKER_STOP, self::EVENT_ON_WORKER_EXIT, self::EVENT_ON_WORKER_ERROR, self::EVENT_ON_BEFORE_START,
            self::EVENT_ON_PIPE_MESSAGE, self::EVENT_ON_TASK, self::EVENT_ON_FINISH, self::EVENT_ON_SHUTDOWN, self::EVENT_ON_MANAGER_START, self::EVENT_ON_MANAGER_STOP,
        ]))
            self::application()->on($event, $call);
        elseif (in_array($event, [
            self::EVENT_ON_REQUEST, self::EVENT_ON_RECEIVE, self::EVENT_ON_CONNECT, self::EVENT_ON_HAND_SHAKE, self::EVENT_ON_OPEN,
            self::EVENT_ON_MESSAGE, self::EVENT_ON_CLOSE, self::EVENT_ON_PACKET,
        ]))
            self::server()->on($event, $call);
    }

    /**
     * @param array|string $prefix
     * @param callable $callback
     * @param callable|string ...$middlewares
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function group(array|string $prefix, callable $callback, callable|string ...$middlewares): void
    {
        self::server()->group($prefix, $callback, ...$middlewares);
    }

    /**
     * @param string|array $methods
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function route(string|array $methods, string $route, callable|array|string $handler, callable|string ...$middlewares): void
    {
        self::server()->route($methods, $route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function get(string $route, callable|array|string $handler, callable|string ...$middlewares): void
    {
        self::server()->get($route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function post(string $route, callable|array|string $handler, callable|string ...$middlewares): void
    {
        self::server()->post($route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function put(string $route, callable|array|string $handler, callable|string ...$middlewares): void
    {
        self::server()->put($route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function delete(string $route, callable|array|string $handler, callable|string ...$middlewares): void
    {
        self::server()->delete($route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function patch(string $route, callable|array|string $handler, callable|string ...$middlewares): void
    {
        self::server()->patch($route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function head(string $route, callable|array|string $handler, callable|string ...$middlewares): void
    {
        self::server()->head($route, $handler, ...$middlewares);
    }

    /**
     * @param callable|string $callback
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function addMiddleware(callable|string $callback): void
    {
        self::server()->addMiddleware($callback);
    }

    /**
     * @param callable|string $callback
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function addExceptionHandler(callable|string $callback): void
    {
        self::server()->addExceptionHandler($callback);
    }

    /**
     * @return Application
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function application(): Application
    {
        return Application::instance();
    }

    /**
     * @param string $processName
     * @return Process
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function process(string $processName): Process
    {
        return Process::instance($processName);
    }

    /**
     * @param string $eventName
     * @return Listener
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function listener(string $eventName): Listener
    {
        return Listener::instance($eventName);
    }

    /**
     * @param string $commandName
     * @return Command
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function command(string $commandName): Command
    {
        return Command::instance($commandName);
    }

    /**
     * @param string $name
     * @return Server
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function server(string $name = self::DEFAULT_SERVER): Server
    {
        return Server::instance($name);
    }

    /**
     * @return Config
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function config(): Config
    {
        return Config::instance();
    }

    /**
     * @return Container
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function container(): Container
    {
        return Container::instance();
    }

    /**
     * @param string|null $name
     * @return Crontab
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function crontab(?string $name = null): Crontab
    {
        return Crontab::instance($name ?? uniqid('crontab_'));
    }

    /**
     * @param Throwable $throwable
     * @param Output|null $output
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function printThrowable(Throwable $throwable, ?Output $output = null): void
    {
        $output = $output ?? Application::instance()->getOutput();
        $type = $throwable instanceof Error ? 'error' : 'exception';
        $output->getErrorOutput()->writeln(sprintf(
            '<error>PHP Fatal %s:  Uncaught %s: %s in %s:%d</error>',
            $type, get_class($throwable), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine()
        ));
        $output->getErrorOutput()->writeln('<error>  Stack trace:</error>');
        array_map(
            fn($message)=>$output->getErrorOutput()->writeln(sprintf('<error>  %s</error>', $message)),
            explode("\n", $throwable->getTraceAsString())
        );
        $output->getErrorOutput()->writeln(sprintf(
            '<error>    thrown in %s on line %d</error>',
            $throwable->getFile(), $throwable->getLine()
        ));
    }
}
