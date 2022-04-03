<?php

namespace Mechanic\CqrsKit\Translation;

interface TranslatableEnumInterface
{
    public function getTranslationLabel(?string $translationPrefix = null): string;
}
