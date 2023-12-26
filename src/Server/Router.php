<?php

namespace Nash\Pin\Server;

use Hyperf\HttpServer\Router\DispatcherFactory;
use Nash\Pin\Core\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Router
{
    /** @var string */
    protected string $serverName;

    /**
     * @param string $serverName
     */
    public function __construct(string $serverName)
    {
        $this->setServerName($serverName);
    }

    /**
     * @param array|string $prefix
     * @param callable $callback
     * @param callable|string ...$middlewares
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function group(array|string $prefix, callable $callback, callable|string ...$middlewares): self
    {
        Container::instance()->get(DispatcherFactory::class)->getRouter($this->getServerName())->addGroup($prefix, $callback, [
            'middleware' => array_filter(array_map(fn($middleware)=>Middleware::create($middleware), $middlewares))
        ]);
        return $this;
    }

    /**
     * @param string|array $methods
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function route(string|array $methods, string $route, callable|array|string $handler, callable|string ...$middlewares): self
    {
        Container::instance()->get(DispatcherFactory::class)->getRouter($this->getServerName())
            ->addRoute($methods, $route, $handler, [
                'middleware' => array_filter(array_map(fn($middleware)=>Middleware::create($middleware), $middlewares))
            ]);
        return $this;
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $route, callable|array|string $handler, callable|string ...$middlewares): self
    {
        return $this->route('GET', $route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function post(string $route, callable|array|string $handler, callable|string ...$middlewares): self
    {
        return $this->route('POST', $route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function put(string $route, callable|array|string $handler, callable|string ...$middlewares): self
    {
        return $this->route('PUT', $route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function delete(string $route, callable|array|string $handler, callable|string ...$middlewares): self
    {
        return $this->route('DELETE', $route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function patch(string $route, callable|array|string $handler, callable|string ...$middlewares): self
    {
        return $this->route('PATCH', $route, $handler, ...$middlewares);
    }

    /**
     * @param string $route
     * @param callable|array|string $handler
     * @param callable|string ...$middlewares
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function head(string $route, callable|array|string $handler, callable|string ...$middlewares): self
    {
        return $this->route('HEAD', $route, $handler, ...$middlewares);
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
     * @return $this
     */
    public function setServerName(string $serverName): self
    {
        $this->serverName = $serverName;
        return $this;
    }
}
