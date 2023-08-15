<?php

namespace App;

use App\Shared\Application\Messenger\CommandHandler;
use App\Shared\Application\Messenger\EventBus;
use App\Shared\Application\Messenger\QueryHandler;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    private const MESSAGE_HANDLER_TAG = 'messenger.message_handler';

    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(CommandHandler::class)
            ->addTag(self::MESSAGE_HANDLER_TAG, ['bus' => 'command.bus']);

        $container
            ->registerForAutoconfiguration(QueryHandler::class)
            ->addTag(self::MESSAGE_HANDLER_TAG, ['bus' => 'query.bus']);

        $container
            ->registerForAutoconfiguration(EventBus::class)
            ->addTag(self::MESSAGE_HANDLER_TAG, ['bus' => 'event.bus']);
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/' . $this->environment . '/*.yaml');

        if (is_file(\dirname(__DIR__) . '/config/services.yaml')) {
            $container->import('../config/services.yaml');
            $container->import('../config/{services}_' . $this->environment . '.yaml');
        } elseif (is_file($path = \dirname(__DIR__) . '/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/' . $this->environment . '/*.yaml');
        $routes->import('../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__) . '/config/routes.yaml')) {
            $routes->import('../config/routes.yaml');
        } elseif (is_file($path = \dirname(__DIR__) . '/config/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }
    }
}
