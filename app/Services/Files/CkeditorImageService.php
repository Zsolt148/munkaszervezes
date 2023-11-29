<?php

namespace App\Services\Files;

use App\Services\Files\ImageResize\ImageResizeManager;
use App\Services\Files\ImageResize\ImageResizerContract;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\Image;

class CkeditorImageService
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $disk;

    /**
     * @var array
     */
    private $generalFileSizes;

    /**
     * @var ImageResizerContract
     */
    private $resizer;

    public function __construct(
        Repository $config,
        FilesystemManager $filesystemManager,
        ImageResizeManager $imageResizeManager
    ) {
        $this->disk = $filesystemManager->disk($config->get('filesystems.image_disk', 'public'));
        $this->resizer = $imageResizeManager->driver($config->get('filesystems.image_resize_driver', 'intervention'));

        $this->generalFileSizes = [
            '160' => [160, 160],
            '500' => [500, 500],
            '1080' => [1080, null],
            //'1980' 	=> [1980, null],
        ];
    }

    /**
     * @return void
     */
    public function createGeneralImages(string $path, UploadedFile $file, string $name, string $extension = 'webp')
    {
        $sourcePath = $file->getRealPath();

        try {
            // Create different sizes.
            foreach ($this->generalFileSizes as $sizeName => $imageSize) {

                $newFileName = $this->imageVariantFileName(
                    $name,
                    $sizeName,
                    $extension
                );

                $stream = $this->createImageStream(
                    $sourcePath,
                    $imageSize[0],
                    $imageSize[1],
                    $extension,
                );

                $this->disk->put($path.$newFileName, $stream);
            }
        } catch (NotReadableException $exception) {
            throw $exception;
        }
    }

    /**
     * @return array
     */
    public function generalImageUrls(string $path, string $fileName, string $extension = 'webp')
    {
        $urls = [];

        foreach ($this->generalFileSizes as $sizeName => $imageSize) {

            $urls[$sizeName] = $this->disk->url(
                $path.$this->imageVariantFileName(
                    $fileName,
                    $sizeName,
                    $extension
                )
            );
        }

        // default is the biggest
        $urls['default'] = end($urls);

        return $urls;
    }

    public function resize(string $path, ?int $width = null, ?int $height = null): Image
    {
        return $this->resizer->resize($path, $width, $height);
    }

    /**
     * @param    $name
     * @param  string  $variantName
     */
    protected function imageVariantFileName(
        string $filename,
        string $sizeName,
        string $extension
    ): string {
        return $filename.'_'.$sizeName.'_'.'.'.$extension;
    }

    /**
     * @param  string  $variant
     * @return mixed|null
     */
    protected function createImageStream(
        string $sourcePath,
        ?int $width,
        ?int $height,
        string $extension,
        int $quality = 90
    ) {
        return $this
            ->resizer
            ->resizeStream($sourcePath, $width, $height, $extension, $quality);
    }

    /**
     * @return string|string[]|null
     */
    public function rewriteFilenameToWebp($filename)
    {
        return preg_replace('/\.(jpe?g)$/', '.webp', $filename);
    }
}
