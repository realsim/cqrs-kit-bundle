<?php

namespace Mechanic\CqrsKit\DependencyInjection;

use Mechanic\CqrsKit\Attribute\CommandHandler;
use Mechanic\CqrsKit\Attribute\EventHandler;
use Mechanic\CqrsKit\Attribute\QueryHandler;
use Mechanic\CqrsKit\DependencyInjection\Compiler\MessageHandlerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CqrsKitExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('messenger.php');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->getDefinition('cqrs.messenger.command_dispatcher')
            ->replaceArgument(0, new Reference($config['dispatchers']['command']));

        $container->getDefinition('cqrs.messenger.query_dispatcher')
            ->replaceArgument(0, new Reference($config['dispatchers']['query']));

        $container->getDefinition('cqrs.messenger.event_dispatcher')
            ->replaceArgument(0, new Reference($config['dispatchers']['event']));


        $this->registerHandlers($container, 'cqrs.command_handler', CommandHandler::class, $config['dispatchers']['command']);
        $this->registerHandlers($container, 'cqrs.query_handler', QueryHandler::class, $config['dispatchers']['query']);
        $this->registerHandlers($container, 'cqrs.event_handler', EventHandler::class, $config['dispatchers']['event']);
    }

    private function registerHandlers(ContainerBuilder $container, string $handlerTag, string $handlerAttribute, string $busTag): void
    {
        $addTag = static function (Definition $definition, string $busTag): void {
            $definition->addTag('messenger.message_handler', ['bus' => $busTag]);
        };

        foreach ($container->findTaggedServiceIds($handlerTag) as $id => $attr) {
            $definition = $container->getDefinition($id);
            $addTag($definition, $busTag);
        }

        $container->registerAttributeForAutoconfiguration($handlerAttribute, function (ChildDefinition $definition) use ($addTag, $busTag) {
            $addTag($definition, $busTag);
        });
    }
}
