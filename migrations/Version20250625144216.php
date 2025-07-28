<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250625144216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE accomodation_convenience (accomodation_id INT NOT NULL, convenience_id INT NOT NULL, INDEX IDX_7CD0B900FD70509C (accomodation_id), INDEX IDX_7CD0B90013A1CE8C (convenience_id), PRIMARY KEY(accomodation_id, convenience_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE announcement_convenience (announcement_id INT NOT NULL, convenience_id INT NOT NULL, INDEX IDX_40AB08EB913AEA17 (announcement_id), INDEX IDX_40AB08EB13A1CE8C (convenience_id), PRIMARY KEY(announcement_id, convenience_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE announcement_service (announcement_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_1D3CC718913AEA17 (announcement_id), INDEX IDX_1D3CC718ED5CA9E6 (service_id), PRIMARY KEY(announcement_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accomodation_convenience ADD CONSTRAINT FK_7CD0B900FD70509C FOREIGN KEY (accomodation_id) REFERENCES accomodation (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accomodation_convenience ADD CONSTRAINT FK_7CD0B90013A1CE8C FOREIGN KEY (convenience_id) REFERENCES convenience (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement_convenience ADD CONSTRAINT FK_40AB08EB913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement_convenience ADD CONSTRAINT FK_40AB08EB13A1CE8C FOREIGN KEY (convenience_id) REFERENCES convenience (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement_service ADD CONSTRAINT FK_1D3CC718913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement_service ADD CONSTRAINT FK_1D3CC718ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE accomodation_convenience DROP FOREIGN KEY FK_7CD0B900FD70509C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE accomodation_convenience DROP FOREIGN KEY FK_7CD0B90013A1CE8C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement_convenience DROP FOREIGN KEY FK_40AB08EB913AEA17
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement_convenience DROP FOREIGN KEY FK_40AB08EB13A1CE8C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement_service DROP FOREIGN KEY FK_1D3CC718913AEA17
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE announcement_service DROP FOREIGN KEY FK_1D3CC718ED5CA9E6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE accomodation_convenience
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE announcement_convenience
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE announcement_service
        SQL);
    }
}
