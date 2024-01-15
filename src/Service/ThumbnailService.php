<?php 

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ThumbnailService
{


    public function __construct(
        private string $targetDirectory,
    ) {
    }

     /**
     * Crée un thumbnail de l'image reçu et l'enregistre sur le disque
     *
     * @param Image $image
     * @return void
     */
    public function create( UploadedFile $file , string $filename)
    {
        // On va resize l'image en 16x9 généré plus tôt !!!
        $fullPath = $image->getPath() . '/cropped/' . $image->getName();
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $savingFullPath = $image->getPath() . '/thumbnail/' . $image->getName();
        

        // On utilise la bonne fonction de création d'image dans GD en fonction de l'extension
        if($extension === 'jpeg' || $extension === 'jpg')
        {
            $originalImg = imagecreatefromjpeg($fullPath);
        }
        else if($extension == 'png')
        {
            $originalImg = imagecreatefrompng($fullPath);
        }

        // Si le dossier thumbnail n'existe pas encore, on le crée, les fonctions imagejpeg et imagepng de GD ne le font pas !
        if (!file_exists($image->getPath() . '/thumbnail'))
        {
            mkdir($image->getPath() . '/thumbnail', 0777, true);
        }

        // Récupération de la taille de l'image source
        list($width, $height) = getimagesize($fullPath);

        // On resize si l'image n'est pas déjà en-dessous ou égal à la taille ciblé
        if(!(($width <= $newWidth) && ($height <= $newHeight)))
        {
            // Création du thumbnail
            $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
            // Redimensionnement
            $resizeSuccess = imagecopyresized($thumbnail, $originalImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            if ($resizeSuccess !== FALSE) 
            {   
                // On utilise la bonne fonction de création d'image en fonction de l'extension
                if($extension === 'jpeg' || $extension === 'jpg')
                {
                    imagejpeg($thumbnail, $savingFullPath);
                }
                else if($extension == 'png')
                {
                    imagepng($thumbnail, $savingFullPath);
                }
                // On détruit l'image resizée chargée en RAM
                imagedestroy($thumbnail);
            }
        }
        else
        {
            // On utilise la bonne fonction de création d'image en fonction de l'extension
            if($extension === 'jpeg' || $extension === 'jpg')
            {
                imagejpeg($originalImg, $savingFullPath);
            }
            else if($extension == 'png')
            {
                imagepng($originalImg, $savingFullPath);
            }
        }
        // On détruit l'image original chargée en RAM
        imagedestroy($originalImg);
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}

// class ThumbnailService
// {
//     private ParameterBagInterface $params;

//     public function __construct(ParameterBagInterface $params)
//     {
//         $this->params = $params;
//     }

//     public function add(UploadedFile $file, ?string $folder = 'thumbnail', ?int $width = 300, ?int $height = 300)
//     {
//         $originalFilename = pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
//         $fileInfos = getimagesize((string) $file);

//         if (false === $fileInfos) {
//             throw new \Exception("format d'image incorrect");
//         }

//         switch ($fileInfos['mime']) {
//             case 'image/png':
//                 $fileSource = imagecreatefrompng((string) $file);
//                 break;
//             case 'image/jpeg':
//                 $fileSource = imagecreatefromjpeg((string) $file);
//                 break;
//             case 'image/webp':
//                 $fileSource = imagecreatefromwebp((string) $file);
//                 break;
//             default:
//                 throw new \Exception("format d'image incorrect");
//         }

//         $fileWidth = $fileInfos[0];
//         $fileHeight = $fileInfos[1];

//         switch ($fileWidth <=> $fileHeight) {
//             case -1:
//                 $squareSize = $fileWidth;
//                 $src_x = 0;
//                 $src_y = (int) (($fileHeight - $squareSize) / 2);
//                 break;
//             case 0:
//                 $squareSize = $fileWidth;
//                 $src_x = 0;
//                 $src_y = 0;
//                 break;
//             case 1:
//                 $squareSize = $fileHeight;
//                 $src_x = (int) (($fileWidth - $squareSize) / 2);
//                 $src_y = 0;
//                 break;
//         }

//         $resizedFile = imagecreatetruecolor($width, $height);
//         imagecopyresampled($resizedFile, $fileSource, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

//         $path = $this->params->get('upload_directory').$folder;

//         if(!file_exists($path.'/' .$folder.'/')) {
//             mkdir($path.'/'.$folder.'/', 0755, true);
//         }

//         imagewebp($resizedFile, $path.$width.'x'.$height.'-'.$originalFilename);

//         $file->move($path.'/', $file);

//         return $file;
//     }

//     public function delete(string $file, ?string $folder = '', ?int $width = 250, ?int $height = 250)
//     {
//         if ('default.webp' !== $file) {
//             $success = false;
//             $path = $this->params->get('images_directory').$folder;
//             $thumb = $path.'/thumb/'.$width.'x'.$height.'-'.$file;

//             if (file_exists($thumb)) {
//                 unlink($thumb);
//                 $success = true;
//             }

//             $original = $path.'/'.$file;

//             if (file_exists($original)) {
//                 unlink($original);
//                 $success = true;
//             }

//             return $success;
//         }

//         return false;
//     }
// }
