<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231127210356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE assignation (id INT AUTO_INCREMENT NOT NULL, gifter_id INT NOT NULL, giftee_id INT NOT NULL, present_status VARCHAR(4096) DEFAULT NULL, UNIQUE INDEX UNIQ_D2A03CE0609EF953 (gifter_id), UNIQUE INDEX UNIQ_D2A03CE0AD509675 (giftee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, nickname VARCHAR(255) NOT NULL, passcode VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant_info (id INT AUTO_INCREMENT NOT NULL, participant_id INT NOT NULL, address VARCHAR(4096) NOT NULL, observations VARCHAR(4096) NOT NULL, wishlist VARCHAR(4096) NOT NULL, UNIQUE INDEX UNIQ_1F3D23699D1C3019 (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assignation ADD CONSTRAINT FK_D2A03CE0609EF953 FOREIGN KEY (gifter_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE assignation ADD CONSTRAINT FK_D2A03CE0AD509675 FOREIGN KEY (giftee_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE participant_info ADD CONSTRAINT FK_1F3D23699D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assignation DROP FOREIGN KEY FK_D2A03CE0609EF953');
        $this->addSql('ALTER TABLE assignation DROP FOREIGN KEY FK_D2A03CE0AD509675');
        $this->addSql('ALTER TABLE participant_info DROP FOREIGN KEY FK_1F3D23699D1C3019');
        $this->addSql('DROP TABLE assignation');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE participant_info');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
