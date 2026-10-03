<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261002184941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE EsdManuel_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE EsdManuel (id INT NOT NULL, matricule VARCHAR(7) NOT NULL, numero VARCHAR(64) NOT NULL, montant INT NOT NULL, service VARCHAR(128) NOT NULL, date_signature DATE NOT NULL, signataire VARCHAR(64) NOT NULL, copie_scannee VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE EsdManuel_id_seq CASCADE');
        $this->addSql('DROP TABLE EsdManuel');
    }
}
