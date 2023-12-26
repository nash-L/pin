<?php

namespace Nash\Pin\Core;

use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use RuntimeException;

class Listener
{
    use StaticNameObjectTrait;

    protected string $eventName;

    /** @var null|callable */
    private $callback;

    /**
     * @param string $eventName
     */
    public function __construct(string $eventName)
    {
        $this->setEventName($eventName);
        $this->setCallback(null);
    }

    /**
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function applyConfig(): self
    {
        if (empty($this->callback)) {
            if (class_exists($this->eventName) && is_subclass_of($this->eventName, ListenerInterface::class)) {
                Config::instance()->append('listeners', $this->eventName);
                return $this;
            }
            throw new RuntimeException('it is not a ListenerInterface class');
        }
        if (is_callable($this->callback)) {
            $provider = Container::instance()->get(ListenerProviderInterface::class);
            $provider->on($this->getEventName(), $this->getCallback(), 1);
        }
        return $this;
    }

    /**
     * @param string $eventName
     * @return $this
     */
    public function setEventName(string $eventName): self
    {
        $this->eventName = $eventName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * @param callable|null $callback
     * @return $this
     */
    public function setCallback(?callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }
}
