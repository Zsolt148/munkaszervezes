<?php

namespace App\Helpers\Interfaces;

interface MentionInterface
{
    public static function mentionField(): string;

    public function mentionTitle(): string;

    public function mentionBody(): string;

    public function mentionRoute(): string;
}
