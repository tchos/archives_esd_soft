<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EsdArchiveController extends AbstractController
{
    #[Route('/esd/archive', name: 'app_esd_archive')]
    public function index(): Response
    {
        return $this->render('esd_archive/index.html.twig', [
            'controller_name' => 'EsdArchiveController',
        ]);
    }
}
