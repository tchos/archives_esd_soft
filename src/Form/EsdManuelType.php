<?php

namespace App\Form;

use App\Entity\Main\EsdManuel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
            // Champ non mappé : le choix de l'utilisateur
            ->add('type_identification', ChoiceType::class, [
                'mapped'   => false,
                'choices'  => [
                    'Nom'       => 'nom',
                    'Matricule' => 'matricule',
                ],
                'expanded' => true,   // boutons radio
                'multiple' => false,
                'data'     => 'nom', // valeur par défaut
            ])
            ->add('matricule', TextType::class, [
                'label' => 'Matricule',
                'required' => false,
                'attr'  => [
                    'maxlength' => 8,
                    "placeholder" => "Ex: 010013A"
                ],
            ])
            ->add('nom', TextType::class, [
                'label'    => 'Nom de l\'intéressé',
                'required' => false,
                'attr'     => [
                    'maxlength' => 64,
                    "placeholder" => "Ex: TCHOS BENEDICTO PACIFICO ELOHIM MELCHISEDEK"
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

        // ---- Détection du mode selon la donnée initiale (édition) ----
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $esd = $event->getData();
            $form = $event->getForm();

            if ($esd instanceof EsdManuel && $esd->getId() !== null) {
                $mode = $esd->isIdentifieParMatricule() ? 'matricule' : 'nom';
                $form->get('type_identification')->setData($mode);
            }

            $this->applyMode($form, $form->get('type_identification')->getData() ?? 'matricule');
        });

        // ---- Adaptation selon le choix soumis ----
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $mode = $data['type_identification'] ?? 'matricule';
            $this->applyMode($form, $mode);

            // Nettoyage : on vide le champ non choisi
            if ($mode === 'matricule') {
                $data['nom'] = null;
            } else {
                $data['matricule'] = null;
            }
            $event->setData($data);
        });

        // ---- Validation croisée après soumission ----
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $mode = $form->get('type_identification')->getData();

            $value = $mode === 'matricule'
                ? $form->get('matricule')->getData()
                : $form->get('nom')->getData();

            if ($value === null || $value === '') {
                $form->get($mode)->addError(new FormError(
                    $mode === 'matricule'
                        ? 'Le matricule est obligatoire.'
                        : 'Le nom est obligatoire.'
                ));
            }
        });
    }

    /**
     * Rend obligatoire le champ actif et optionnel l'autre.
     *
     * On force TOUJOURS 'required' => false côté HTML pour ne jamais
     * bloquer la soumission navigateur quand un champ est caché.
     * La validation réelle se fait dans l'écouteur POST_SUBMIT.
     */
    private function applyMode(\Symfony\Component\Form\FormInterface $form, string $mode): void
    {
        $form->add('matricule', TextType::class, array_merge(
            $form->get('matricule')->getConfig()->getOptions(),
            ['required' => false]
        ));
        $form->add('nom', TextType::class, array_merge(
            $form->get('nom')->getConfig()->getOptions(),
            ['required' => false]
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EsdManuel::class,
        ]);
    }
}