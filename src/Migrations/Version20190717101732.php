<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190717101732 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cttest (id INT AUTO_INCREMENT NOT NULL, author VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, published TINYINT(1) NOT NULL, tag JSON NOT NULL, cat JSON NOT NULL, aaa VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE page_test');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE page_test (id INT AUTO_INCREMENT NOT NULL, author VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, display_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, updated_at DATETIME NOT NULL, published TINYINT(1) NOT NULL, test VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, tag JSON NOT NULL, cat JSON NOT NULL, test_bis VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE cttest');
    }
}
