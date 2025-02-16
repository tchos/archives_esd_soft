<?php

namespace App\Controller;

use App\Form\SearchESDType;
use App\Repository\BusinessData\resumeesdsoftRepository;
use App\Service\PdfMetadataExtractor;
use App\Service\PdfMetadataService;
use App\Service\RechercherESD;
use App\Service\Statistiques;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RechercherESD $rechercherESD, Statistiques $statistiques, Request $request): Response
    {
        $esds = null; // Variable qui va contenir le resultat de la recherche
        $form = $this->createForm(SearchESDType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // Récupérer le matricule et/ou le numero d'ESD saisis dans le formulaire
            $donnees = $form->getData();
            $cherche = $donnees['recherche'];

            // Recherche dans le BD des ESD lies aux infos saisis
            $esds = $rechercherESD->findESD($cherche);
            //dd($esds);

            // Si l'on ne trouve rien on affiche un message d'ESD non trouve
            if(!$esds) {
                $this->addFlash('danger',
                    '<strong>Erreur !!!</strong> Il n\'existe aucun ESD pour le matricule et l\'ESD <strong>'.$cherche.'</strong> 
                        dans les archives.'
                );
            }
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'esds' => $esds,
        ]);
    }
}
