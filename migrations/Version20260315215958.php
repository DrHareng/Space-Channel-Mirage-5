<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260315215958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE army (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, shortname VARCHAR(10) NOT NULL)');
        $this->addSql('CREATE TABLE army_list (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(500) NOT NULL, author_id INTEGER NOT NULL, army_id INTEGER NOT NULL, CONSTRAINT FK_5AE71996F675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5AE7199618D2742D FOREIGN KEY (army_id) REFERENCES army (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_5AE71996F675F31B ON army_list (author_id)');
        $this->addSql('CREATE INDEX IDX_5AE7199618D2742D ON army_list (army_id)');
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, content CLOB NOT NULL, created_at DATETIME NOT NULL, author_id INTEGER NOT NULL, photo_id INTEGER NOT NULL, CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9474526C7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE INDEX IDX_9474526C7E9E4C8C ON comment (photo_id)');
        $this->addSql('CREATE TABLE photo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, author_id INTEGER NOT NULL, CONSTRAINT FK_14B78418F675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_14B78418F675F31B ON photo (author_id)');
        $this->addSql('CREATE TABLE photo_tag (photo_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY (photo_id, tag_id), CONSTRAINT FK_8C2D8E577E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8C2D8E57BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8C2D8E577E9E4C8C ON photo_tag (photo_id)');
        $this->addSql('CREATE INDEX IDX_8C2D8E57BAD26311 ON photo_tag (tag_id)');
        $this->addSql('CREATE TABLE photo_user (photo_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY (photo_id, user_id), CONSTRAINT FK_CA264BD7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_CA264BDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_CA264BD7E9E4C8C ON photo_user (photo_id)');
        $this->addSql('CREATE INDEX IDX_CA264BDA76ED395 ON photo_user (user_id)');
        $this->addSql('CREATE TABLE tag (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B783989D9B62 ON tag (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE army');
        $this->addSql('DROP TABLE army_list');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE photo');
        $this->addSql('DROP TABLE photo_tag');
        $this->addSql('DROP TABLE photo_user');
        $this->addSql('DROP TABLE tag');
    }
}
