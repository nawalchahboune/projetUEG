<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250331071151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item ADD proof_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251ED7086615 FOREIGN KEY (proof_id) REFERENCES proof (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1F1B251ED7086615 ON item (proof_id)');
        $this->addSql('ALTER TABLE proof DROP FOREIGN KEY FK_FBF940DD126F525E');
        $this->addSql('DROP INDEX IDX_FBF940DD126F525E ON proof');
        $this->addSql('ALTER TABLE proof DROP item_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE proof ADD item_id INT NOT NULL');
        $this->addSql('ALTER TABLE proof ADD CONSTRAINT FK_FBF940DD126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_FBF940DD126F525E ON proof (item_id)');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251ED7086615');
        $this->addSql('DROP INDEX UNIQ_1F1B251ED7086615 ON item');
        $this->addSql('ALTER TABLE item DROP proof_id');
    }
}
