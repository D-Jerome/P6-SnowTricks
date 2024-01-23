<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Media;
use App\Entity\TypeMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Webmozart\Assert\Assert;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeMedia', EnumType::class, [
                'label'       => false,
                'placeholder' => 'Choisir un type de média',
                'class'       => TypeMedia::class,
                'required'    => false,
                // 'mapped'      => false,
            ])
            ->add('file', FileType::class, [
                'label'       => false,
                'required'    => false,
                'attr'        => [
                    'placeholder' => 'Ajouter un Media',
                ],
                'constraints' => [
                    new File([
                        'maxSize'   => '2048k',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une Image Valide de 2Mo maximun et au format(jpg, jpeg, webp, png)',
                    ])],
            ])
            ->add('path', HiddenType::class, [
            ])
        ;

        // $formModifier = static function (FormInterface $form, TypeMedia $typeMedia = null): void {
        //     if('Picture' !== $typeMedia && $typeMedia) {
        //         $form->add('path', TextType::class, [
        //             'required'    => false,
        //             'mapped'      => false,
        //         ]);
        //         $form->remove('file');
        //     }
        // };

        // $builder->addEventListener(
        //     FormEvents::POST_SET_DATA,
        //     static function (FormEvent $event) use ($formModifier): void {
        //         $typeMedia = $event->getForm()->getData();
        //         if ($typeMedia) {
        //             Assert::isInstanceOf($typeMedia, TypeMedia::class);
        //             Assert::notNull($event->getForm()->getParent());
        //             $formModifier($event->getForm()->getParent(), $typeMedia);
        //         }
        //     });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
