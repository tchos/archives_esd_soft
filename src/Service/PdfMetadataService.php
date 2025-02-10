<?php

namespace App\Service;

use App\Entity\BusinessData\resumeesdsoft;
use App\Entity\Main\Esd;
use App\Repository\BusinessData\resumeesdsoftRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Smalot\PdfParser\Parser;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\KernelInterface;

class PdfMetadataService
{
    private $archiveDirectory;
    private $repos;
    private $emBD; // configure dans services.yaml
    private $entityManager; // configure dans services.yaml
    public function __construct(EntityManagerInterface $entityManager, PdfMetadataExtractor $pdfMetadataExtractor,
                                    EntityManagerInterface $emBD, KernelInterface $kernel)
    {
        $this->entityManager = $entityManager;
        $this->pdfParser = new Parser();
        $this->pdfMetadataExtractor = $pdfMetadataExtractor;
        $this->emBD = $emBD;
        $this->archiveDirectory = $kernel->getProjectDir().'/public/asset/archives/';
        //$this->repos = $this->emBD->getRepository(resumeesdsoft::class);
    }

    public function getPathDir()
    {
        return $this->archiveDirectory;
    }

    /*
     * Cette fonction recupere les infos d'un pdf pour un ESD et les enregistre en BD.
     */
    public function enregistrerPdf(string $cheminValide, string $cheminScanne)
    {
        // Extraction des informations à partir du nom du fichier
        preg_match('/([A-Z0-9]+)-(\d+)/', basename($cheminValide), $matches);
        if (!$matches) {
            return;
        }
        $matricule = $matches[1];
        $numesd = $matches[2];

        // On dissocie le chemin du fichier de son nom pour les 2 types de fichier
        $electronique = $this->pathAndFilename($cheminValide);
        $scanne = $this->pathAndFilename($cheminScanne);

        // On va chercher le code et le montant de paiement de l'ESD.
        //$paiement = $this->repos->findByMatriculeAndNumesd($matricule, $numesd);

        // Extraction des métadonnées du fichier "Valide"
        $data = $this->pdfMetadataExtractor->extraireMetadonnees($cheminValide);

        if($electronique && $scanne){
            $fileElectronique = $electronique['nomFichier'];
            $fileScanne = $scanne['nomFichier'];

            // 'esd_archives_directory' = Chemin par défaut configuré dans le fichier service.yaml
            $chemin = $this->getPathDir().'/'.$data['ministere'].'/'.$data['anneeEsd'];

            // Move the file to the directory where file are stored
            try {
                if(is_dir($chemin)){
                    rename($cheminValide, $chemin.'/'.$fileElectronique);
                    rename($cheminScanne, $chemin.'/'.$fileScanne);
                }else {
                    mkdir($chemin,0777, true);
                    rename($cheminValide, $chemin.'/'.$fileElectronique);
                    rename($cheminScanne, $chemin.'/'.$fileScanne);
                }

            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
        }

        //dd($electronique, $data, $matricule, $numesd, $fileElectronique, $scanne, $fileScanne, $chemin.'/'.$fileElectronique, $chemin.'/'.$fileScanne);

        // Création et persistance en base
        $esd = new Esd();
        $esd->setNumesd($numesd)
            ->setMatricule($matricule)
            ->setNomagent($data['nom'])
            ->setDateesd($data['date_creation'])
            ->setFichierElectronique($fileElectronique)
            ->setFichierScanne($fileScanne)
            ->setIsDeleted(false)
        ;

        try {
            $this->entityManager->persist($esd);
            $this->entityManager->flush();
        }catch (UniqueConstraintViolationException $e){
            if(php_sapi_name() == 'cli'){
                echo "Erreur: L'ESD ".$esd->getNumesd()." a deja ete archive pour le matricule ".$esd->getMatricule()." !!!".PHP_EOL;
            }else {
                error_log("Erreur: L'ESD ".$esd->getNumesd()." a deja ete archive pour le matricule ".$esd->getMatricule()." !!!");
            }
        }

    }

    /*
     * Cette fonction dissocie le chemin du fichier de son nom pour les 2 types de fichier
     */
    public function pathAndFilename($chemin): array
    {
        $fileInfo = pathinfo($chemin);
        $path = $fileInfo['dirname']; // Récupère le chemin
        $file = $fileInfo['basename']; // Récupère le nom du fichier avec extension

        return [
            'chemin' => $path,
            'nomFichier' => $file,
        ];
    }
}