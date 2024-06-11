<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611210736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE budget (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE budget_user (budget_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F757D41936ABA6B8 (budget_id), INDEX IDX_F757D419A76ED395 (user_id), PRIMARY KEY(budget_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE envelope (id INT AUTO_INCREMENT NOT NULL, budget_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_8A95786836ABA6B8 (budget_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE budget_user ADD CONSTRAINT FK_F757D41936ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE budget_user ADD CONSTRAINT FK_F757D419A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE envelope ADD CONSTRAINT FK_8A95786836ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE budget_user DROP FOREIGN KEY FK_F757D41936ABA6B8');
        $this->addSql('ALTER TABLE budget_user DROP FOREIGN KEY FK_F757D419A76ED395');
        $this->addSql('ALTER TABLE envelope DROP FOREIGN KEY FK_8A95786836ABA6B8');
        $this->addSql('DROP TABLE budget');
        $this->addSql('DROP TABLE budget_user');
        $this->addSql('DROP TABLE envelope');
    }
}
