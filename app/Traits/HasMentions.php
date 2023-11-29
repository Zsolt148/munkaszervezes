<?php

namespace App\Traits;

use App\Events\MentionEvent;
use App\Models\Admin;
use DOMDocument;
use Illuminate\Database\Eloquent\Model;

trait HasMentions
{
    // Used in MentionList.vue or Ckeditor/config.js
    protected static $mentionElement = 'span';

    protected static $mentionKey = 'data-user-id';

    // Default class to notify
    protected static $mentionClass = Admin::class;

    protected static function bootHasMentions()
    {
        $callback = function (Model $model) {
            static::checkMention($model);
        };

        static::created($callback);
        static::updated($callback);
    }

    /**
     * Default mention field to watch
     * Works with wyswyg components
     */
    abstract public static function mentionField(): string;

    abstract public function mentionTitle(): string;

    abstract public function mentionBody(): string;

    abstract public function mentionRoute(): string;

    protected static function checkMention(Model $model): void
    {
        $ids = [];
        $field = $model->{static::mentionField()};

        if (empty($field)) {
            return;
        }

        $dom = new DOMDocument;
        @$dom->loadHTML($field); // @: DOMDocument::loadHTML(): Tag figure invalid in Entity

        foreach ($dom->getElementsByTagName(static::$mentionElement) as $tag) {

            $attributes = [];

            foreach ($tag->attributes as $name => $value) {
                $attributes[$name] = $tag->getAttribute($name);
            }

            if (array_key_exists(static::$mentionKey, $attributes)) {
                $ids[] = $attributes[static::$mentionKey];
            }
        }

        static::dispatchMentionEvent($ids, $model);
    }

    protected static function dispatchMentionEvent(string|array $ids, Model $model): void
    {
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        foreach ($ids as $id) {
            $class = app(static::$mentionClass);
            MentionEvent::dispatch($class::find($id), $model);
        }
    }
}
