<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploaderService
{
    public function __construct(
        private ParameterBagInterface $params,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file, ?string $folder = ''): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $path = $this->params->get('uploads_directory') . $folder;
        try {
            $file->move("{$path}/", $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function delete(string $file, ?string $folder = ''): bool
    {
        if ($file !== 'default.webp') {
            $path = $this->params->get('uploads_directory') . $folder;

            $original = $path . '/' . $file;

            if (file_exists($original)) {
                $success = unlink($original);
            }
            return true;
        }
        return false;
    }
}
