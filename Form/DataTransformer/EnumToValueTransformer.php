<?php

namespace Mechanic\CqrsKit\Form\DataTransformer;

use MyCLabs\Enum\Enum;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use LogicException;
use UnexpectedValueException;
use function is_subclass_of;
use function sprintf;

class EnumToValueTransformer implements DataTransformerInterface
{
    private string $enumClass;

    public function __construct(string $enumClass)
    {
        if (!is_subclass_of($enumClass, Enum::class)) {
            throw new LogicException(sprintf('%s must be a subclass of Enum.', $enumClass));
        }

        $this->enumClass = $enumClass;
    }

    public function transform($enum)
    {
        if (null === $enum) {
            return null;
        }

        if (!$enum instanceof $this->enumClass) {
            throw new TransformationFailedException(sprintf('Expected an instance of %s.', $this->enumClass));
        }

        return $enum->getValue();
    }

    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            $enum = new $this->enumClass($value);
        } catch (UnexpectedValueException $ex) {
            throw new TransformationFailedException($ex->getMessage(), 0, $ex);
        }

        return $enum;
    }
}
