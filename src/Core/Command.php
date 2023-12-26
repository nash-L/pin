<?php

namespace Nash\Pin\Core;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function Hyperf\Support\call;

class Command extends \Hyperf\Command\Command
{
    use StaticNameObjectTrait;

    /** @var null|array */
    protected ?array $callback;

    /**
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->callback = null;
    }

    /**
     * @param callable $callback
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setCallback(callable $callback): self
    {
        $this->callback = CallableTransform::create($callback);
        return $this;
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(): void
    {
        if ($this->callback) {
            call([
                Container::instance()->get($this->callback[0]), $this->callback[1]
            ], [$this->input, $this->output]);
        }
    }

    /**
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function applyConfig(): self
    {
        Config::instance()->append('commands', Container::instance()->setEntity($this));
        return $this;
    }
}
