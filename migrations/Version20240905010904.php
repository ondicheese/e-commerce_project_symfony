<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240905010904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "order" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, client_name VARCHAR(255) NOT NULL, delivery_address CLOB DEFAULT NULL, quantity INTEGER NOT NULL, order_cost INTEGER NOT NULL, taxe INTEGER DEFAULT NULL, order_cost_ttc INTEGER NOT NULL, is_paid BOOLEAN DEFAULT NULL, status VARCHAR(255) NOT NULL, carrier_price INTEGER NOT NULL, carrier_name VARCHAR(255) NOT NULL, carrier_id INTEGER NOT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE TABLE order_details (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, my_order_id INTEGER NOT NULL, product_name VARCHAR(255) NOT NULL, product_description VARCHAR(255) NOT NULL, product_sale_price INTEGER NOT NULL, product_regular_price INTEGER NOT NULL, quantity INTEGER NOT NULL, taxe INTEGER DEFAULT NULL, subtotal INTEGER NOT NULL, updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_845CA2C1BFCDF877 FOREIGN KEY (my_order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_845CA2C1BFCDF877 ON order_details (my_order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE order_details');
    }
}
