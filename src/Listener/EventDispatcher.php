<?php

namespace Nash\Pin\Listener;

use Nash\Pin\Core\Container;
use Nash\Pin\Core\StaticObjectTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventDispatcher implements EventDispatcherInterface
{
    use StaticObjectTrait;

    /**
     * @param object $event
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function dispatch(object $event): void
    {
        Container::instance()->get(EventDispatcherInterface::class)->dispatch($event);
    }
}
