<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240722085729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tchat_room (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tchat_room_user (tchat_room_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4232EBB916F52A05 (tchat_room_id), INDEX IDX_4232EBB9A76ED395 (user_id), PRIMARY KEY(tchat_room_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tchat_room_user ADD CONSTRAINT FK_4232EBB916F52A05 FOREIGN KEY (tchat_room_id) REFERENCES tchat_room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tchat_room_user ADD CONSTRAINT FK_4232EBB9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD tchat_message_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A4B4E1A3 FOREIGN KEY (tchat_message_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649A4B4E1A3 ON user (tchat_message_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A4B4E1A3');
        $this->addSql('ALTER TABLE tchat_room_user DROP FOREIGN KEY FK_4232EBB916F52A05');
        $this->addSql('ALTER TABLE tchat_room_user DROP FOREIGN KEY FK_4232EBB9A76ED395');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE tchat_room');
        $this->addSql('DROP TABLE tchat_room_user');
        $this->addSql('DROP INDEX IDX_8D93D649A4B4E1A3 ON user');
        $this->addSql('ALTER TABLE user DROP tchat_message_id');
    }
}
