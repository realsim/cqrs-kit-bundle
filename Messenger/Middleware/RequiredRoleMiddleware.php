<?php

namespace Mechanic\CqrsKit\Messenger\Middleware;

use Mechanic\CqrsKit\Messenger\Exception\AccessDeniedException;
use Mechanic\CqrsKit\Messenger\Stamp\RequiredRoleStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RequiredRoleMiddleware implements MiddlewareInterface
{
    private AuthorizationCheckerInterface $authChecker;

    public function __construct(AuthorizationCheckerInterface $authChecker)
    {
        $this->authChecker = $authChecker;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $roleStamp = $envelope->last(RequiredRoleStamp::class);
        if (null !== $roleStamp) {
            $role = $roleStamp->getRole();

            if (!$this->authChecker->isGranted($role)) {
                throw new AccessDeniedException($role);
            }
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
