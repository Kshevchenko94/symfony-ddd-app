<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260728085725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('audit_log');
        $table->addColumn('id', 'guid', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 255]);
        $table->addColumn('payload', 'text');
        $table->addColumn('created_at', 'integer', ['unsigned' => true]);

        $table->addIndex(['id'], 'PRIMARY');
        $table->addIndex(['user_id'], 'idx_audit_log_user_id');
        $table->addIndex(['created_at'], 'idx_audit_log_created_at');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('audit_log');
    }
}
