<?php

namespace Nash\Pin\Core;

use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerInterface;

/**
 * @method void set(string $name, mixed $entry)
 * @method void unbind(string $name)
 * @method void define(string $name, mixed $definition)
 * @method mixed make(string $name, array $parameters = [])
 */
class Container implements ContainerInterface
{
    use StaticObjectTrait;

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return ApplicationContext::getContainer()->{$name}(...$arguments);
    }

    /**
     * @param string|object $className
     * @param array $params
     * @return string
     */
    public function setEntity(string|object $className, array $params = []): string
    {
        if (is_string($className) && class_exists($className)) {
            $entity = $this->make($className, $params);
            $this->set($entityId = $className . '_' . spl_object_hash($entity), $entity);
        } else {
            $this->set($entityId = get_class($className) . '_' . spl_object_hash($className), $className);
        }
        return $entityId;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed
    {
        return $this->__call(__FUNCTION__, func_get_args());
    }

    /**
     * @param string $id
     * @return array
     */
    public function getMap(string $id): array
    {
        $key = $id . '#map';
        if ($this->has($key))
            return $this->get($key);
        return [];
    }

    /**
     * @param string $id
     * @return void
     */
    public function unbindMap(string $id): void
    {
        $key = $id . '#map';
        if ($this->has($key))
            $this->unbind($key);
    }

    /**
     * @param string $id
     * @param string $key
     * @param mixed $obj
     * @return mixed
     */
    public function appendMap(string $id, string $key, mixed $obj): mixed
    {
        $map = static::getMap($id);
        $map[$key] = $obj;
        $this->set($id . '#map', $map);
        return $obj;
    }
}
