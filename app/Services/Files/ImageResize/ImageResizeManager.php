<?php

namespace App\Services\Files\ImageResize;

use Illuminate\Support\Manager;

class ImageResizeManager extends Manager
{
    /**
     * @return \App\ImageResize\ImageResizerContract
     */
    public function createInterventionDriver(): ImageResizerContract
    {
        return new Intervention();
    }

    /**
     * @param  null  $driver
     * @return \App\ImageResize\ImageResizerContract
     */
    public function driver($driver = null)
    {
        return parent::driver($driver);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultDriver()
    {
        return 'intervention';
    }
}
