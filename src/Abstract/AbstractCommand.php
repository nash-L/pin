<?php

namespace Nash\Pin\Abstract;

use Hyperf\Command\Command;
use Nash\Pin\Command\{Input, Output};

abstract class AbstractCommand extends Command
{
    /**
     * @return int
     */
    public function handle(): int
    {
        return $this->__invoke(Input::instance(), Output::instance());
    }

    /**
     * @return int
     */
    abstract public function __invoke(Input $input, Output $output): int;
}

