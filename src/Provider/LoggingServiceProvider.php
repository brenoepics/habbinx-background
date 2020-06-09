<?php

/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE.md (GNU License)
 */

namespace App\Provider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

/**
 * Class LoggingServiceProvider
 *
 * @package App\Provider
 */
class LoggingServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'logger'
    ];

    public function register()
    {
        $container = $this->getContainer();

        $container->add('logger', function () use ($container) {
            $settings       = $container->get('settings');
            $loggerSettings = $settings['logger'];
            $logger         = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();

            $logger->pushProcessor($processor);

            foreach ($loggerSettings['enabled_log_levels'] as $logStreamSettings) {
                $handler = new StreamHandler($logStreamSettings['path'], $logStreamSettings['level'], false);
                $logger->pushHandler($handler);
            }

            return $logger;
        });
    }
}