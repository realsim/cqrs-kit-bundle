<?php

namespace Mechanic\CqrsKit\Entity;

use Symfony\Component\String\ByteString;

final class EntityUtils
{
    public static function alias($classOrObject): string
    {
        if (is_object($classOrObject)) {
            $className = get_class($classOrObject);
        } else {
            $className = (string) $classOrObject;
        }

        return (new ByteString($className))
            ->afterLast('\\')
            ->snake()
            ->toString();
    }
}
