<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251107063835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, email VARCHAR(180) NOT NULL, display_name VARCHAR(255) DEFAULT NULL, google_id VARCHAR(64) DEFAULT NULL, roles JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_users_email ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX uniq_users_google_id ON users (google_id)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN users.email IS \'(DC2Type:email)\'');
        $this->addSql('COMMENT ON COLUMN users.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE events ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE events ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE events ALTER organiser_id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE events ALTER organiser_id TYPE VARCHAR(36)');
        $this->addSql('COMMENT ON COLUMN events.id IS \'(DC2Type:event_id)\'');
        $this->addSql('COMMENT ON COLUMN events.organiser_id IS \'(DC2Type:organiser_id)\'');
        $this->addSql('ALTER TABLE organisers ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE organisers ALTER id TYPE VARCHAR(36)');
        $this->addSql('COMMENT ON COLUMN organisers.id IS \'(DC2Type:organiser_id)\'');
        $this->addSql('ALTER TABLE tickets ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE tickets ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE tickets ALTER event_id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE tickets ALTER event_id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE tickets ALTER organiser_id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE tickets ALTER organiser_id TYPE VARCHAR(36)');
        $this->addSql('COMMENT ON COLUMN tickets.id IS \'(DC2Type:ticket_id)\'');
        $this->addSql('COMMENT ON COLUMN tickets.event_id IS \'(DC2Type:event_id)\'');
        $this->addSql('COMMENT ON COLUMN tickets.organiser_id IS \'(DC2Type:organiser_id)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE events ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE events ALTER organiser_id TYPE VARCHAR(36)');
        $this->addSql('COMMENT ON COLUMN events.id IS NULL');
        $this->addSql('COMMENT ON COLUMN events.organiser_id IS NULL');
        $this->addSql('ALTER TABLE organisers ALTER id TYPE VARCHAR(36)');
        $this->addSql('COMMENT ON COLUMN organisers.id IS NULL');
        $this->addSql('ALTER TABLE tickets ALTER id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE tickets ALTER event_id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE tickets ALTER organiser_id TYPE VARCHAR(36)');
        $this->addSql('COMMENT ON COLUMN tickets.id IS NULL');
        $this->addSql('COMMENT ON COLUMN tickets.event_id IS NULL');
        $this->addSql('COMMENT ON COLUMN tickets.organiser_id IS NULL');
    }
}
