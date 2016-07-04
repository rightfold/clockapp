<?php
namespace ClockApp\Plugins;
use Exception;

final class Plugin {
    private $name;
    private $initialize;

    private function __construct(string $name, callable $initialize) {
        $this->name = $name;
        $this->initialize = $initialize;
    }

    public function name() { return $this->name; }

    public function initialize(Environment $environment) {
        ($this->initialize)($environment);
    }

    public static function load(string $path): self {
        $pluginInfo = json_decode(file_get_contents("$path/plugin.json"));
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('ill-formed plugin info file');
        }
        if (!isset($pluginInfo->name)) {
            throw new Exception('plugin info file has no name field');
        }
        $initialize = include_once "$path/initialize.php";
        return new self($pluginInfo->name, $initialize);
    }
}
