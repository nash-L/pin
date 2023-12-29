<?php

namespace Nash\Pin\Core;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function Hyperf\Support\call;

class CallableTransform
{
    /** @var callable|null */
    private $callback;

    /**
     * @param callable|null $callback
     */
    public function __construct(?callable $callback = null)
    {
        $this->callback = $callback;
    }

    /**
     * @param ...$arguments
     * @return mixed|null
     */
    public function __invoke(...$arguments)
    {
        if ($this->callback)
            return call($this->callback, $arguments);
    }

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
     * @param bool $entitySelf
     * @return array|string[]
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function create(callable $callback, bool $entitySelf = false): array
    {
        if ($entitySelf)
            return [Container::instance()->setEntity(new self($callback)), '__invoke'];
        return [self::class, '__invoke_' . Container::instance()->setEntity(Closure::fromCallable($callback))];
    }
}