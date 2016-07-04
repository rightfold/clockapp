<?php
namespace ClockApp;
use ClockApp\Plugins\Plugin;
use ClockApp\Plugins\Environment;
use Composer\Autoload\ClassLoader;
use Exception;
use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use stdClass;
use Throwable;

final class Main {
    private function __construct() { }

    public static function main(ClassLoader $classLoader) {
        $logger = new Logger('ClockApp');
        $logger->pushHandler(new SyslogHandler('ClockApp'));
        $config = self::loadConfig();
        self::loadPlugins($logger, $classLoader, $config->plugins);
    }

    private static function configPath(): string {
        $path = getenv('CLOCKAPP_CONFIG');
        if ($path === false) {
            throw new Exception('CLOCKAPP_CONFIG environment variable was not set');
        }
        return $path;
    }

    private static function loadConfig(): stdClass {
        $config = json_decode(file_get_contents(self::configPath()));
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('ill-formed ClockApp config file');
        }
        if (!isset($config->plugins)) {
            $config->plugins = [];
        }
        return $config;
    }

    private static function loadPlugins(Logger $logger, ClassLoader $classLoader, array $pluginPaths) {
        $environment = new Environment($classLoader);
        $configPath = self::configPath();
        foreach ($pluginPaths as $pluginPath) {
            $pluginPath = dirname($configPath) . "/$pluginPath";
            try {
                $plugin = Plugin::load($pluginPath);
                $plugin->initialize($environment);
            } catch (Throwable $ex) {
                $logger->error('error loading plugin', ['plugin' => $pluginPath, 'reason' => $ex]);
                $logger->warning('will not load plugin', ['plugin' => $pluginPath]);
            }
        }
    }
}
