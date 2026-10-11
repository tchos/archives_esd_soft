<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261010151736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE esdmanuel ADD nom VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE esdmanuel ALTER matricule DROP NOT NULL');
        $this->addSql('ALTER TABLE esdmanuel ALTER matricule TYPE VARCHAR(8)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE EsdManuel DROP nom');
        $this->addSql('ALTER TABLE EsdManuel ALTER matricule SET NOT NULL');
        $this->addSql('ALTER TABLE EsdManuel ALTER matricule TYPE VARCHAR(7)');
    }
}
