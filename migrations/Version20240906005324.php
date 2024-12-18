<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240906005324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, client_name, delivery_address, quantity, order_cost, taxe, order_cost_ttc, is_paid, status, carrier_price, carrier_name, carrier_id, updated_at, created_at FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_name VARCHAR(255) NOT NULL, billing_address CLOB DEFAULT NULL, quantity INTEGER NOT NULL, order_cost INTEGER NOT NULL, taxe INTEGER DEFAULT NULL, order_cost_ttc INTEGER NOT NULL, is_paid BOOLEAN DEFAULT NULL, status VARCHAR(255) NOT NULL, carrier_price INTEGER NOT NULL, carrier_name VARCHAR(255) NOT NULL, carrier_id INTEGER NOT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , shipping_address CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO "order" (id, client_name, billing_address, quantity, order_cost, taxe, order_cost_ttc, is_paid, status, carrier_price, carrier_name, carrier_id, updated_at, created_at) SELECT id, client_name, delivery_address, quantity, order_cost, taxe, order_cost_ttc, is_paid, status, carrier_price, carrier_name, carrier_id, updated_at, created_at FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, client_name, quantity, order_cost, taxe, order_cost_ttc, is_paid, status, carrier_price, carrier_name, carrier_id, updated_at, created_at FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_name VARCHAR(255) NOT NULL, quantity INTEGER NOT NULL, order_cost INTEGER NOT NULL, taxe INTEGER DEFAULT NULL, order_cost_ttc INTEGER NOT NULL, is_paid BOOLEAN DEFAULT NULL, status VARCHAR(255) NOT NULL, carrier_price INTEGER NOT NULL, carrier_name VARCHAR(255) NOT NULL, carrier_id INTEGER NOT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivery_address CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO "order" (id, client_name, quantity, order_cost, taxe, order_cost_ttc, is_paid, status, carrier_price, carrier_name, carrier_id, updated_at, created_at) SELECT id, client_name, quantity, order_cost, taxe, order_cost_ttc, is_paid, status, carrier_price, carrier_name, carrier_id, updated_at, created_at FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
    }
}