<?php

namespace App\Controller;

use App\Repository\BusinessData\resumeesdsoftRepository;
use App\Service\PdfMetadataExtractor;
use App\Service\PdfMetadataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PdfMetadataExtractor $extractor, PdfMetadataService $metadata, resumeesdsoftRepository $repository): Response
    {
        $filesToSave = $extractor->getMatchingPdfs("/home/tchos/Documents/esdsoft/Bonita_Pdf");
        //dd($filesToSave);

        // Traitement des fichiers qui ont les deux versions
        foreach ($filesToSave as $id => $versions) {
            if (isset($versions['Valide']) && isset($versions['Scanned'])) {
                $metadata->enregistrerPdf($versions['Valide'], $versions['Scanned']);
            }
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
