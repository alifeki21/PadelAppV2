<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260526164946 extends AbstractMigration
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
        $this->addSql('ALTER TABLE contact_message ADD CONSTRAINT FK_2C9211FEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E3184009 FOREIGN KEY (court_id) REFERENCES court (id)');
        $this->addSql('ALTER TABLE tournament_registration ADD CONSTRAINT FK_F42ADBF1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE tournament_registration ADD CONSTRAINT FK_F42ADBF133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
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
    }
}
