<?php

namespace Mechanic\CqrsKit\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Mechanic\CqrsKit\Entity\ContainsDomainEventsInterface;
use Mechanic\CqrsKit\Messenger\EventDispatcher;

class DoctrineOrmDomainEventsSubscriber implements EventSubscriber
{
    private EventDispatcher $eventDispatcher;
    private ArrayCollection $entities;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->entities = new ArrayCollection();
    }

    public function getSubscribedEvents(): array
    {
        return [
            'prePersist',
            'preUpdate',
            'preRemove',
            'preFlush',
            'postFlush',
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->collectEntityContainingDomainEvents($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->collectEntityContainingDomainEvents($args);
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $this->collectEntityContainingDomainEvents($args);
    }

    public function preFlush(PreFlushEventArgs $args): void
    {
        $uow = $args->getEntityManager()->getUnitOfWork();

        foreach ($uow->getIdentityMap() as $class => $entities) {
            if (!is_subclass_of($class, ContainsDomainEventsInterface::class)) {
                continue;
            }

            foreach ($entities as $entity) {
                $this->entities->add($entity);
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $events = new ArrayCollection();

        foreach ($this->entities as $entity) {
            foreach ($entity->releaseEvents() as $event) {
                $events->add($event);
            }
        }

        foreach ($events as $event) {
            $this->eventDispatcher->emit($event);
        }
    }

    private function collectEntityContainingDomainEvents(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof ContainsDomainEventsInterface) {
            $this->entities->add($entity);
        }
    }
}
