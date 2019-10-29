<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190721111259 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, order_date DATETIME NOT NULL, status SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_details (id INT AUTO_INCREMENT NOT NULL, order_reference_id INT NOT NULL, product_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, INDEX IDX_845CA2C112854AC3 (order_reference_id), INDEX IDX_845CA2C14584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, stock_id INT NOT NULL, details_id INT DEFAULT NULL, name VARCHAR(30) NOT NULL, description LONGTEXT DEFAULT NULL, price DOUBLE PRECISION DEFAULT \'0\' NOT NULL, picture VARCHAR(120) DEFAULT NULL, UNIQUE INDEX UNIQ_D34A04ADDCD6110 (stock_id), UNIQUE INDEX UNIQ_D34A04ADBB1A0722 (details_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_details (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(60) DEFAULT NULL, long_description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C112854AC3 FOREIGN KEY (order_reference_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADDCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADBB1A0722 FOREIGN KEY (details_id) REFERENCES product_details (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C112854AC3');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C14584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADBB1A0722');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADDCD6110');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE order_details');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_details');
        $this->addSql('DROP TABLE stock');
    }
}
