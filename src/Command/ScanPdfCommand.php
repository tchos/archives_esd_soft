<?php

namespace App\Command;

use App\Service\PdfMetadataExtractor;
use App\Service\PdfMetadataService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:scan-pdf',
    description: 'Scanne le dossier Bonita_Pdf et enregistre les métadonnées des fichiers trouvés en BD',
)]
class ScanPdfCommand extends Command
{
    protected static $defaultName = 'app:scan-pdf';
    private PdfMetadataExtractor $extractor;
    private PdfMetadataService $metadataService;
    public function __construct(PdfMetadataExtractor $extractor, PdfMetadataService $metadataService)
    {
        $this->extractor = $extractor;
        $this->metadataService = $metadataService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('🗂 Scan des fichiers PDF');

        $sourceDir = '/home/tchos/Documents/esdsoft/Bonita_Pdf';
        $filesToSave = $this->extractor->getMatchingPdfs($sourceDir);

        if (empty($filesToSave)) {
            $io->warning('Aucun fichier PDF trouvé.');
            return Command::SUCCESS;
        }

        foreach ($filesToSave as $versions) {
            $valide = $versions['Valide'] ?? null;
            $scanned = $versions['Scanned'] ?? null;
            $this->metadataService->enregistrerPdf($valide, $scanned);
        }

        $io->success('✅ Scan terminé et métadonnées enregistrées !');
        return Command::SUCCESS;
    }
}
