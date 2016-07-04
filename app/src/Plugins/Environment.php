<?php
namespace ClockApp\Plugins;
use Composer\Autoload\ClassLoader;

final class Environment {
    private $classLoader;

    public function __construct(ClassLoader $classLoader) {
        $this->classLoader = $classLoader;
    }

    public function classLoader() { return $this->classLoader; }
}
