<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220313162123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document CHANGE total_ht total_ht NUMERIC(5, 2) NOT NULL, CHANGE total_tva total_tva NUMERIC(5, 2) NOT NULL, CHANGE total_ttc total_ttc NUMERIC(5, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document CHANGE total_ht total_ht INT NOT NULL, CHANGE total_tva total_tva INT NOT NULL, CHANGE total_ttc total_ttc INT NOT NULL');
    }
}
