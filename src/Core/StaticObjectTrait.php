<?php

namespace Nash\Pin\Core;

use Hyperf\Config\{Config, ProviderConfig};
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\{Definition\DefinitionSource, Container as BaseContainer};
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};

trait StaticObjectTrait
{
    /**
     * @return static
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function instance(): static
    {
        if (!ApplicationContext::hasContainer()) {
            $config = new Config(ProviderConfig::load());
            $container = new BaseContainer(new DefinitionSource($config->get('dependencies', [])));
            $container->set(ConfigInterface::class, $config);
            ApplicationContext::setContainer($container);
        }
        return ApplicationContext::getContainer()->get(self::class);
    }
}
