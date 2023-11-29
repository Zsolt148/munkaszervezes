<?php

namespace App\Services\Files\ImageResize;

use Intervention\Image\Constraint;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;
use Psr\Http\Message\StreamInterface;

class Intervention implements ImageResizerContract
{
    public function resizeStream(string $sourcePath, ?int $width, ?int $height, string $extension, int $quality = 90): StreamInterface
    {
        return Image::make($sourcePath)
            ->orientate()
            ->resize(
                $width,
                $height,
                function (Constraint $constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                }
            )
            ->stream($extension, $quality);
    }

    public function thumbnail(string $sourcePath, ?int $width, ?int $height, string $extension, int $quality = 90): StreamInterface
    {
        return Image::make($sourcePath)
            ->orientate()
            ->fit($width, $height, function (Constraint $constraint) {
                $constraint->upsize();
                $constraint->aspectRatio();
            })
            ->stream($extension, $quality);
    }

    public function fit(string $sourcePath, ?int $width = null, ?int $height = null): InterventionImage
    {
        return Image::make($sourcePath)
            ->orientate()
            ->fit(
                $width,
                $height,
                function (Constraint $constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                }
            )
            ->save($sourcePath);
    }

    public function resize(string $sourcePath, ?int $width = null, ?int $height = null): InterventionImage
    {
        return Image::make($sourcePath)
            ->orientate()
            ->resize(
                $width,
                $height,
                function (Constraint $constraint) {
                    $constraint->upsize();
                    $constraint->aspectRatio();
                }
            )
            ->save($sourcePath);
    }

    public function resizeCanvas(string $sourcePath, ?int $width = null, ?int $height = null): InterventionImage
    {
        return Image::make($sourcePath)
            ->orientate()
            ->resizeCanvas(
                $width,
                $height
            )
            ->save($sourcePath);
    }

    public function crop(string $sourcePath, int $width, int $height, int|null $x = null, int|null $y = null): InterventionImage
    {
        return Image::make($sourcePath)
            ->crop($width, $height, $x, $y)
            ->save($sourcePath);
    }

    public function rotate(string $sourcePath, float $angle): InterventionImage
    {
        return Image::make($sourcePath)
            ->rotate($angle)
            ->save($sourcePath);
    }

    public function flip(string $sourcePath, string $mode = 'h'): InterventionImage
    {
        return Image::make($sourcePath)
            ->flip($mode)
            ->save($sourcePath);
    }
}
