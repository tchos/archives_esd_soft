<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250216194803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE Utilisateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE Utilisateur (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, fullname VARCHAR(255) NOT NULL, telephone VARCHAR(32) NOT NULL, ministere VARCHAR(64) NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, IsPasswordModified BOOLEAN DEFAULT NULL, dateDerniereConnexion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, enableYN BOOLEAN NOT NULL, createdBy_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9B80EC643174800F ON Utilisateur (createdBy_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON Utilisateur (username)');
        $this->addSql('COMMENT ON COLUMN Utilisateur.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE Utilisateur ADD CONSTRAINT FK_9B80EC643174800F FOREIGN KEY (createdBy_id) REFERENCES Utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE Utilisateur_id_seq CASCADE');
        $this->addSql('ALTER TABLE Utilisateur DROP CONSTRAINT FK_9B80EC643174800F');
        $this->addSql('DROP TABLE Utilisateur');
    }
}
