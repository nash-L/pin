<?php

namespace Nash\Pin\Core;

use Hyperf\Config\{Config, ProviderConfig};
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\{Definition\DefinitionSource, Container as BaseContainer};
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};

trait StaticNameObjectTrait
{
    /**
     * @param string $name
     * @return static
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function instance(string $name): static
    {
        $list = Container::instance()->getMap(static::class);
        if (empty($list[$name]))
            return Container::instance()->appendMap(static::class, $name, new static($name));
        return $list[$name];
    }
}
