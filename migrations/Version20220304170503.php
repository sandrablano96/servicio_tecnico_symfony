<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220304170503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cliente CHANGE telefono telefono VARCHAR(9) NOT NULL');
        $this->addSql('ALTER TABLE incidencia DROP FOREIGN KEY FK_C7C6728CDB38439E');
        $this->addSql('DROP INDEX IDX_C7C6728CDB38439E ON incidencia');
        $this->addSql('ALTER TABLE incidencia ADD estado VARCHAR(20) NOT NULL, DROP usuario_id');
        $this->addSql('ALTER TABLE usuario ADD incidencias_id INT NOT NULL');
        $this->addSql('ALTER TABLE usuario ADD CONSTRAINT FK_2265B05DA9945E2A FOREIGN KEY (incidencias_id) REFERENCES incidencia (id)');
        $this->addSql('CREATE INDEX IDX_2265B05DA9945E2A ON usuario (incidencias_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE cliente CHANGE telefono telefono VARCHAR(11) DEFAULT NULL');
        $this->addSql('ALTER TABLE incidencia ADD usuario_id INT NOT NULL, DROP estado');
        $this->addSql('ALTER TABLE incidencia ADD CONSTRAINT FK_C7C6728CDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('CREATE INDEX IDX_C7C6728CDB38439E ON incidencia (usuario_id)');
        $this->addSql('ALTER TABLE usuario DROP FOREIGN KEY FK_2265B05DA9945E2A');
        $this->addSql('DROP INDEX IDX_2265B05DA9945E2A ON usuario');
        $this->addSql('ALTER TABLE usuario DROP incidencias_id');
    }
}
