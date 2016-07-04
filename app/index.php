<?php
call_user_func(function() {
    set_error_handler(function($errno, $errstr, $errfile, $errline ) {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    });
    $classLoader = require_once __DIR__ . '/vendor/autoload.php';
    \ClockApp\Main::main($classLoader);
});
