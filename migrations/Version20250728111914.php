<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728111914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD phone_number VARCHAR(30) DEFAULT NULL, ADD occupation VARCHAR(50) DEFAULT NULL, CHANGE birth_date birth_date DATE DEFAULT NULL COMMENT '(DC2Type:date_immutable)', CHANGE gender gender VARCHAR(10) DEFAULT NULL, CHANGE billing_address billing_address VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP phone_number, DROP occupation, CHANGE birth_date birth_date DATE NOT NULL COMMENT '(DC2Type:date_immutable)', CHANGE gender gender VARCHAR(10) NOT NULL, CHANGE billing_address billing_address VARCHAR(255) NOT NULL
        SQL);
    }
}
