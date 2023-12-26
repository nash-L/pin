<?php

namespace Nash\Pin\Core;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function Hyperf\Support\call;

class CallableTransform
{
    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|void|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __call(string $name, array $arguments)
    {
        return static::__callStatic($name, $arguments);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed|void|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (str_starts_with($name, '__invoke_'))
            return call(Container::instance()->get(substr($name, 9)), $arguments);
    }

    /**
     * @param callable $callback
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function create(callable $callback): array
    {
        return [self::class, '__invoke_' . Container::instance()->setEntity(Closure::fromCallable($callback))];
    }
}