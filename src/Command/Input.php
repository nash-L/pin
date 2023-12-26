<?php

namespace Nash\Pin\Command;

use Nash\Pin\Core\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Input extends ArgvInput
{
    const ARGUMENT_REQUIRED = InputArgument::REQUIRED;
    const ARGUMENT_OPTIONAL = InputArgument::OPTIONAL;
    const ARGUMENT_IS_ARRAY = InputArgument::IS_ARRAY;
    const OPTION_OPTIONAL = InputOption::VALUE_OPTIONAL;
    const OPTION_IS_ARRAY = InputOption::VALUE_IS_ARRAY;
    const OPTION_NONE = InputOption::VALUE_NONE;
    const OPTION_NEGATABLE = InputOption::VALUE_NEGATABLE;
    const OPTION_REQUIRED = InputOption::VALUE_REQUIRED;

    /**
     * @return self
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function instance(): self
    {
        return Application::instance()->getInput();
    }
}