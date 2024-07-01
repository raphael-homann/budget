<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701071633 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import ADD budget_id INT NOT NULL');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT FK_9D4ECE1D36ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id)');
        $this->addSql('CREATE INDEX IDX_9D4ECE1D36ABA6B8 ON import (budget_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import DROP FOREIGN KEY FK_9D4ECE1D36ABA6B8');
        $this->addSql('DROP INDEX IDX_9D4ECE1D36ABA6B8 ON import');
        $this->addSql('ALTER TABLE import DROP budget_id');
    }
}
