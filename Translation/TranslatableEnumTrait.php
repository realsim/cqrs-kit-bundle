<?php

namespace Mechanic\CqrsKit\Translation;

use Symfony\Component\String\ByteString;

use function get_class;
use function sprintf;
use function strtolower;

trait TranslatableEnumTrait
{
    /**
     * @return mixed
     * @see Enum::getKey()
     */
    abstract public function getKey();

    public function getTranslationLabel(?string $translationPrefix = null): string
    {
        $enumClass = new ByteString(get_class($this));
        $enumType = $enumClass->afterLast('\\')->snake()->toString();
        $baseLabel = sprintf('%s.%s', $enumType, strtolower($this->getKey()));

        return $translationPrefix ? "$translationPrefix.$baseLabel" : $baseLabel;
    }
}
