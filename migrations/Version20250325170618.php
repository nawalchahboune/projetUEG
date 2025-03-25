<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325170618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `invitation` (id INT AUTO_INCREMENT NOT NULL, receivers_id INT DEFAULT NULL, INDEX IDX_F11D61A2C5BEF871 (receivers_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, sender_id INT DEFAULT NULL, recipient_id INT DEFAULT NULL, invitation_id INT DEFAULT NULL, item_id INT DEFAULT NULL, INDEX IDX_BF5476CAF624B39D (sender_id), INDEX IDX_BF5476CAE92F8F78 (recipient_id), INDEX IDX_BF5476CAA35D7AF0 (invitation_id), INDEX IDX_BF5476CA126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE test (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `invitation` ADD CONSTRAINT FK_F11D61A2C5BEF871 FOREIGN KEY (receivers_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAF624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE92F8F78 FOREIGN KEY (recipient_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA35D7AF0 FOREIGN KEY (invitation_id) REFERENCES `invitation` (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA126F525E FOREIGN KEY (item_id) REFERENCES item (id)');
        $this->addSql('ALTER TABLE user DROP collaboration_token, DROP public_token');
        $this->addSql('ALTER TABLE wishlist CHANGE collaboration_token collaboration_token VARCHAR(36) DEFAULT NULL, CHANGE public_token public_token VARCHAR(36) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CE12A3124CB2F13 ON wishlist (collaboration_token)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CE12A31AE981E3B ON wishlist (public_token)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `invitation` DROP FOREIGN KEY FK_F11D61A2C5BEF871');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAF624B39D');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE92F8F78');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA35D7AF0');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA126F525E');
        $this->addSql('DROP TABLE `invitation`');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE test');
        $this->addSql('DROP INDEX UNIQ_9CE12A3124CB2F13 ON wishlist');
        $this->addSql('DROP INDEX UNIQ_9CE12A31AE981E3B ON wishlist');
        $this->addSql('ALTER TABLE wishlist CHANGE collaboration_token collaboration_token VARCHAR(255) DEFAULT NULL, CHANGE public_token public_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD collaboration_token VARCHAR(255) DEFAULT NULL, ADD public_token VARCHAR(255) DEFAULT NULL');
    }
}
