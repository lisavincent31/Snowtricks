<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240209054858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE tricks ADD featured_image_id INT NOT NULL, DROP featured_image');
        $this->addSql('ALTER TABLE tricks ADD CONSTRAINT FK_E1D902C13569D950 FOREIGN KEY (featured_image_id) REFERENCES medias (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1D902C13569D950 ON tricks (featured_image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tricks DROP FOREIGN KEY FK_E1D902C13569D950');
        $this->addSql('DROP INDEX UNIQ_E1D902C13569D950 ON tricks');
        $this->addSql('ALTER TABLE tricks ADD featured_image VARCHAR(255) NOT NULL, DROP featured_image_id');
    }
}
