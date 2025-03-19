<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319110817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, has_purchased TINYINT(1) NOT NULL, url VARCHAR(255) DEFAULT NULL, wishlist_id INT NOT NULL, INDEX IDX_1F1B251EFB8E54CD (wishlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE proof (id INT AUTO_INCREMENT NOT NULL, congrats_message VARCHAR(255) NOT NULL, proof VARCHAR(255) DEFAULT NULL, buyer_id INT DEFAULT NULL, INDEX IDX_FBF940DD6C755722 (buyer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, username VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, lock_status TINYINT(1) NOT NULL, type VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, deadline DATE DEFAULT NULL, owner_id INT NOT NULL, INDEX IDX_9CE12A317E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE wishlist_user (wishlist_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F6AC07BFFB8E54CD (wishlist_id), INDEX IDX_F6AC07BFA76ED395 (user_id), PRIMARY KEY(wishlist_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id)');
        $this->addSql('ALTER TABLE proof ADD CONSTRAINT FK_FBF940DD6C755722 FOREIGN KEY (buyer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A317E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE wishlist_user ADD CONSTRAINT FK_F6AC07BFFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wishlist_user ADD CONSTRAINT FK_F6AC07BFA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EFB8E54CD');
        $this->addSql('ALTER TABLE proof DROP FOREIGN KEY FK_FBF940DD6C755722');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A317E3C61F9');
        $this->addSql('ALTER TABLE wishlist_user DROP FOREIGN KEY FK_F6AC07BFFB8E54CD');
        $this->addSql('ALTER TABLE wishlist_user DROP FOREIGN KEY FK_F6AC07BFA76ED395');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE proof');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('DROP TABLE wishlist_user');
    }
}
