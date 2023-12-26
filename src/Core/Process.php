<?php

namespace Nash\Pin\Core;

use Hyperf\Process\AbstractProcess;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use function Hyperf\Support\call;

if (!class_exists(AbstractProcess::class))
    throw new RuntimeException('Need `hyperf/process` composer component. Use `composer require hyperf/process` to install it');

class Process extends AbstractProcess
{
    use StaticNameObjectTrait;

    /** @var callable */
    private $callback;

    /** @var bool|callable */
    private $enable = true;

    /**
     * @param string $name
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(string $name) {
        parent::__construct(Container::instance());
        $this->setName($name);
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    /**
     * @return void
     */
    public function handle(): void {
        call($this->getCallback());
    }

    /**
     * @param $server
     * @return bool
     */
    public function isEnable($server): bool
    {
        if (is_callable($this->enable)) {
            return (bool) call($this->enable, [$server]);
        }
        return (bool) $this->enable;
    }

    /**
     * @param callable|bool $enable
     * @return $this
     */
    public function setEnable(callable|bool $enable): self
    {
        $this->enable = $enable;
        return $this;
    }

    /**
     * @param bool $enableCoroutine
     * @return $this
     */
    public function setEnableCoroutine(bool $enableCoroutine): self
    {
        $this->enableCoroutine = $enableCoroutine;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param int $nums
     * @return $this
     */
    public function setNums(int $nums): self
    {
        $this->nums = $nums;
        return $this;
    }

    /**
     * @param int $pipeType
     * @return $this
     */
    public function setPipeType(int $pipeType): self
    {
        $this->pipeType = $pipeType;
        return $this;
    }

    /**
     * @param bool $redirectStdinStdout
     * @return $this
     */
    public function setRedirectStdinStdout(bool $redirectStdinStdout): self
    {
        $this->redirectStdinStdout = $redirectStdinStdout;
        return $this;
    }

    /**
     * @param int $restartInterval
     * @return $this
     */
    public function setRestartInterval(int $restartInterval): self
    {
        $this->restartInterval = $restartInterval;
        return $this;
    }

    /**
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function applyConfig(): self
    {
        Config::instance()->append('processes', Container::instance()->setEntity($this));
        return $this;
    }
}
