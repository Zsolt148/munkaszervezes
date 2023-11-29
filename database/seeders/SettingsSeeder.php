<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        activity()->withoutLogs(function () {
            Settings::updateOrCreate(['id' => 1], [
                'facebook_link' => null,
                'instagram_link' => null,
                'twitter_link' => null,
                'slogan' => null,
                'short_description' => null,
                'country' => null,
                'zip' => null,
                'city' => null,
                'address' => null,
                'apartment' => null,
                'phone' => null,
                'email' => null,
                'seo_page_title' => null,
                'seo_page_url' => null,
                'seo_page_description' => null,
                'seo_page_author' => null,
                'facebook_pixel_code' => null,
                'google_analytics_code' => null,
                'google_tag_manager_code' => null,
                'primary_color' => '#16b1ff',
            ]);
        });
    }
}
