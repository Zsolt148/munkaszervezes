<?php

namespace App\Traits;

use Spatie\Translatable\HasTranslations as BaseHasTranslations;

trait HasTranslations
{
    use BaseHasTranslations;

    public function toArray()
    {
        return parent::toArray();

        /*
         * Get one translation for each translatable field
        foreach ($this->getTranslatableAttributes() as $field) {
            $attributes[$field] = $this->getTranslation($field, \App::getLocale());
        }

        return $attributes;
        */
    }
}
