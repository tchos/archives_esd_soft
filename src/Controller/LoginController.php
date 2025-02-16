<?php

namespace App\Controller;

use App\Service\PdfMetadataExtractor;
use App\Service\PdfMetadataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, PdfMetadataExtractor $extractor, PdfMetadataService $metadata): Response
    {
        $filesToSave = $extractor->getMatchingPdfs("/home/tchos/Documents/esdsoft/Bonita_Pdf");

        // Traitement des fichiers qui ont les deux versions
        foreach ($filesToSave as $id => $versions) {
            if (isset($versions['Valide']) && isset($versions['Scanned'])) {
                $metadata->enregistrerPdf($versions['Valide'], $versions['Scanned']);
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
