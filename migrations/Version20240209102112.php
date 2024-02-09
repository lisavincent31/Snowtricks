<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240209102112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tricks CHANGE featured_image_id featured_image_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_8d93d649f85e0677 TO UNIQ_1483A5E9F85E0677');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_1483A5E9E7927C74 ON `users`');
        $this->addSql('ALTER TABLE `users` RENAME INDEX uniq_1483a5e9f85e0677 TO UNIQ_8D93D649F85E0677');
        $this->addSql('ALTER TABLE tricks CHANGE featured_image_id featured_image_id INT NOT NULL');
    }
}
