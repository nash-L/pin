<?php

namespace Nash\Pin\Server;

use Nash\Pin\Core\CallableTransform;
use Nash\Pin\Core\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Swow\Psr7\Message\ResponsePlusInterface;
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
     * @param ResponsePlusInterface $response
     * @return void
     */
    public function handle(Throwable $throwable, ResponsePlusInterface $response): void
    {
        call($this->callback, [$throwable]);
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
