<?php

namespace Chatter\Middleware\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Filter
{
    protected $allowedTypes = ['image/jpeg', 'image/png'];

    public function filter(UploadedFile $uploadedFile)
    {
        $type = $uploadedFile->getClientMimeType();

        if (!in_array($type, $this->allowedTypes)) {
            throw new \Exception('Unauthorized type');
        }

        $file = $uploadedFile->move(
            'assets/images/',
            $uploadedFile->getClientOriginalName()
        );

        return $file->getPathname();
    }
}