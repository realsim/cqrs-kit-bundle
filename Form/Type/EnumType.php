<?php

namespace Mechanic\CqrsKit\Form\Type;

use Mechanic\CqrsKit\Form\DataTransformer\EnumToValueTransformer;
use Mechanic\CqrsKit\Form\DataTransformer\EnumsToValuesTransformer;
use MyCLabs\Enum\Enum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function is_subclass_of;

class EnumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (true === $options['multiple']) {
            $builder->addModelTransformer(new EnumsToValuesTransformer($options['enum_class']));
        } else {
            $builder->addModelTransformer(new EnumToValueTransformer($options['enum_class']));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('enum_class');
        $resolver->setAllowedTypes('enum_class', ['string']);
        $resolver->setAllowedValues('enum_class', fn($enumClass) => is_subclass_of($enumClass, Enum::class));

        $resolver->setDefaults([
            'choice_loader' => function (Options $options) {
                return ChoiceList::lazy($this, function () use ($options) {
                    foreach ($options['enum_class']::values() as $enum) {
                        yield $enum->getKey() => $enum->getValue();
                    }
                }, $options['enum_class']);
            },
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
