<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'is_maintenance' => $this->is_maintenance,
            'maintenance_password' => $this->maintenance_password,
            'allowed_ips' => $this->allowed_ips ?? null,

            'htaccess_username' => $this->htaccess_username,
            'htaccess_password' => $this->htaccess_password,

            'logo' => $this->getMedia('logos')->first()?->getUrl() ?? null,
            'favicon' => $this->getMedia('favicons')->first()?->getUrl('favicon') ?? null,

            'facebook_link' => $this->facebook_link,
            'instagram_link' => $this->instagram_link,
            'twitter_link' => $this->twitter_link,

            'slogan' => $this->getTranslations('slogan'),
            'short_description' => $this->getTranslations('short_description'),
            'country' => $this->country,
            'zip' => $this->zip,
            'city' => $this->city,
            'address' => $this->address,
            'apartment' => $this->apartment,
            'phone' => $this->phone,
            'email' => $this->email,

            'seo_page_author' => $this->seo_page_author,
            'seo_page_url' => $this->getTranslations('seo_page_url'),
            'seo_page_title' => $this->getTranslations('seo_page_title'),
            'seo_page_description' => $this->getTranslations('seo_page_description'),

            'facebook_pixel_code' => $this->facebook_pixel_code,
            'google_analytics_code' => $this->google_analytics_code,
            'google_tag_manager_code' => $this->google_tag_manager_code,

            'primary_color' => $this->primary_color,

        ];
    }
}
