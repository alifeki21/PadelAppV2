<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260528103837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_message (id INT AUTO_INCREMENT NOT NULL, subject VARCHAR(200) NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_2C9211FEA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE court (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, is_indoor TINYINT NOT NULL, price_per_hour NUMERIC(6, 2) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, reservation_date DATE NOT NULL, reservation_time TIME NOT NULL, player_count SMALLINT NOT NULL, requirements VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, court_id INT NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), INDEX IDX_42C84955E3184009 (court_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tournament (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, level VARCHAR(20) DEFAULT NULL, max_teams SMALLINT DEFAULT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tournament_registration (id INT AUTO_INCREMENT NOT NULL, partner_name VARCHAR(100) DEFAULT NULL, registered_at DATETIME NOT NULL, user_id INT NOT NULL, tournament_id INT NOT NULL, INDEX IDX_F42ADBF1A76ED395 (user_id), INDEX IDX_F42ADBF133D1A3E7 (tournament_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, phone VARCHAR(20) NOT NULL, skill_level NUMERIC(3, 1) DEFAULT NULL, preferred_position VARCHAR(10) DEFAULT NULL, playing_hand VARCHAR(10) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE contact_message ADD CONSTRAINT FK_2C9211FEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E3184009 FOREIGN KEY (court_id) REFERENCES court (id)');
        $this->addSql('ALTER TABLE tournament_registration ADD CONSTRAINT FK_F42ADBF1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE tournament_registration ADD CONSTRAINT FK_F42ADBF133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
        $this->addSql('ALTER TABLE reservations DROP FOREIGN KEY `fk_reservations_user`');
        $this->addSql('ALTER TABLE tournament_registrations DROP FOREIGN KEY `tournament_registrations_ibfk_1`');
        $this->addSql('ALTER TABLE tournament_registrations DROP FOREIGN KEY `tournament_registrations_ibfk_2`');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE tournament_registrations');
        $this->addSql('DROP TABLE tournaments');
        $this->addSql('DROP TABLE users');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, court_number TINYINT NOT NULL, reservation_date DATE NOT NULL, reservation_time TIME NOT NULL, duration_minutes SMALLINT UNSIGNED DEFAULT 90 NOT NULL, player_count TINYINT DEFAULT 4 NOT NULL, requirements VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX fk_reservations_user (user_id), INDEX idx_reservation_lookup (court_number, reservation_date), UNIQUE INDEX uniq_court_slot (court_number, reservation_date, reservation_time), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tournament_registrations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, tournament_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, partner_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, registered_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX tournament_id (tournament_id), INDEX user_id (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tournaments (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, event_date DATE NOT NULL, status ENUM(\'upcoming\', \'ongoing\', \'completed\') CHARACTER SET utf8mb4 DEFAULT \'upcoming\' NOT NULL COLLATE `utf8mb4_unicode_ci`, max_teams INT UNSIGNED DEFAULT 16 NOT NULL, current_teams INT UNSIGNED DEFAULT 0 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, first_name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, last_name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, email VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(20) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password_hash VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, skill_level NUMERIC(3, 1) DEFAULT \'1.0\' NOT NULL, preferred_position ENUM(\'left\', \'right\', \'both\') CHARACTER SET utf8mb4 DEFAULT \'both\' NOT NULL COLLATE `utf8mb4_unicode_ci`, playing_hand ENUM(\'left\', \'right\') CHARACTER SET utf8mb4 DEFAULT \'right\' NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, UNIQUE INDEX email (email), INDEX idx_email (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT `fk_reservations_user` FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('ALTER TABLE tournament_registrations ADD CONSTRAINT `tournament_registrations_ibfk_1` FOREIGN KEY (tournament_id) REFERENCES tournaments (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournament_registrations ADD CONSTRAINT `tournament_registrations_ibfk_2` FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contact_message DROP FOREIGN KEY FK_2C9211FEA76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955E3184009');
        $this->addSql('ALTER TABLE tournament_registration DROP FOREIGN KEY FK_F42ADBF1A76ED395');
        $this->addSql('ALTER TABLE tournament_registration DROP FOREIGN KEY FK_F42ADBF133D1A3E7');
        $this->addSql('DROP TABLE contact_message');
        $this->addSql('DROP TABLE court');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE tournament');
        $this->addSql('DROP TABLE tournament_registration');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
