<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220225194956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_details ADD produit_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation_details ADD CONSTRAINT FK_15B3B00FF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_15B3B00FF347EFB ON reservation_details (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_details DROP FOREIGN KEY FK_15B3B00FF347EFB');
        $this->addSql('DROP INDEX IDX_15B3B00FF347EFB ON reservation_details');
        $this->addSql('ALTER TABLE reservation_details DROP produit_id');
    }
}
