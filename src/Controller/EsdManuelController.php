<?php

namespace App\Controller;

use App\Entity\Main\EsdManuel;
use App\Form\EsdManuelType;
use App\Form\Main\EsdManuelSearchType;
use App\Repository\Main\EsdManuelRepository;
use App\Service\EsdManuelFileUploader;
use App\Service\RechercherESD;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

#[Route('/esd/manuel')]
#[IsGranted("ROLE_USER")]
class EsdManuelController extends AbstractController
{
    #[Route('/new', name: 'app_esd_manuel_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        EsdManuelFileUploader $uploader,
    ): Response {
        $esdManuel = new EsdManuel();
        $form = $this->createForm(EsdManuelType::class, $esdManuel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('copie_scannee_file')->getData();

            // 1. Upload du PDF + récupération du nom final
            $fileName = $uploader->upload($file, $esdManuel);
            $esdManuel->setCopieScannee($fileName);

            // 2. Persistance
            $entityManager->persist($esdManuel);
            $entityManager->flush();

            $this->addFlash('success', 'L\'ESD Manuel a bien été enregistré.');

            return $this->redirectToRoute('app_esd_manuel_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('esd_manuel/new.html.twig', [
            'esd_manuel' => $esdManuel,
            'form'       => $form,
        ]);
    }

    #[Route('/search', name: 'app_esd_manuel_search', methods: ['GET'])]
    public function search(
        RechercherESD $rechercherESD,
        Request $request,
        EsdManuelRepository $repository,
    ): Response {

        $esds = null; // Variable qui va contenir le resultat de la recherche
        $form = $this->createForm(EsdManuelSearchType::class);
        $form->handleRequest($request);

        $results   = [];
        $submitted = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $submitted = true;
            // Récupérer le matricule et/ou le numero d'ESD saisis dans le formulaire
            $donnees = $form->getData();
            $cherche = $donnees['recherche'];

            // Recherche dans le BD des ESD lies aux infos saisis
            $esds = $rechercherESD->findESDManuel($cherche);
            //dd($esds);

            // Si l'on ne trouve rien on affiche un message d'ESD non trouve
            if(!$esds) {
                $this->addFlash('danger',
                    '<strong>Erreur !!!</strong> Il n\'existe aucun ESD pour le matricule et l\'ESD <strong>'.$cherche.'</strong> 
                        dans les archives.'
                );
            }
        }

        return $this->render('esd_manuel/index.html.twig', [
            'form'      => $form,
            'esds'   => $esds,
            'submitted' => $submitted,
        ]);
    }

    // Cette fonction permet de télécharger un fichier pdf s'il existe
    #[Route('/{id}/download', name: 'app_esd_manuel_download', methods: ['GET'])]
    public function download(
        EsdManuel $esdManuel,
        EsdManuelFileUploader $uploader,
    ): Response {
        $path = $uploader->getAbsolutePath($esdManuel);

        if (!is_file($path)) {
            throw $this->createNotFoundException('Le fichier PDF est introuvable.');
        }

        $response = new BinaryFileResponse($path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE, // ou ATTACHMENT pour forcer le téléchargement
            $esdManuel->getCopieScannee(),
        );

        return $response;
    }
}