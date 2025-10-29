<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240531114733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE schedule ADD user_id INT DEFAULT NULL, ADD start_at DATETIME NOT NULL, ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A3811FBA76ED395 ON schedule (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649A40BC2D5');
        $this->addSql('DROP INDEX UNIQ_8D93D649A40BC2D5 ON user');
        $this->addSql('ALTER TABLE user DROP schedule_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD schedule_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649A40BC2D5 FOREIGN KEY (schedule_id) REFERENCES schedule (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A40BC2D5 ON user (schedule_id)');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FBA76ED395');
        $this->addSql('DROP INDEX IDX_5A3811FBA76ED395 ON schedule');
        $this->addSql('ALTER TABLE schedule DROP user_id, DROP start_at, DROP active');
    }
}
