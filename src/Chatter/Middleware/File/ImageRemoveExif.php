<?php

namespace Chatter\Middleware\File;

class ImageRemoveExif
{
    public static function removeExif($imagePath)
    {
        $info = pathinfo($imagePath);

        if (in_array($info['extension'], ['jpg', 'jpeg'])) {
            $pngFilename = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.png';
            $image = imagecreatefromjpeg($imagePath);
            imagepng($image, $pngFilename);

            $imagePath = $pngFilename;
        }

        return $imagePath;
    }
}