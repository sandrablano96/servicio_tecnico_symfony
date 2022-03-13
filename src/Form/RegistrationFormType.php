<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use \Symfony\Component\Form\Extension\Core\Type\FileType;
use \Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\File;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Introduzca la contraseña',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'La contraseña debe tener al menos 4 carácteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Introduzca el email',
                    ])
            ],
            ])
            ->add('nombre', TextType::class,  [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Introduzca su nombre',
                    ])
            ],
            ])
            ->add('apellidos', TextType::class,  [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Introduzca sus apellidos',
                    ])
            ],
            ])
            ->add('telefono', TelType::class,  [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Introduzca su telefono',
                    ])
            ],
            ])
            ->add('foto', FileType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Introduzca su foto',
                    ]),
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'El archivo seleccionado no tiene un formato válido',
                    ])
            ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
