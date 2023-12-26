<?php

namespace Nash\Pin\Core;

use Hyperf\Crontab\Process\CrontabDispatcherProcess;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

if (!class_exists(CrontabDispatcherProcess::class))
    throw new RuntimeException('Need `hyperf/crontab` composer component. Use `composer require hyperf/crontab` to install it');

class Crontab
{
    use StaticNameObjectTrait;

    /** @var ?callable|array */
    protected $callback;

    /** @var string */
    protected string $name;

    /** @var string */
    protected string $rule;

    /** @var string */
    protected string $type;

    /** @var bool */
    protected bool $singleton;

    /** @var bool */
    protected bool $onOneServer;

    /** @var int */
    protected int $mutexExpires;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->setName($name);
        $this->setRule('* * * * *');
        $this->setCallback(null);
        $this->setSingleton(false);
        $this->setOnOneServer(false);
        $this->setMutexExpires(3600);
    }

    /**
     * @param int $mutexExpires
     * @return Crontab
     */
    public function setMutexExpires(int $mutexExpires): self
    {
        $this->mutexExpires = $mutexExpires;
        return $this;
    }

    /**
     * @return int
     */
    public function getMutexExpires(): int
    {
        return $this->mutexExpires;
    }

    /**
     * @param bool $onOneServer
     * @return Crontab
     */
    public function setOnOneServer(bool $onOneServer): self
    {
        $this->onOneServer = $onOneServer;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOnOneServer(): bool
    {
        return $this->onOneServer;
    }

    /**
     * @param bool $singleton
     * @return Crontab
     */
    public function setSingleton(bool $singleton): self
    {
        $this->singleton = $singleton;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * @param string $name
     * @return Crontab
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Crontab
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param callable|null $callback
     * @return $this
     */
    public function setCallback(?callable $callback): self
    {
        $this->setType('callback');
        $this->callback = $callback;
        return $this;
    }

    /**
     * @param string $command
     * @param array $arguments
     * @param array $options
     * @return $this
     */
    public function setCommand(string $command, array $arguments = [], array $options = []): self
    {
        $this->setType('command');
        $options['--disable-event-dispatcher'] = true;
        $this->callback = array_merge(['command' => $command], $arguments, $options);
        return $this;
    }

    /**
     * @return callable|array
     */
    public function getCallback(): callable|array
    {
        return $this->callback;
    }

    /**
     * @param string $rule
     * @return self
     */
    public function setRule(string $rule): self
    {
        $this->rule = $rule;
        return $this;
    }

    /**
     * @return string
     */
    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function applyConfig(): self
    {
        Config::instance()->set('crontab.enable', true);
        if (!in_array(CrontabDispatcherProcess::class, Config::instance()->get('processes', [])))
            Config::instance()->append('processes', CrontabDispatcherProcess::class);
        $crontab = (new \Hyperf\Crontab\Crontab())->setName($this->getName())->setRule($this->getRule())->setType($this->getType())
            ->setMutexExpires($this->getMutexExpires())->setSingleton($this->isSingleton())->setOnOneServer($this->isOnOneServer());
        switch ($this->getType()) {
            case 'command': $crontab->setCallback($this->getCallback()); break;
            case 'callback': $call = CallableTransform::create($this->callback); $crontab->setCallback([$call[0], '__call', [$call[1], []]]); break;
        }
        Config::instance()->append('crontab.crontab', $crontab);
        return $this;
    }
}
