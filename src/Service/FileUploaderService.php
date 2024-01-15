<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderService
{
    public function __construct(
        private string $targetDirectory,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
        $fileName = md5(uniqid()).'.webp';

        $fileInfos = getimagesize((string) $file);

        if (false === $fileInfos) {
            throw new \Exception("format d'image incorrect");
        }

        switch ($fileInfos['mime']) {
            case 'image/png':
                $fileSource = imagecreatefrompng((string)$file);
                break;
            case 'image/jpeg':
                $fileSource = imagecreatefromjpeg((string)$file);
                break;
            case 'image/webp':
                $fileSource = imagecreatefromwebp((string)$file);
                break;
            default:
                throw new \Exception("format d'image incorrect");
        }

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
