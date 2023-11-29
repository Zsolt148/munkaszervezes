<?php

namespace App\Http\Controllers;

use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return \App\Models\Admin|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return Auth::guard('admin')->user();
    }

    /**
     * @throws \Exception
     */
    public function syncTags(Model $model, iterable $tags, string $type = null): void
    {
        if (! method_exists($model, 'syncTagsWithTypeAndLocale')) {
            throw new \Exception(
                'Function [syncTagsWithTypeAndLocale] does not exists on ['.class_basename($model).'] class. 
				The model should implement ['.class_basename(HasTags::class).'] trait.'
            );
        }

        foreach ($tags as $lang => $tag) {
            $model->syncTagsWithTypeAndLocale(mapFromSelect($tag), $type, $lang);
        }
    }
}
