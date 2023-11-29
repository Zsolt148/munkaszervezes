<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PlanVariant
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PlanVariant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanVariant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanVariant query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlanVariant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanVariant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanVariant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlanVariant whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PlanVariant extends Model
{
    use HasFactory;

    protected $table = 'plan_variants';

    protected $fillable = [
        'name',
    ];
}
