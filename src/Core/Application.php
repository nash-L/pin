<?php

namespace Nash\Pin\Core;

use Hyperf\Contract\ApplicationInterface;
use Hyperf\Framework\Bootstrap\{PipeMessageCallback, ServerStartCallback, WorkerExitCallback, WorkerStartCallback};
use Hyperf\Server\{CoroutineServer, Event, SwowServer};
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};
use Nash\Pin\Command\{Input, Output};

class Application
{
    use StaticObjectTrait;

    /** @var int */
    const MODE_BASE = 1, MODE_PROCESS = 2, MODE_COROUTINE = 3;

    /** @var int */
    protected int $mode;

    /** @var Input */
    protected Input $input;

    /** @var Output */
    protected Output $output;

    /** @var array */
    protected array $eventCallBacks;

    /**
     * @param string $event
     * @param callable $call
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function on(string $event, callable $call): void
    {
        if (in_array($event, [
            Event::ON_START, Event::ON_WORKER_START, Event::ON_WORKER_STOP, Event::ON_WORKER_EXIT, Event::ON_WORKER_ERROR, Event::ON_BEFORE_START,
            Event::ON_PIPE_MESSAGE, Event::ON_TASK, Event::ON_FINISH, Event::ON_SHUTDOWN, Event::ON_MANAGER_START, Event::ON_MANAGER_STOP,
        ]))
            $this->eventCallBacks[$event] = CallableTransform::create($call);
    }

    public function __construct()
    {
        $this->setMode(self::MODE_PROCESS);
        $this->setInput(new Input);
        $this->setOutput(new Output);
        $this->eventCallBacks = [];
    }

    /**
     * @param Input $input
     * @return Application
     */
    public function setInput(Input $input): self
    {
        $this->input = $input;
        return $this;
    }

    /**
     * @return Output
     */
    public function getOutput(): Output
    {
        return $this->output;
    }

    /**
     * @param Output $output
     * @return $this
     */
    public function setOutput(Output $output): self
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @return Input
     */
    public function getInput(): Input
    {
        return $this->input;
    }

    /**
     * @param int $mode
     * @return $this
     */
    public function setMode(int $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): void
    {
        Container::instance()->get(ApplicationInterface::class)->run($this->getInput(), $this->getOutput());
    }

    /**
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function applyConfig(): self
    {
        if (SYSTEM_ENGINE === 'swoole') {
            switch ($this->getMode()) {
                case self::MODE_BASE: $this->applyBaseConfig(); break;
                case self::MODE_PROCESS: $this->applyProcessConfig(); break;
                case self::MODE_COROUTINE: $this->applyCoroutineConfig(); break;
            }
        } elseif (SYSTEM_ENGINE === 'swow')
            $this->applySwowConfig();
        return $this;
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function applyBaseConfig(): void
    {
        Config::instance()->set('server', [
            'mode' => SWOOLE_BASE,
            'servers' => [],
            'settings' => [
                'worker_num' => 1, 'max_coroutine' => 100000, 'max_request' => 0,
                'socket_buffer_size' => 2 * 1024 * 1024, 'buffer_output_size' => 2 * 1024 * 1024,
                'enable_coroutine' => true, 'open_tcp_nodelay' => true, 'open_http2_protocol' => true,
            ],
            'callbacks' => array_merge([
                Event::ON_BEFORE_START => [ServerStartCallback::class, 'beforeStart'],
                Event::ON_WORKER_START => [WorkerStartCallback::class, 'onWorkerStart'],
                Event::ON_WORKER_EXIT => [WorkerExitCallback::class, 'onWorkerExit'],
                Event::ON_PIPE_MESSAGE => [PipeMessageCallback::class, 'onPipeMessage'],
            ], $this->eventCallBacks),
        ]);
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function applyProcessConfig(): void
    {
        Config::instance()->set('server', [
            'mode' => SWOOLE_PROCESS,
            'servers' => [],
            'settings' => [
                'socket_buffer_size' => 2 * 1024 * 1024, 'buffer_output_size' => 2 * 1024 * 1024,
                'worker_num' => swoole_cpu_num(), 'max_request' => 100000, 'max_coroutine' => 100000,
                'enable_coroutine' => true, 'open_tcp_nodelay' => true, 'open_http2_protocol' => true,
            ],
            'callbacks' => array_merge([
                Event::ON_BEFORE_START => [ServerStartCallback::class, 'beforeStart'],
                Event::ON_WORKER_START => [WorkerStartCallback::class, 'onWorkerStart'],
                Event::ON_WORKER_EXIT => [WorkerExitCallback::class, 'onWorkerExit'],
                Event::ON_PIPE_MESSAGE => [PipeMessageCallback::class, 'onPipeMessage'],
            ], $this->eventCallBacks),
        ]);
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function applyCoroutineConfig(): void
    {
        Config::instance()->set('server', [
            'mode' => SWOOLE_BASE,
            'type' => CoroutineServer::class,
            'servers' => [],
            'settings' => [
                'worker_num' => 1, 'max_coroutine' => 100000, 'max_request' => 0,
                'socket_buffer_size' => 2 * 1024 * 1024, 'buffer_output_size' => 2 * 1024 * 1024,
                'enable_coroutine' => true, 'open_tcp_nodelay' => true, 'open_http2_protocol' => true,
            ],
            'callbacks' => array_merge([
                Event::ON_BEFORE_START => [ServerStartCallback::class, 'beforeStart'],
                Event::ON_WORKER_START => [WorkerStartCallback::class, 'onWorkerStart'],
                Event::ON_WORKER_EXIT => [WorkerExitCallback::class, 'onWorkerExit'],
                Event::ON_PIPE_MESSAGE => [PipeMessageCallback::class, 'onPipeMessage'],
            ], $this->eventCallBacks),
        ]);
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function applySwowConfig(): void
    {
        Config::instance()->set('server', [
            'type' => SwowServer::class,
            'servers' => [],
        ]);
    }
}