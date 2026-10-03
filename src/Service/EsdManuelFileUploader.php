<?php

namespace App\Service;

use App\Entity\Main\EsdManuel;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class EsdManuelFileUploader
{
    public function __construct(
        private string $esdManuelsDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    /**
     * Enregistre le PDF dans public/asset/archives/{service}/{année}/matricule_numero_date.pdf
     * et retourne le nom du fichier généré (à stocker en BDD dans $esdManuel->setCopieScannee()).
     */
    public function upload(UploadedFile $file, EsdManuel $esdManuel): string
    {
        $matricule    = (string) $esdManuel->getMatricule();
        $numero       = (string) $esdManuel->getNumero();
        $date         = $esdManuel->getDateSignature();
        $dateString   = $date?->format('Ymd') ?? date('Ymd');
        $annee        = $date?->format('Y') ?? date('Y');
        $service      = (string) $esdManuel->getService();

        // Nom du fichier : matricule_numero_dateSignature.pdf
        $baseName = sprintf('%s_%s_%s', $matricule, $numero, $dateString);
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
        $fileName = $safeName . '.pdf';


        // Dossier cible : .../archives/{service}/{année}/
        $serviceDir = preg_replace('/[^A-Za-z0-9_\-]/', '_', $service);
        $targetDir  = sprintf('%s/%s/%s', rtrim($this->esdManuelsDirectory, '/'), $serviceDir, $annee);

        try {
            $file->move($targetDir, $fileName);
        } catch (FileException $e) {
            throw new \RuntimeException('Impossible d\'enregistrer le fichier PDF : ' . $e->getMessage(), 0, $e);
        }

        return $fileName;
    }

    /**
     * Retourne le chemin absolu du PDF à partir d'une entité (utile pour téléchargement/suppression).
     */
    public function getAbsolutePath(EsdManuel $esdManuel): string
    {
        //On recupère l'année de signature à partir de la date de signature
        $annee   = $esdManuel->getDateSignature()?->format('Y') ?? date('Y');
        $service = $this->slugger->slug((string) $esdManuel->getService())->toString();

        return sprintf(
            '%s/%s/%s/%s',
            rtrim($this->esdManuelsDirectory, '/'),
            $service,
            $annee,
            $esdManuel->getCopieScannee()
        );
    }
}