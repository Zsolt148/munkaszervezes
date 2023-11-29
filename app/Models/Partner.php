<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Partner
 *
 * @property int $id
 * @property int $created_by
 * @property string $name
 * @property string $zip
 * @property string $city
 * @property string $address
 * @property string|null $tax_number
 * @property string|null $communal_tax_number
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Admin $createdBy
 *
 * @method static Builder|Partner newModelQuery()
 * @method static Builder|Partner newQuery()
 * @method static \Illuminate\Database\Query\Builder|Partner onlyTrashed()
 * @method static Builder|Partner query()
 * @method static Builder|Partner whereAddress($value)
 * @method static Builder|Partner whereCity($value)
 * @method static Builder|Partner whereCommunalTaxNumber($value)
 * @method static Builder|Partner whereCreatedAt($value)
 * @method static Builder|Partner whereCreatedBy($value)
 * @method static Builder|Partner whereDeletedAt($value)
 * @method static Builder|Partner whereId($value)
 * @method static Builder|Partner whereName($value)
 * @method static Builder|Partner whereTaxNumber($value)
 * @method static Builder|Partner whereUpdatedAt($value)
 * @method static Builder|Partner whereZip($value)
 * @method static \Illuminate\Database\Query\Builder|Partner withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Partner withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Partner extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'created_by',
        'name',
        'zip',
        'city',
        'address',
        'tax_number',
        'communal_tax_number',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
