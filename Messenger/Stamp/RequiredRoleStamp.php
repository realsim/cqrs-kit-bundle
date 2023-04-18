<?php

namespace Mechanic\CqrsKit\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

final class RequiredRoleStamp implements StampInterface
{
    private string $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
