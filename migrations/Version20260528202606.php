<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260528202606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Enrich contact_message with type, rating, status, reported_user (FK) and level fields (additive only)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE contact_message ADD type VARCHAR(20) DEFAULT 'contact' NULL");
        $this->addSql("ALTER TABLE contact_message ADD rating SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE contact_message ADD status VARCHAR(20) DEFAULT 'nouveau' NULL");
        $this->addSql("ALTER TABLE contact_message ADD reported_user_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE contact_message ADD reported_current_level NUMERIC(3, 1) DEFAULT NULL");
        $this->addSql("ALTER TABLE contact_message ADD reported_suggested_level NUMERIC(3, 1) DEFAULT NULL");

        $this->addSql('ALTER TABLE contact_message ADD CONSTRAINT FK_2C9211FE6CFEF9D7 FOREIGN KEY (reported_user_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2C9211FE6CFEF9D7 ON contact_message (reported_user_id)');

        $this->addSql("UPDATE contact_message SET type = 'contact' WHERE type IS NULL");
        $this->addSql("UPDATE contact_message SET status = 'nouveau' WHERE status IS NULL");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact_message DROP FOREIGN KEY FK_2C9211FE6CFEF9D7');
        $this->addSql('DROP INDEX IDX_2C9211FE6CFEF9D7 ON contact_message');

        $this->addSql('ALTER TABLE contact_message DROP type');
        $this->addSql('ALTER TABLE contact_message DROP rating');
        $this->addSql('ALTER TABLE contact_message DROP status');
        $this->addSql('ALTER TABLE contact_message DROP reported_user_id');
        $this->addSql('ALTER TABLE contact_message DROP reported_current_level');
        $this->addSql('ALTER TABLE contact_message DROP reported_suggested_level');
    }
}
