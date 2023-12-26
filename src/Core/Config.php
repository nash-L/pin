<?php

namespace Nash\Pin\Core;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Config implements ConfigInterface
{
    use StaticObjectTrait;

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __call(string $name, array $arguments)
    {
        return Container::instance()->get(ConfigInterface::class)->{$name}(...$arguments);
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $keys
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function has(string $keys): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function set(string $key, mixed $value): void
    {
        $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function merge(string $key, mixed $value): void
    {
        $this->set($key, array_merge_recursive($this->get($key), $value));
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function append(string $key, mixed $value): void
    {
        $this->set($key, array_merge($this->get($key, []), [$value]));
    }
}
