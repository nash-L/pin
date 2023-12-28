<?php

namespace Nash\Pin\Server;

use Hyperf\Context\Context;
use Nash\Pin\Core\CallableTransform;
use Nash\Pin\Core\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function Hyperf\Support\call;

class Middleware implements MiddlewareInterface
{
    /**
     * @param array $callback
     */
    public function __construct(private array $callback)
    {}

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = call($this->callback, [function() use ($handler) {
            Context::set(ResponseInterface::class, $handler->handle(Context::get(ServerRequestInterface::class)));
        }]);
        if ($result instanceof ResponseInterface)
            return Context::set(ResponseInterface::class, $result);
        return Context::get(ResponseInterface::class);
    }

    /**
     * @param callable|string $callback
     * @return ?string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function create(callable|string $callback): ?string
    {
        if (is_callable($callback)) {
            $middleware = new self(CallableTransform::create($callback));
            return Container::instance()->setEntity($middleware);
        }
        if (class_exists($callback) && is_subclass_of($callback, MiddlewareInterface::class))
            return $callback;
        return null;
    }
}
