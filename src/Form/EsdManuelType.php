<?php

namespace App\Form;

use App\Entity\Main\EsdManuel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class EsdManuelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $types_esd = [
            'DESICION' => 'DESICION',
            'ESD CODIFIE' => 'ESD CODIFIE',
        ];

        $services_emetteurs = [
            'S3' => 'S3',
            'S5' => 'S5',
            'S6' => 'S6',
        ];

        $builder
            ->add('matricule', TextType::class, [
                'label' => 'Matricule',
                'attr'  => [
                    'maxlength' => 7,
                    "placeholder" => "Ex: 010013A"
                ],
            ])
            ->add('numero', TextType::class, [
                'label' => 'Numéro',
                'attr'  => [
                    'maxlength' => 64,
                    "placeholder" => "Ex: 013010"
                ],
            ])
            ->add('montant', IntegerType::class, [
                'label' => 'Montant',
                'attr'  => [
                    "placeholder" => "Ex: 150000"
                ],
            ])
            ->add('service', ChoiceType::class, [
                'required' => true,
                'label' => 'Service Emetteur',
                'choices' => $services_emetteurs,
                'attr' => [
                    'default' => 'S5'
                ]
            ])
            ->add('date_signature', DateType::class, [
                'label'  => 'Date de signature',
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
            ])
            ->add('signataire', TextType::class, [
                'label' => 'Signataire',
                'attr'  => [
                    'maxlength' => 64,
                    "placeholder" => "Ex: SIMO KENGNE ROBERT"
                ],
            ])
            // Champ NON mappé : on gère nous-mêmes le nom final en BDD
            ->add('copie_scannee_file', FileType::class, [
                'label'    => 'Copie scannée (PDF)',
                'mapped'   => false,
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un fichier PDF.',
                    ]),
                    new File([
                        'maxSize'          => '20M',
                        'mimeTypes'        => ['application/pdf'],
                        'mimeTypesMessage' => 'Le fichier doit être au format PDF.',
                    ]),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'label' => "Type de l'ESD",
                'choices' => $types_esd,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EsdManuel::class,
        ]);
    }
}