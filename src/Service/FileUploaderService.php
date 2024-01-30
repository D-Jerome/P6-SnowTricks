<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Webmozart\Assert\Assert;

class FileUploaderService
{
    public function __construct(
        private ParameterBagInterface $params,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file, ?string $folder = ''): ?string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        $path = $this->params->get('uploads_directory');
        Assert::string($path);
        $path .= $folder;
        try {
            $file->move("{$path}/", $fileName);
        } catch (FileException $e) {
            $fileName =  null;
        }

        return $fileName;
    }
}
