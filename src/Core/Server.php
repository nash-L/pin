<?php

namespace Nash\Pin\Core;

use Nash\Pin\Server\ExceptionHandler;
use Nash\Pin\Server\Middleware;
use Nash\Pin\Server\Router;
use swow\Socket, RuntimeException;
use Hyperf\Server\{Event, ServerInterface};
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};

/**
 * @method self route(string|array $methods, string $route, callable|array|string $handler, callable|string ...$middlewares)
 * @method self get(string $route, callable|array|string $handler, callable|string ...$middlewares)
 * @method self post(string $route, callable|array|string $handler, callable|string ...$middlewares)
 * @method self put(string $route, callable|array|string $handler, callable|string ...$middlewares)
 * @method self delete(string $route, callable|array|string $handler, callable|string ...$middlewares)
 * @method self patch(string $route, callable|array|string $handler, callable|string ...$middlewares)
 * @method self head(string $route, callable|array|string $handler, callable|string ...$middlewares)
 * @method self group(array|string $prefix, callable $callback, callable|string ...$middlewares)
 */
class Server
{
    use StaticNameObjectTrait;

    /** @var string */
    const DEFAULT_SERVER = 'default';
    const TYPE_HTTP = 3, TYPE_WEBSOCKET = 4, TYPE_TCP = 2, TYPE_UDP = 1;

    /** @var string */
    protected string $serverName;

    /** @var int */
    protected int $type;

    /** @var string */
    protected string $host;

    /** @var int */
    protected int $port;

    /** @var array */
    protected array $eventCallBacks;

    /** @var Router */
    protected Router $router;

