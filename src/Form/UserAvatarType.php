<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserAvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('avatar', FileType::class, [
                'label' => 'Selectionner votre avatar',

                'required'    => false,
                'mapped'      => false,
                'constraints' => [
                    new File([
                        'maxSize'   => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci de selectionner une image au format jpeg, png ou webp',
                        'maxSizeMessage'   => 'Taille Maximale 2Mo',
                    ]),
                ],
            ])
            ->add('modifyAvatar', SubmitType::class, ['label' => 'Modifier'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'      => User::class,
            'user'            => User::class,
            'csrf_field_name' => '_token',
        ]);
    }
}
