<?php

namespace Mechanic\CqrsKit\Form\DataTransformer;

use MyCLabs\Enum\Enum;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use UnexpectedValueException;
use LogicException;

class EnumsToValuesTransformer implements DataTransformerInterface
{
    private string $enumClass;

    public function __construct(string $enumClass)
    {
        if (!is_subclass_of($enumClass, Enum::class)) {
            throw new LogicException(sprintf('%s must be a subclass of Enum.', $enumClass));
        }

        $this->enumClass = $enumClass;
    }

    public function transform($enums): array
    {
        if (null === $enums) {
            return [];
        }

        if (!is_array($enums)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $return = [];

        foreach ($enums as $enum) {
            if (!$enum instanceof $this->enumClass) {
                throw new TransformationFailedException(sprintf('Expected an instance of %s.', $this->enumClass));
            }

            $return[] = $enum->getValue();
        }

        return $return;
    }

    public function reverseTransform($values): array
    {
        if (null === $values) {
            return [];
        }

        if (!is_array($values)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $return = [];

        foreach ($values as $value) {
            try {
                $return[] = new $this->enumClass($value);
            } catch (UnexpectedValueException $ex) {
                throw new TransformationFailedException($ex->getMessage(), 0, $ex);
            }
        }

        return $return;
    }
}
