<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Media;
use App\Entity\TypeMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeMedia', EnumType::class, [
                'label'       => false,
                'class'       => TypeMedia::class,
            ])

            ->add('file', FileType::class, [
                'label'             => false,
                'required'          => false,
                'error_bubbling'    => false,
                'attr'              => [
                    'accept' => 'image/*',
                ],
                'help'              => 'Taille maximale du fichier 2Mo',
                'constraints'       => [
                    new Image([
                        'maxSize'        => '2048k',
                        'maxSizeMessage' => 'La taille maximale ne doit pas dépassée 2Mo',
                        'mimeTypes'      => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une Image Valide de 2Mo maximun et au format(jpg, jpeg, webp, png)',
                    ])],
            ])
            ->add('path', TextType::class, [
                'required'          => false,
                'label'             => 'Copier le lien "embed" ou "integrer" de la video ci-dessous',
                'attr'              => [
                    'placeholder' => '<iframe ...>...</iframe>',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
