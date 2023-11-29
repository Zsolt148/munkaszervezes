<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Park
 *
 * @property int $id
 * @property string $name
 * @property int|null $zip
 * @property string|null $city
 * @property string|null $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 *
 * @method static \Database\Factories\ParkFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Park newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Park newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Park query()
 * @method static \Illuminate\Database\Eloquent\Builder|Park whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Park whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Park whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Park whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Park whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Park whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Park whereZip($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 *
 * @mixin \Eloquent
 */
class Park extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'zip',
        'city',
        'address',
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'park_id');
    }
}
