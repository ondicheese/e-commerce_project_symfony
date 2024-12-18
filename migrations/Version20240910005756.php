<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240910005756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "order" ADD COLUMN user_id INTEGER NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__order AS SELECT id, client_name, billing_address, shipping_address, quantity, order_cost, taxe, order_cost_ttc, is_paid, status, carrier_price, carrier_name, carrier_id, updated_at, created_at, stripe_client_secret, paypal_client_secret, payment_method FROM "order"');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_name VARCHAR(255) NOT NULL, billing_address CLOB DEFAULT NULL, shipping_address CLOB DEFAULT NULL, quantity INTEGER NOT NULL, order_cost INTEGER NOT NULL, taxe INTEGER DEFAULT NULL, order_cost_ttc INTEGER NOT NULL, is_paid BOOLEAN DEFAULT NULL, status VARCHAR(255) NOT NULL, carrier_price INTEGER NOT NULL, carrier_name VARCHAR(255) NOT NULL, carrier_id INTEGER NOT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , stripe_client_secret VARCHAR(255) DEFAULT NULL, paypal_client_secret VARCHAR(255) DEFAULT NULL, payment_method VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO "order" (id, client_name, billing_address, shipping_address, quantity, order_cost, taxe, order_cost_ttc, is_paid, status, carrier_price, carrier_name, carrier_id, updated_at, created_at, stripe_client_secret, paypal_client_secret, payment_method) SELECT id, client_name, billing_address, shipping_address, quantity, order_cost, taxe, order_cost_ttc, is_paid, status, carrier_price, carrier_name, carrier_id, updated_at, created_at, stripe_client_secret, paypal_client_secret, payment_method FROM __temp__order');
        $this->addSql('DROP TABLE __temp__order');
    }
}