    /**
     * @param string $serverName
     */
    public function __construct(string $serverName)
    {
        $this->setServerName($serverName);
        $this->setType(self::TYPE_HTTP);
        $this->setPort(9501);
        $this->setHost('0.0.0.0');
        $this->setRouter(new Router($serverName));
        $this->eventCallBacks = [];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return self
     */
    public function __call(string $name, array $arguments): self
    {
        $router = $this->getRouter();
        if (method_exists($router, $name)) {
            $router->{$name}(...$arguments);
        }
        return $this;
    }

    /**
     * @param string $event
     * @param callable $call
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function on(string $event, callable $call): void
    {
        if (in_array($event, [
            Event::ON_REQUEST, Event::ON_RECEIVE, Event::ON_CONNECT, Event::ON_HAND_SHAKE, Event::ON_OPEN,
            Event::ON_MESSAGE, Event::ON_CLOSE, Event::ON_PACKET,
        ]))
            $this->eventCallBacks[$event] = CallableTransform::create($call, true);
    }

    /**
     * @param callable|string $callback
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addMiddleware(callable|string $callback): self
    {
        ($middleware = Middleware::create($callback)) && Config::instance()->append('middlewares.' . $this->getServerName(), $middleware);
        return $this;
    }

    /**
     * @param callable|string $callback
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function addExceptionHandler(callable|string $callback): self
    {
        ($exceptionHandler = ExceptionHandler::create($callback)) && Config::instance()->append('exceptions.handler.' . $this->getServerName(), $exceptionHandler);
        return $this;
    }

    /**
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function applyConfig(): self
    {
        switch ($this->getType()) {
            case self::TYPE_UDP: $this->applyUdpConfig(); break;
            case self::TYPE_TCP: $this->applyTcpConfig(); break;
            case self::TYPE_HTTP: $this->applyHttpConfig(); break;
            case self::TYPE_WEBSOCKET: $this->applyWebsocketConfig(); break;
        }
        return $this;
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function applyUdpConfig(): void
    {
        $config = [
            'host' => $this->getHost(), 'port' => $this->getPort(),
            'name' => $this->getServerName(), 'type' => ServerInterface::SERVER_BASE,
            'callbacks' => array_intersect_key($this->eventCallBacks, [Event::ON_PACKET => null]),
        ];
        if (SYSTEM_ENGINE === 'swoole')
            $config['sock_type'] = SWOOLE_SOCK_UDP;
        elseif (SYSTEM_ENGINE === 'swow')
            $config['sock_type'] = Socket::TYPE_UDP;
        Config::instance()->append('server.servers', $config);
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function applyTcpConfig(): void
    {
        $config = [
            'host' => $this->getHost(), 'port' => $this->getPort(),
            'name' => $this->getServerName(), 'type' => ServerInterface::SERVER_BASE,
            'callbacks' => array_intersect_key($this->eventCallBacks, [Event::ON_CONNECT => null, Event::ON_RECEIVE => null, Event::ON_CLOSE => null]),
        ];
        if (SYSTEM_ENGINE === 'swoole')
            $config['sock_type'] = SWOOLE_SOCK_TCP;
        elseif (SYSTEM_ENGINE === 'swow')
            $config['sock_type'] = Socket::TYPE_TCP;
        Config::instance()->append('server.servers', $config);
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function applyHttpConfig(): void
    {
        $callbacks = array_intersect_key($this->eventCallBacks, [Event::ON_REQUEST => null]);
        empty($callbacks[Event::ON_REQUEST]) && $callbacks[Event::ON_REQUEST] = [
            Container::instance()->setEntity('Hyperf\\HttpServer\\Server'), 'onRequest'
        ];
        $config = [
            'host' => $this->getHost(), 'port' => $this->getPort(), 'name' => $this->getServerName(),
            'type' => ServerInterface::SERVER_HTTP, 'callbacks' => $callbacks,
        ];
        if (SYSTEM_ENGINE === 'swoole')
            $config['sock_type'] = SWOOLE_SOCK_TCP;
        elseif (SYSTEM_ENGINE === 'swow')
            $config['sock_type'] = Socket::TYPE_TCP;
        Config::instance()->append('server.servers', $config);
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function applyWebsocketConfig(): void
    {
        if (!class_exists('Hyperf\\WebSocketServer\\Server'))
            throw new RuntimeException('Need `hyperf/websocket-server` composer component. Use `composer require hyperf/websocket-server` to install it');
        $callbacks = array_intersect_key($this->eventCallBacks, [
            Event::ON_REQUEST => null, Event::ON_HAND_SHAKE => null, Event::ON_MESSAGE => null, Event::ON_CLOSE => null,
        ]);
        empty($callbacks[Event::ON_REQUEST]) && $callbacks[Event::ON_REQUEST] = [
            Container::instance()->setEntity('Hyperf\\HttpServer\\Server'), 'onRequest'
        ];
        empty($callbacks[Event::ON_MESSAGE]) && $callbacks[Event::ON_MESSAGE] = [
            Container::instance()->setEntity('Hyperf\\WebSocketServer\\Server'), 'onMessage'
        ];
        empty($callbacks[Event::ON_HAND_SHAKE]) && $callbacks[Event::ON_HAND_SHAKE] = [$callbacks[Event::ON_MESSAGE][0], 'onHandShake'];
        empty($callbacks[Event::ON_CLOSE]) && $callbacks[Event::ON_CLOSE] = [$callbacks[Event::ON_MESSAGE][0], 'onClose'];
        $config = [
            'name' => $this->getServerName(), 'host' => $this->getHost(), 'port' => $this->getPort(),
            'type' => ServerInterface::SERVER_WEBSOCKET, 'callbacks' => $callbacks,
        ];
        if (SYSTEM_ENGINE === 'swoole')
            $config['sock_type'] = SWOOLE_SOCK_TCP;
        elseif (SYSTEM_ENGINE === 'swow')
            $config['sock_type'] = Socket::TYPE_TCP;
        Config::instance()->append('server.servers', $config);
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * @param Router $router
     * @return Server
     */
    public function setRouter(Router $router): self
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return Server
     */
    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return Server
     */
    public function setPort(int $port): self
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Server
     */
    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }

    /**
     * @param string $serverName
     * @return Server
     */
    public function setServerName(string $serverName): self
    {
        $this->serverName = $serverName;
        return $this;
    }
}
