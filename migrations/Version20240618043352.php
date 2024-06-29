<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240618043352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movement ADD budget_id INT NOT NULL');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F736ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id)');
        $this->addSql('CREATE INDEX IDX_F4DD95F736ABA6B8 ON movement (budget_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F736ABA6B8');
        $this->addSql('DROP INDEX IDX_F4DD95F736ABA6B8 ON movement');
        $this->addSql('ALTER TABLE movement DROP budget_id');
    }
}
