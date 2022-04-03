<?php

namespace Mechanic\CqrsKit\Form\DataTransformer;

use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use function is_array;
use function count;

class ModelIdListToChoicesTransformer implements DataTransformerInterface
{
    private ChoiceListInterface $choiceList;

    public function __construct(ChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    public function transform($value)
    {
        if (null === $value) {
            return [];
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $choices = $this->choiceList->getChoicesForValues($value);

        if (count($choices) !== count($value)) {
            throw new TransformationFailedException('Could not find all matching choices for the given values.');
        }

        return $choices;
    }

    public function reverseTransform($value)
    {
        if (null === $value) {
            return [];
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return $this->choiceList->getValuesForChoices($value);
    }
}
