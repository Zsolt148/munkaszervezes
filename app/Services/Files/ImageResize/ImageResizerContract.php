<?php

namespace App\Services\Files\ImageResize;

use Intervention\Image\Image;
use Psr\Http\Message\StreamInterface;

interface ImageResizerContract
{
    public function resizeStream(string $sourcePath, ?int $width, ?int $height, string $extension, int $quality = 90): StreamInterface;

    public function thumbnail(string $sourcePath, int $width, int $height, string $extension, int $quality = 90): StreamInterface;

    public function fit(string $sourcePath, ?int $width = null, ?int $height = null): Image;

    public function resize(string $sourcePath, ?int $width = null, ?int $height = null): Image;

    public function resizeCanvas(string $sourcePath, ?int $width = null, ?int $height = null): Image;

    public function crop(string $sourcePath, int $width, int $height, int|null $x = null, int|null $y = null): Image;

    public function rotate(string $sourcePath, float $angle): Image;

    public function flip(string $sourcePath, string $mode = 'h'): Image;
}
