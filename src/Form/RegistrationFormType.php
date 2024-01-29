<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('password', PasswordType::class, [
                'attr'        => ['autocomplete' => 'new-password'],
            ])

            ->add('confirmPassword', PasswordType::class, [
                'mapped'      => false,
            ])
            // ->add('avatar', FileType::class)

            ->add('rgpdTerms', CheckboxType::class, [
                'mapped'      => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'valider les conditions',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
