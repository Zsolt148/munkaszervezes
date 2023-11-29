<?php

namespace App\Services\Files;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PathGenerator implements \Spatie\MediaLibrary\Support\PathGenerator\PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media).'/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media).'/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media).'/responsive-images/';
    }

    /*
     * Get a unique base path for the given media.
     */
    protected function getBasePath(Media $media): string
    {
        $prefix = config('media-library.prefix', '');

        $folder = Str::slug($media->getKey().'-'.$media->uuid);

        if ($prefix !== '') {
            return $prefix.'/'.$folder;
        }

        return $folder;
    }
}
