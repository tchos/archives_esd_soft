<?php

namespace App\Service;

use RecursiveIteratorIterator;
use Smalot\PdfParser\Parser;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class PdfMetadataExtractor
{
    /*
     * Cette fonction permet d'extraire les meta-donnees d'un pdf pour un ESD valide
     * et les renvoie dans un tableau
     */
    public function extraireMetadonnees(string $cheminFichier): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($cheminFichier);
        $details = $pdf->getDetails();

        return [
            'matricule' => $details['MATRICULE'] ?? 'Inconnu',
            'nom' => $details['NOM'] ?? 'Inconnu',
            'dateCreation' => $details['DATECREATION'] ?? 'Inconnu',
            'anneeEsd' => $details['ANNEE'] ?? 'Inconnu',
            'ministere' => $details['MINISTERE'] ?? 'Inconnu',
            'date_creation' => isset($details['CreationDate']) ? new \DateTime($details['CreationDate']) : new \DateTime(),
            'version' => $details['Version'] ?? 'Inconnu',
        ];
        /**
         *"ANNEE" => "2025"
         * "Author" => "Théophile Nanne Mbende"
         * "CreationDate" => "2025-01-17T11:29:13+01:00"
         * "DATECREATION" => "2025-01-17"
         * "DATESIGNATURE" => ""
         * "DATEVALIDATION" => ""
         * "MATRICULE" => "530791C"
         * "MINISTERE" => "PENSIONNES"
         * "MINISTERES" => "PENSIONNES"
         * "ModDate" => "2025-01-30T15:57:59+01:00"
         * "NOM" => "DAWAI MESSINGUE"
         * "Producer" => "iText® 7.1.8 ©2000-2019 iText Group NV (AGPL-version); modified using iText® 7.1.8 ©2000-2019 iText Group NV (AGPL-version)"
         * "RAYONCLASSEMENT" => ""
         * "STATUT" => "VALIDE"
         * "Statut" => "CALCULE"
         * "Subject" => "ESD 2025/00000468 Mle:530791C"
         * "Title" => "ESD 2025/00000468 Mle:530791C"
         * "Version" => "ELECTRONIQUE"
         * "Pages" => 5
         * "dc:creator" => "Théophile Nanne Mbende"
         * "dc:description" => "ESD 2025/00000468 Mle:530791C"
         * "dc:title" => "ESD 2025/00000468 Mle:530791C"
         */
    }

    /*
     * Cette fonction parcours le répertoire des pdf generes par Bonita
     * et renvoie ceux dont les 2 versions (scanne et valide) existent.
     */
    function getMatchingPdfs(string $directory): array {
        if (!is_dir($directory)) {
            return ["Le répertoire spécifié n'existe pas."];
        }

        $files = glob($directory . '/*.pdf'); // Récupère tous les PDF
        $filesGrouped = [];

        foreach ($files as $filePath) {
            $fileName = basename($filePath); // Récupérer uniquement le nom du fichier

            // Vérifier si le fichier correspond au format attendu
            if (preg_match('/^([\dA-Z]+-\d+)(\(Scanned\)|-Valide)\.pdf$/', $fileName, $matches)) {
                $baseName = $matches[1]; // Partie commune "nom-numero"
                $type = $matches[2];     // "(Scanned)" ou "-Valide"

                // Stocker les fichiers dans un tableau associatif
                if (!isset($filesGrouped[$baseName])) {
                    $filesGrouped[$baseName] = ['Scanned' => null, 'Valide' => null];
                }

                if ($type === "(Scanned)") {
                    $filesGrouped[$baseName]['Scanned'] = $filePath;
                } elseif ($type === "-Valide") {
                    $filesGrouped[$baseName]['Valide'] = $filePath;
                }
            }
        }

        // Filtrer les fichiers ayant au moins l'une des deux versions (Si l'on veut les 2 version on remplacera || par &&)
        $matchingFiles = [];
        foreach ($filesGrouped as $group) {
            if ($group['Scanned']) {
                $matchingFiles[] = $group['Scanned'];
            }

            if ($group['Valide']) {
                $matchingFiles[] = $group['Valide'];
            }
        }

        // Tableau associatif pour stocker les fichiers par leur identifiant unique
        $documents = [];
        foreach ($matchingFiles as $file) {
            // Extraire l'identifiant unique du fichier (ex: "145456W-00000493")
            if (preg_match('/^(.+?-\d+)(?:\((Scanned)\)|-Valide)\.pdf$/', basename($file), $matches)) {
                $id = $matches[1]; // Ex: "145456W-00000493"
                $version = isset($matches[2]) ? "Scanned" : "Valide"; // "Scanned" ou "Valide"

                // Stocker le fichier selon sa version
                $documents[$id][$version] = $file;
            }
        }

        return $documents;
    }
}

