<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220215174901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE infos_legales (id INT AUTO_INCREMENT NOT NULL, url_site VARCHAR(255) NOT NULL, nom_societe VARCHAR(255) NOT NULL, siret_societe VARCHAR(14) NOT NULL, adresse_societe VARCHAR(255) NOT NULL, nom_webmaster VARCHAR(255) NOT NULL, email_site VARCHAR(255) NOT NULL, societe_webmaster VARCHAR(255) NOT NULL, hebergeur VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE infos_legales');
    }
}
