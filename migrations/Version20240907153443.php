<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240907153443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment_method ADD COLUMN test_base_url CLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE payment_method ADD COLUMN prod_base_url CLOB DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__payment_method AS SELECT id, name, description, more_description, image_url, test_public_api_key, test_private_api_key, prod_public_api_key, prod_private_api_key, updated_at, created_at FROM payment_method');
        $this->addSql('DROP TABLE payment_method');
        $this->addSql('CREATE TABLE payment_method (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, more_description CLOB DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, test_public_api_key CLOB DEFAULT NULL, test_private_api_key CLOB DEFAULT NULL, prod_public_api_key CLOB DEFAULT NULL, prod_private_api_key CLOB DEFAULT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('INSERT INTO payment_method (id, name, description, more_description, image_url, test_public_api_key, test_private_api_key, prod_public_api_key, prod_private_api_key, updated_at, created_at) SELECT id, name, description, more_description, image_url, test_public_api_key, test_private_api_key, prod_public_api_key, prod_private_api_key, updated_at, created_at FROM __temp__payment_method');
        $this->addSql('DROP TABLE __temp__payment_method');
    }
}
