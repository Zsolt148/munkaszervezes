<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Settings
 *
 * @property int $id
 * @property string|null $facebook_link
 * @property string|null $instagram_link
 * @property string|null $twitter_link
 * @property array|null $slogan
 * @property array|null $short_description
 * @property string|null $country
 * @property string|null $zip
 * @property string|null $city
 * @property string|null $address
 * @property string|null $apartment
 * @property string|null $phone
 * @property string|null $email
 * @property array|null $seo_page_title
 * @property array|null $seo_page_url
 * @property array|null $seo_page_description
 * @property string|null $seo_page_author
 * @property string|null $facebook_pixel_code
 * @property string|null $google_analytics_code
 * @property string|null $google_tag_manager_code
 * @property int $is_maintenance
 * @property string|null $maintenance_password
 * @property array|null $allowed_ips
 * @property string|null $htaccess_username
 * @property string|null $htaccess_password
 * @property int $dark_mode
 * @property string|null $primary_color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|Media[] $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereAllowedIps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereApartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereDarkMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereFacebookLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereFacebookPixelCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereGoogleAnalyticsCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereGoogleTagManagerCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereHtaccessPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereHtaccessUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereInstagramLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereIsMaintenance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereMaintenancePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereSeoPageAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereSeoPageDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereSeoPageTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereSeoPageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereSlogan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereTwitterLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereZip($value)
 *
 * @mixin \Eloquent
 */
class Settings extends Model implements HasMedia
{
    use HasFactory, HasTranslations, InteractsWithMedia;

    protected $fillable = [
        'facebook_link',
        'instagram_link',
        'twitter_link',
        'slogan',
        'short_description',
        'country',
        'zip',
        'city',
        'address',
        'apartment',
        'phone',
        'email',
        'seo_page_title',
        'seo_page_url',
        'seo_page_description',
        'seo_page_author',
        'facebook_pixel_code',
        'google_analytics_code',
        'google_tag_manager_code',
        'is_maintenance',
        'maintenance_password',
        'allowed_ips',
        'htaccess_username',
        'htaccess_password',
        'primary_color',
        'dark_mode',
    ];

    public $translatable = [
        'slogan',
        'short_description',
        'seo_page_title',
        'seo_page_url',
        'seo_page_description',
    ];

    protected $casts = [
        'allowed_ips' => 'array',
    ];

    /**
     * @return void
     */
    protected static function booted()
    {
        static::updated(function ($settings) {
            cache()->forget('settings');
        });
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('favicon')
            ->width(16)
            ->height(16)
            ->performOnCollections('favicons');
    }

    public function IsBasicAuthEnabled(): bool
    {
        return $this->htaccess_username && $this->htaccess_password;
    }
}
