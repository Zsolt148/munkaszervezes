<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as DbCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $type
 * @property string $locale
 * @property int|null $order_column
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static Builder|Tag containing(string $name, $locale = null)
 * @method static Builder|Tag newModelQuery()
 * @method static Builder|Tag newQuery()
 * @method static Builder|Tag ordered(string $direction = 'asc')
 * @method static Builder|Tag query()
 * @method static Builder|Tag whereCreatedAt($value)
 * @method static Builder|Tag whereId($value)
 * @method static Builder|Tag whereLocale($value)
 * @method static Builder|Tag whereName($value)
 * @method static Builder|Tag whereOrderColumn($value)
 * @method static Builder|Tag whereSlug($value)
 * @method static Builder|Tag whereType($value)
 * @method static Builder|Tag whereUpdatedAt($value)
 * @method static Builder|Tag withType(?string $type = null)
 * @method static Builder|Tag withLocale(?string $locale = null)
 *
 * @mixin \Eloquent
 */
class Tag extends Model implements Sortable
{
    use SortableTrait;
    use HasFactory;

    public array $translatable = ['name', 'slug'];

    public $fillable = [
        'name',
        'slug',
        'type',
        'locale',
    ];

    public static function getLocale()
    {
        return app()->getLocale();
    }

    public function scopeWithLocale(Builder $query, string $locale = null): Builder
    {
        if (is_null($locale)) {
            return $query;
        }

        return $query->where('locale', $locale)->ordered();
    }

    public function scopeWithType(Builder $query, string $type = null): Builder
    {
        if (is_null($type)) {
            return $query;
        }

        return $query->where('type', $type)->ordered();
    }

    public function scopeContaining(Builder $query, string $name, $locale = null): Builder
    {
        return $query->whereRaw('lower('.$this->getQuery()
            ->getGrammar()
            ->wrap('name').') like ?', ['%'.mb_strtolower($name).'%']
        );
    }

    public static function findOrCreate(
        string|array|ArrayAccess $values,
        string|null $type = null,
        string|null $locale = null,
    ): Collection|\Spatie\Tags\Tag|static {

        $tags = collect($values)->map(function ($value) use ($type, $locale) {
            if ($value instanceof self) {
                return $value;
            }

            return static::findOrCreateFromString($value, $type, $locale);
        });

        return is_string($values) ? $tags->first() : $tags;
    }

    public static function getWithType(string $type): DbCollection
    {
        return static::withType($type)->get();
    }

    public static function findFromString(string $name, string $type = null, string $locale = null)
    {
        return static::query()
            ->where('name', $name)
            ->where('type', $type)
            ->first();
    }

    public static function findFromStringOfAnyType(string $name, string $locale = null)
    {
        return static::query()
            ->where('name', $name)
            ->get();
    }

    protected static function findOrCreateFromString(string $name, string $type = null, string $locale = null)
    {
        $tag = static::findFromString($name, $type, $locale);

        if (! $tag) {
            $tag = static::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'type' => $type,
                'locale' => $locale,
            ]);
        }

        return $tag;
    }

    public static function getTypes(): Collection
    {
        return static::groupBy('type')->pluck('type');
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute($key, $value);
    }

    /**
     * @param  string|null  $type
     */
    public static function tagsGroupByLocale(string|null $type = null): Collection
    {
        return self::query()
            ->when($type, function (Builder $q) use ($type) {
                $q->withType($type);
            })
            ->get()
            ->groupBy('locale')
            ->map(function (Collection $items, string $locale) {
                return $items->pluck('name')->toArray();
            })
            ->toBase();
    }
}
