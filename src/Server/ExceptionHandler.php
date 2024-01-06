<?php

namespace Nash\Pin\Server;

use Hyperf\Context\Context;
use Nash\Pin\Core\CallableTransform;
use Nash\Pin\Core\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use function Hyperf\Support\call;

class ExceptionHandler extends \Hyperf\ExceptionHandler\ExceptionHandler
{
    /**
     * @param array $callback
     */
    public function __construct(private array $callback)
    {}

    /**
     * @param Throwable $throwable
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $result = call($this->callback, [$throwable]);
        if (!$result instanceof Throwable)
            $this->stopPropagation();
        if ($result instanceof ResponseInterface && !$result instanceof Response)
            return Context::set(ResponseInterface::class, $result);
        return Context::get(ResponseInterface::class);
    }

    /**
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return true;
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
            $exceptionHandler = new self(CallableTransform::create($callback));
            return Container::instance()->setEntity($exceptionHandler);
        }
        if (class_exists($callback) && is_subclass_of($callback, \Hyperf\ExceptionHandler\ExceptionHandler::class))
            return $callback;
        return null;
    }
}
