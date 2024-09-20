<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920182622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movement ADD detection_mask_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F76B7FC884 FOREIGN KEY (detection_mask_id) REFERENCES detection_mask (id)');
        $this->addSql('CREATE INDEX IDX_F4DD95F76B7FC884 ON movement (detection_mask_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F76B7FC884');
        $this->addSql('DROP INDEX IDX_F4DD95F76B7FC884 ON movement');
        $this->addSql('ALTER TABLE movement DROP detection_mask_id');
    }
}
