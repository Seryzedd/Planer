<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522122858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE password_resetting (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD password_resetting_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D94D3B7B FOREIGN KEY (password_resetting_id) REFERENCES password_resetting (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D94D3B7B ON user (password_resetting_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D94D3B7B');
        $this->addSql('DROP TABLE password_resetting');
        $this->addSql('DROP INDEX UNIQ_8D93D649D94D3B7B ON user');
        $this->addSql('ALTER TABLE user DROP password_resetting_id');
    }
}
