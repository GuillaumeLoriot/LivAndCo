<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904095518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91C7E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_4DB9D91C7E3C61F9 ON announcement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement DROP owner_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement ADD owner_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4DB9D91C7E3C61F9 ON announcement (owner_id)
        SQL);
    }
}
