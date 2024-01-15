<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $image, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        // Rename file
        $file = md5(uniqid("pict_", true)).'.webp';

        // Get data of image
        $imageData = getimagesize($image);

        if (false === $imageData) {
            throw new \Exception('Format d\'image non supporté');
        }

        // Check the image format
        switch ($imageData['mime']) {
            case 'image/png':
                $imageSource = imagecreatefrompng($image);
                break;
            case 'image/jpeg':
                $imageSource = imagecreatefromjpeg($image);
                break;
            case 'image/webp':
                $imageSource = imagecreatefromwebp($image);
                break;
            default:
                throw new \Exception('Format d\'image non supporté');
        }

        // Get dimensions of image
        $imageWidth = $imageData[0];
        $imageHeight = $imageData[1];

        // Check orientation of image
        switch ($imageWidth <=> $imageHeight) {
            case -1: // portrait
                $squareSize = $imageWidth;
                $srcX = 0;
                $srcY = ($imageHeight - $squareSize) / 2;
                break;
            case 0: // square
                $squareSize = $imageWidth;
                $srcX = 0;
                $srcY = 0;
                break;
            case 1: // landscape
                $squareSize = $imageHeight;
                $srcX = ($imageWidth - $squareSize) / 2;
                $srcY = 0;
                break;
        }

        // Create a new image
        $resizedImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($resizedImage, $imageSource, 0, 0, $srcX, $srcY, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('upload_directory').$folder;

        // Create destination folder if not exist
        if (!file_exists($path.'/thumbnail/')) {
            mkdir($path.'/thumbnail/', 0755, true);
        }

        // Save image
        imagewebp($resizedImage, $path.'/thumbnail/'.$width.'x'.$height.'-'.$file);

        // Move file to destination folder
        $image->move($path.'/', $file);

        // Return file name
        return $file;
    }

    public function delete(string $file, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        if ('default.webp' !== $file) {
            $success = false;
            $path = $this->params->get('upload_directory').$folder;

            $mini = $path.'thumbnail/'.$width.'x'.$height.'-'.$file;
            if (file_exists($mini)) {
                unlink($mini);
                $success = true;
            }

            $original = $path.'/'.$file;
            if (file_exists($original)) {
                unlink($original);
                $success = true;
            }

            return $success;
        }

        return false;
    }
}
