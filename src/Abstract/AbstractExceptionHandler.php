<?php

namespace Nash\Pin\Abstract;

use Nash\Pin\Server\ExceptionHandler;
use Nash\Pin\Server\Response;
use Throwable;

abstract class AbstractExceptionHandler extends ExceptionHandler
{
    public function __construct()
    {
        parent::__construct([$this, '__invoke']);
    }

    abstract public function __invoke(Throwable $throwable): Throwable|Response;

}

