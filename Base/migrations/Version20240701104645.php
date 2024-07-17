<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701104645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_translation DROP FOREIGN KEY FK_5F59AA22166D1F9C');
        $this->addSql('DROP INDEX IDX_5F59AA22166D1F9C ON team_translation');
        $this->addSql('ALTER TABLE team_translation CHANGE project_id team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE team_translation ADD CONSTRAINT FK_5F59AA22296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_5F59AA22296CD8AE ON team_translation (team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_translation DROP FOREIGN KEY FK_5F59AA22296CD8AE');
        $this->addSql('DROP INDEX IDX_5F59AA22296CD8AE ON team_translation');
        $this->addSql('ALTER TABLE team_translation CHANGE team_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE team_translation ADD CONSTRAINT FK_5F59AA22166D1F9C FOREIGN KEY (project_id) REFERENCES team (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5F59AA22166D1F9C ON team_translation (project_id)');
    }
}
