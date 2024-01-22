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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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

                // 'mapped'      => false,
                'attr'        => [
                    'placeholder' => 'Ajouter un Media',
                ],
            ])
        ;

        $formModifier = static function (FormInterface $form, TypeMedia $typeMedia = null): void {
            if('Picture' !== $typeMedia && $typeMedia) {
                $form->add('path', TextType::class, [
                    'required'    => false,
                    'mapped'      => false,
                ]);
            }
        };

        $builder->get('typeMedia')->addEventListener(
            FormEvents::POST_SUBMIT,
            static function (FormEvent $event) use ($formModifier): void {
                $typeMedia = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $typeMedia);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
