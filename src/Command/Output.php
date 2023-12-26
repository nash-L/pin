<?php

namespace Nash\Pin\Command;

use Nash\Pin\Core\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class Output extends ConsoleOutput
{
    /**
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function instance(): self
    {
        return Application::instance()->getOutput();
    }
}