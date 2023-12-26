<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::setMode(\Nash\Pin\Core\Application::MODE_COROUTINE);
})();
