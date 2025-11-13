<?php

namespace App\Controller;

use App\Service\PdfMetadataExtractor;
use App\Service\PdfMetadataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SaveESDController extends AbstractController
{
    #[Route('/save/esd', name: 'app_save_esd')]
    public function index(PdfMetadataExtractor $extractor, PdfMetadataService $metadata): Response
    {
        $sourceDir = $this->getParameter('esd_source_directory');
        $filesToSave = $extractor->getMatchingPdfs($sourceDir);
        //dd($filesToSave);

        // Traitement des fichiers qui ont les deux versions
        foreach ($filesToSave as $id => $versions) {
            $valide  = $versions['Valide'] ?? null;
            $scanned = $versions['Scanned'] ?? null;
            $metadata->enregistrerPdf($valide, $scanned);
        }

        return new Response('', Response::HTTP_OK);
    }
}
