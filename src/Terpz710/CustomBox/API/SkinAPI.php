<?php

declare(strict_types=1);

namespace Terpz710\CustomBox\API;

class SkinAPI
{
    public static function pngToBytes(string $path): string
    {
        $img = @imagecreatefrompng($path);
        if (!$img) {
            throw new \RuntimeException("Failed to create image from PNG file at path: " . $path);
        }
        
        $bytes = "";
        $imageSize = @getimagesize($path);
        if (!$imageSize) {
            throw new \RuntimeException("Failed to get image size for file at path: " . $path);
        }

        [$width, $height] = $imageSize;
        
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $bytes .= chr(($rgba >> 16) & 0xff) . chr(($rgba >> 8) & 0xff) . chr($rgba & 0xff) . chr(((~((int)($rgba >> 24))) << 1) & 0xff);
            }
        }

        @imagedestroy($img);
        return $bytes;
    }
}
