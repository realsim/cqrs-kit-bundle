<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Mechanic\CqrsKit\Messenger\CommandDispatcher;
use Mechanic\CqrsKit\Messenger\EventDispatcher;
use Mechanic\CqrsKit\Messenger\HandledResultExtractor;
use Mechanic\CqrsKit\Messenger\QueryDispatcher;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('cqrs.messenger.handled_result_extractor', HandledResultExtractor::class)

        ->set('cqrs.messenger.command_dispatcher', CommandDispatcher::class)
            ->args([
                abstract_arg('command bus service'),
                service('cqrs.messenger.handled_result_extractor'),
            ])
            ->alias(CommandDispatcher::class, 'cqrs.messenger.command_dispatcher')

        ->set('cqrs.messenger.query_dispatcher', QueryDispatcher::class)
            ->args([
                abstract_arg('query bus service'),
                service('cqrs.messenger.handled_result_extractor'),
            ])
            ->alias(QueryDispatcher::class, 'cqrs.messenger.query_dispatcher')

        ->set('cqrs.messenger.event_dispatcher', EventDispatcher::class)
            ->args([
                abstract_arg('event bus service'),
            ])
            ->alias(EventDispatcher::class, 'cqrs.messenger.event_dispatcher')
    ;
};
