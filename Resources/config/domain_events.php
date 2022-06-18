<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Mechanic\CqrsKit\EventListener\DoctrineOrmDomainEventsSubscriber;

return static function (ContainerConfigurator $container): void {
    $container->services()

        ->set('cqrs.domain_events.orm_listener', DoctrineOrmDomainEventsSubscriber::class)
            ->args([
                service('cqrs.messenger.event_dispatcher'),
            ])
            ->tag('doctrine.event_subscriber')
    ;
};
