<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250203140159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE aigles_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE esd_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE aigles (id INT NOT NULL, rubrique VARCHAR(8) NOT NULL, code_ant VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE esd (id INT NOT NULL, numesd VARCHAR(32) NOT NULL, matricule VARCHAR(8) NOT NULL, nomagent VARCHAR(255) NOT NULL, codepaiement VARCHAR(4) NOT NULL, montant INT NOT NULL, dateesd DATE DEFAULT NULL, fichier_electronique VARCHAR(255) DEFAULT NULL, fichier_scanne VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE aigles_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE esd_id_seq CASCADE');
        $this->addSql('DROP TABLE aigles');
        $this->addSql('DROP TABLE esd');
    }
}
