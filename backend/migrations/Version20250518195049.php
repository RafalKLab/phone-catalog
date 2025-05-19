<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518195049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE model_capacity DROP FOREIGN KEY FK_7F32E09766B6F0BA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE model_capacity DROP FOREIGN KEY FK_7F32E0977975B7E7
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE model_capacity
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE items ADD capacity_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE items ADD CONSTRAINT FK_E11EE94D66B6F0BA FOREIGN KEY (capacity_id) REFERENCES capacities (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E11EE94D66B6F0BA ON items (capacity_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE model_capacity (model_id INT NOT NULL, capacity_id INT NOT NULL, INDEX IDX_7F32E0977975B7E7 (model_id), INDEX IDX_7F32E09766B6F0BA (capacity_id), PRIMARY KEY(model_id, capacity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE model_capacity ADD CONSTRAINT FK_7F32E09766B6F0BA FOREIGN KEY (capacity_id) REFERENCES capacities (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE model_capacity ADD CONSTRAINT FK_7F32E0977975B7E7 FOREIGN KEY (model_id) REFERENCES models (id) ON UPDATE NO ACTION ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE items DROP FOREIGN KEY FK_E11EE94D66B6F0BA
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E11EE94D66B6F0BA ON items
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE items DROP capacity_id
        SQL);
    }
}
