<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ThumbnailService
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        $file = md5(uniqid((string) random_int(0, getrandmax()), true)).'.webp';

        $pictureInfos = getimagesize((string) $picture);

        if (false === $pictureInfos) {
            throw new \Exception("format d'image incorrect");
        }

        switch ($pictureInfos['mime']) {
            case 'image/png':
                $pictureSource = imagecreatefrompng((string) $picture);
                break;
            case 'image/jpeg':
                $pictureSource = imagecreatefromjpeg((string) $picture);
                break;
            case 'image/webp':
                $pictureSource = imagecreatefromwebp((string) $picture);
                break;
            default:
                throw new \Exception("format d'image incorrect");
        }

        $imageWidth = $pictureInfos[0];
        $imageHeight = $pictureInfos[1];

        switch ($imageWidth <=> $imageHeight) {
            case -1:
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = (int) (($imageHeight - $squareSize) / 2);
                break;
            case 0:
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case 1:
                $squareSize = $imageHeight;
                $src_x = (int) (($imageWidth - $squareSize) / 2);
                $src_y = 0;
                break;
        }

        $resizedPicture = imagecreatetruecolor($width, $height);
        imagecopyresampled($resizedPicture, $pictureSource, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory').$folder;

        if(!file_exists($path.'/thumbnail/')) {
            mkdir($path.'/thumbnail/', 0755, true);
        }

        imagewebp($resizedPicture, $path.'/thumb/'.$width.'x'.$height.'-'.$file);

        $picture->move($path.'/', $file);

        return $file;
    }

    public function delete(string $file, ?string $folder = '', ?int $width = 250, ?int $height = 250)
    {
        if ('default.webp' !== $file) {
            $success = false;
            $path = $this->params->get('images_directory').$folder;
            $thumb = $path.'/thumb/'.$width.'x'.$height.'-'.$file;

            if (file_exists($thumb)) {
                unlink($thumb);
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
