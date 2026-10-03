<?php

namespace App\Form\Main;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EsdManuelSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recherche', TextType::class, [
                'label' => 'Matricule ou Numéro de l\'ESD manuel',
                'attr' => [
                    'placeholder' => 'Rechercher un ESD manuel via le matricule (EX: A123456) ou via son numéro (EX: 00000468)'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method'          => 'GET',
            'csrf_protection' => false, // inutile pour un GET idempotent
        ]);
    }

    public function getBlockPrefix(): string
    {
        // Pour éviter que les champs s'appellent esd_manuel_search[matricule] dans l'URL
        return '';
    }
}