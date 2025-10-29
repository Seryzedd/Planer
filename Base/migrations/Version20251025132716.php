<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251025132716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calendar_event (id INT AUTO_INCREMENT NOT NULL, start_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, adress VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE calendar_event_user (calendar_event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4125EE77495C8E3 (calendar_event_id), INDEX IDX_4125EE7A76ED395 (user_id), PRIMARY KEY(calendar_event_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calendar_event_user ADD CONSTRAINT FK_4125EE77495C8E3 FOREIGN KEY (calendar_event_id) REFERENCES calendar_event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE calendar_event_user ADD CONSTRAINT FK_4125EE7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message CHANGE readed_users_id readed_users_id LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar_event_user DROP FOREIGN KEY FK_4125EE77495C8E3');
        $this->addSql('ALTER TABLE calendar_event_user DROP FOREIGN KEY FK_4125EE7A76ED395');
        $this->addSql('DROP TABLE calendar_event');
        $this->addSql('DROP TABLE calendar_event_user');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE message CHANGE readed_users_id readed_users_id LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
