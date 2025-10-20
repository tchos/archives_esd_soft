<?php

namespace App\Service;

use App\Entity\BusinessData\resumeesdsoft;
use App\Entity\Main\Esd;
use App\Repository\BusinessData\resumeesdsoftRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Smalot\PdfParser\Parser;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\KernelInterface;

class PdfMetadataService
{
    private $archiveDirectory;
    private $repos;
    private $emBD; // configure dans services.yaml
    private $entityManager; // configure dans services.yaml
    private $managerRegistry;
    public function __construct(EntityManagerInterface $entityManager, PdfMetadataExtractor $pdfMetadataExtractor,
                                    EntityManagerInterface $emBD, KernelInterface $kernel, ManagerRegistry $managerRegistry)
    {
        $this->entityManager = $entityManager;
        $this->pdfParser = new Parser();
        $this->pdfMetadataExtractor = $pdfMetadataExtractor;
        $this->emBD = $emBD;
        $this->archiveDirectory = $kernel->getProjectDir().'/public/asset/archives/';
        $this->managerRegistry = $managerRegistry;
        //$this->repos = $this->emBD->getRepository(resumeesdsoft::class);
    }

    public function getPathDir()
    {
        return $this->archiveDirectory;
    }

    /*
     * Cette fonction recupere les infos d'un pdf pour un ESD et les enregistre en BD.
     */
    public function enregistrerPdf(?string $cheminValide, ?string $cheminScanne)
    {
        // Si aucun fichier n’est présent, on arrête proprement
        if (empty($cheminValide) && empty($cheminScanne)) {
            return;
        }

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
        $fileElectronique = null;
        $fileScanne = null;

        // Deplacement du fichier valide
        if($electronique){
            $fileElectronique = $electronique['nomFichier'];

            // 'esd_archives_directory' = Chemin par défaut configuré dans le fichier service.yaml
            $chemin = $this->getPathDir().'/'.$data['ministere'].'/'.$data['anneeEsd'];
            if (!is_dir($chemin)) {
                mkdir($chemin, 0777, true);
            }

            // Deplacement des fichiers vers le dossier de stockage
            try {
                if($fileElectronique)   rename($cheminValide, $chemin.'/'.$fileElectronique);
            } catch (FileException $e) {
                error_log("Erreur lors du déplacement de $cheminValide : " . $e->getMessage());
            }
        }

        // Deplacement du fichier scanné
        if($scanne){
            $fileScanne = $scanne['nomFichier'];

            // 'esd_archives_directory' = Chemin par défaut configuré dans le fichier service.yaml
            $chemin = $this->getPathDir().$data['ministere'].'/'.$data['anneeEsd'];
            if(!is_dir($chemin)){
                rename($cheminScanne, $chemin.'/'.$fileScanne);
            }

            // Deplacement des fichiers vers le dossier de stockage
            try {
                if($fileScanne)     rename($cheminScanne, $chemin.'/'.$fileScanne);
            } catch (FileException $e) {
                error_log("Erreur lors du déplacement de $cheminScanne : " . $e->getMessage());
            }
        }

        //dd($electronique, $data, $matricule, $numesd, $fileElectronique, $scanne, $fileScanne, $chemin.'/'.$fileElectronique, $chemin.'/'.$fileScanne);

        // Création et persistance en base
        $repo = $this->entityManager->getRepository(\App\Entity\Main\Esd::class);

        // On recupere l'ESD au cas où il s'agit d'une MAJ de chemin ie on a l'autre version du fichier qui est disponible
        $existing = $repo->findOneByNumesdAndMatricule($numesd, $matricule);

        // Demarrage de la transaction
        $this->entityManager->beginTransaction();
        if($existing){
            $updated = false;
            if($fileElectronique && $existing->getFichierValide() !== $fileElectronique){
                $updated = true;
                $existing->setFichierValide($fileElectronique);
            }

            if($fileScanne && $existing->getFichierScanne() !== $fileScanne){
                $updated = true;
                $existing->setFichierScanne($fileScanne);
            }

            // mettre à jour la date d'archivage auto
            $existing->setDateArchivageAuto(new \DateTime());
            if ($updated) {
                $this->entityManager->persist($existing);
            }
        }else {
            $esd = new Esd();
            $esd->setNumesd($numesd)
                ->setMatricule($matricule)
                ->setNomagent($data['nom'])
                ->setDateesd($data['date_creation'])
                ->setFichierValide($fileElectronique)
                ->setFichierScanne($fileScanne)
                ->setIsDeleted(false)
                ->setMinistere($data['ministere'])
            ;
        }

        try {

            $this->entityManager->persist($esd);
            $this->entityManager->flush();

            $this->entityManager->commit();
        }catch (UniqueConstraintViolationException $e){
            $this->entityManager->rollback();
            $this->managerRegistry->resetManager();
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

        $dirname = $fileInfo['dirname'] ?? '';
        $path = $dirname; // Récupère le chemin
        $file = $fileInfo['basename']; // Récupère le nom du fichier avec extension

        return [
            'chemin' => $path,
            'nomFichier' => $file,
        ];
    }
}