<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\CallbackTransformer;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = [
            'Utilisateur' => 'ROLE_USER',
            'Administrateur' => 'ROLE_ADMIN'
        ];
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur",
                'constraints' => [
                    new NotBlank([
                        'message' => "Le username ne peut être vide ",
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Au moins {{ limit }} caractères pour le username !!!',
                        // max length allowed by Symfony for security reasons
                        'max' => 32,
                    ]),
                ],
                'attr' => [
                    'placeholder' => "Ex: tchos",
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes ne correspondent',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le mot de passe ne doit pas être vide',
                        ]),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'Le password doit avoir au moins {{ limit }} caractères !!!',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'attr' => [
                        'placeholder' => "Ex: TP#@BKn58Fa66",
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Le mot de passe ne doit pas être vide',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Le password doit avoir au moins {{ limit }} caractères !!!',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'attr' => [
                        'placeholder' => "Ex: TP#@BKn58Fa66",
                    ]
                ],
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add('fullname', TextType::class, [
                'label' => "Nom complet",
                'constraints' => [
                    new NotBlank([
                        'message' => "Le nom complet ne peut être vide ",
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Au moins {{ limit }} caractères pour le nom !!!',
                        // max length allowed by Symfony for security reasons
                        'max' => 32,
                    ]),
                ],
                'attr' => [
                    'placeholder' => "Ex: TUCKO BENEDICTO PACIFICO RUAN MARIA RAMIREZ",
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => "Téléphone",
                'constraints' => [
                    new NotBlank([
                        'message' => "Le nom complet ne peut être vide ",
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Téléphone doit avoir au moins {{ limit }} caractères !!!',
                        // max length allowed by Symfony for security reasons
                        'max' => 32,
                    ]),
                ],
                'attr' => [
                    'placeholder' => "Ex: 699887766 / 677665544 / 622110000",
                ]
            ])
            ->add('ministere', ChoiceType::class, [
                'label' => 'Ministère dans lequel vous travaillez',
                'choices' => [
                    'PRC' => 'PRC',
                    'MINRA' => 'MINRA',
                    'SPM' => 'SPM',
                    'DGSN' => 'DGSN',
                    'MINREX' => 'MINREX',
                    'MINREST' => 'MINREST',
                    'MINAT' => 'MINAT',
                    'MINJUSTICE' => 'MINJUSTICE',
                    'MINDDEVEL' => 'MINDDEVEL',
                    'MINMAP' => 'MINMAP',
                    'MINDEF' => 'MINDEF',
                    'MINAC' => 'MINAC',
                    'MINEDUB' => 'MINEDUB',
                    'MINSEP' => 'MINSEP',
                    'MINCOM' => 'MINCOM',
                    'MINESUP' => 'MINESUP',
                    'MINRESI' => 'MINRESI',
                    'MINFI' => 'MINFI',
                    'MINCOMMERCE' => 'MINCOMMERCE',
                    'MINEPAT' => 'MINEPAT',
                    'MINTOUL' => 'MINTOUL',
                    'MINESEC' => 'MINESEC',
                    'MINJEC' => 'MINJEC',
                    'MINEPDED' => 'MINEPDED',
                    'MINMIDT' => 'MINMIDT',
                    'MINADER' => 'MINADER',
                    'MINEPIA' => 'MINEPIA',
                    'MINEE' => 'MINEE',
                    'MINFOF' => 'MINFOF',
                    'MINEFOP' => 'MINEFOP',
                    'MINTP' => 'MINTP',
                    'MINDCAF' => 'MINDCAF',
                    'MINHDU' => 'MINHDU',
                    'MINPMEESA' => 'MINPMEESA',
                    'MINSANTE' => 'MINSANTE',
                    'MINTSS' => 'MINTSS',
                    'MINAS' => 'MINAS',
                    'MINPROFF' => 'MINPROFF',
                    'MINPOSTEL' => 'MINPOSTEL',
                    'MINT' => 'MINT',
                    'MINFOPRA' => 'MINFOPRA',
                    'PENSIONNES' => 'PENSIONNES',
                    'CONSUPE' => 'CONSUPE',
                    'COURSUP' => 'COURSUP',
                    'TAMPON' => 'TAMPON',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Vous devez obligatoirement renseigner votre ministère",
                    ]),
                ],
            ])
            ->add('roles', ChoiceType::class,[
                'required' => true,
                'label' => 'Rôle de l\'utilisateur',
                'choices' => $roles,
                'attr' => [
                    'default' => 'ROLE_USER'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Vous devez accepter nos conditions.',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
            ])
        ;

        // Data transformer pour gerer l'erreur "Warning: Array to string conversion"
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray)? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
