<?php

namespace Mechanic\CqrsKit\Messenger\Exception;

use JetBrains\PhpStorm\Pure;

class AccessDeniedException extends DomainException
{
    public function __construct(string $role, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('You are not allowed to perform this action. Required role is %s.', $role);

        parent::__construct($message, $code, $previous);
    }
}
