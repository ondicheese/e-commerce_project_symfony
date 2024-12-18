<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240812114944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collection ADD COLUMN is_mega BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__collection AS SELECT id, title, description, button_text, button_link, image_url, updated_at, created_at FROM collection');
        $this->addSql('DROP TABLE collection');
        $this->addSql('CREATE TABLE collection (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, button_text VARCHAR(255) NOT NULL, button_link VARCHAR(255) NOT NULL, image_url VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO collection (id, title, description, button_text, button_link, image_url, updated_at, created_at) SELECT id, title, description, button_text, button_link, image_url, updated_at, created_at FROM __temp__collection');
        $this->addSql('DROP TABLE __temp__collection');
    }
}
