<?php

namespace Mechanic\CqrsKit\Form\DataTransformer;

use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use function is_int;
use function is_string;
use function count;
use function current;

class ModelIdToChoiceTransformer implements DataTransformerInterface
{
    private ChoiceListInterface $choiceList;

    public function __construct(ChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * Ищет среди списка опцию с идентификатором сущности, соответствующим уканному в форме
     *
     * @param mixed $value Идентификатор сущности
     *
     * @return mixed
     */
    public function transform($value): mixed
    {
        if (null !== $value && !is_int($value) && !is_string($value)) {
            throw new TransformationFailedException('Expected a string, integer or null.');
        }

        $choices = $this->choiceList->getChoicesForValues([(string) $value]);

        if (1 !== count($choices)) {
            if (null === $value || '' === $value || 0 === $value) {
                return null;
            }

            throw new TransformationFailedException(sprintf('The choice "%s" does not exist or is not unique.', $value));
        }

        return current($choices);
    }

    /**
     * Возвращает идентификатор сущности, полученный из выбранной опции
     *
     * @param mixed $value
     *
     * @return int|string|null
     */
    public function reverseTransform($value): int|string|null
    {
        $modelId = current($this->choiceList->getValuesForChoices([$value]));
        if (false === $modelId) {
            return null;
        }

        return $modelId;
    }
}
