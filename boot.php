<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::addExceptionHandler(function () {
        \Nash\Pin\Server\Response::instance()->withStatus(400)->withHeader('test', 'abc')->json(['id' => 1]);
    });
    App::get('/', fn()=>'Hello World!', function ($next) {
        \Nash\Pin\Server\Response::instance()->withHeader('test', 'adc');
        $next();
    });
})();
