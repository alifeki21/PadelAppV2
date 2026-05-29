<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260528210000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add manual fallback fields for reported player (name + phone) when player not registered';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact_message ADD reported_player_name VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_message ADD reported_player_phone VARCHAR(30) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE contact_message DROP reported_player_name');
        $this->addSql('ALTER TABLE contact_message DROP reported_player_phone');
    }
}
