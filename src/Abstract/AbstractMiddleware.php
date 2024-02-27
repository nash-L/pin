<?php

namespace Nash\Pin\Abstract;

use Nash\Pin\Server\Middleware;
use Nash\Pin\Server\Response;

abstract class AbstractMiddleware extends Middleware
{
    public function __construct()
    {
        parent::__construct([$this, '__invoke']);
    }

    /**
     * @param callable $next
     * @return Response
     */
    abstract public function __invoke(callable $next): Response;
}

