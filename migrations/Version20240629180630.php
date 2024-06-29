<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240629180630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE budget (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE budget_user (budget_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F757D41936ABA6B8 (budget_id), INDEX IDX_F757D419A76ED395 (user_id), PRIMARY KEY(budget_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, budget_id INT NOT NULL, envelope_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_64C19C136ABA6B8 (budget_id), INDEX IDX_64C19C14706CB17 (envelope_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE envelope (id INT AUTO_INCREMENT NOT NULL, budget_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_8A95786836ABA6B8 (budget_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE import (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movement (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, budget_id INT NOT NULL, import_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, comment VARCHAR(255) NOT NULL, date DATE NOT NULL, INDEX IDX_F4DD95F712469DE2 (category_id), INDEX IDX_F4DD95F736ABA6B8 (budget_id), INDEX IDX_F4DD95F7B6A263D9 (import_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE budget_user ADD CONSTRAINT FK_F757D41936ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE budget_user ADD CONSTRAINT FK_F757D419A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C136ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C14706CB17 FOREIGN KEY (envelope_id) REFERENCES envelope (id)');
        $this->addSql('ALTER TABLE envelope ADD CONSTRAINT FK_8A95786836ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F736ABA6B8 FOREIGN KEY (budget_id) REFERENCES budget (id)');
        $this->addSql('ALTER TABLE movement ADD CONSTRAINT FK_F4DD95F7B6A263D9 FOREIGN KEY (import_id) REFERENCES import (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE budget_user DROP FOREIGN KEY FK_F757D41936ABA6B8');
        $this->addSql('ALTER TABLE budget_user DROP FOREIGN KEY FK_F757D419A76ED395');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C136ABA6B8');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C14706CB17');
        $this->addSql('ALTER TABLE envelope DROP FOREIGN KEY FK_8A95786836ABA6B8');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F712469DE2');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F736ABA6B8');
        $this->addSql('ALTER TABLE movement DROP FOREIGN KEY FK_F4DD95F7B6A263D9');
        $this->addSql('DROP TABLE budget');
        $this->addSql('DROP TABLE budget_user');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE envelope');
        $this->addSql('DROP TABLE import');
        $this->addSql('DROP TABLE movement');
        $this->addSql('DROP TABLE `user`');
    }
}
