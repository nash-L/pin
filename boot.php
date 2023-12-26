<?php

require __DIR__ . '/vendor/autoload.php';

(function () {
    App::get('/', fn()=>'Hello World!');
})();
