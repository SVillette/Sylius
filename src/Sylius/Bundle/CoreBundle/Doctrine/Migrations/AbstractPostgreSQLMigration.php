<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\Migrations;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration as BaseAbstractMigration;

abstract class AbstractPostgreSQLMigration extends BaseAbstractMigration implements MigrationSkipInterface
{
    public function preUp(Schema $schema): void
    {
        $this->skipIf(!$this->isPostgreSQL(), 'This migration can only be executed on \'PostgreSQL\'.');
    }

    public function preDown(Schema $schema): void
    {
        $this->skipIf(!$this->isPostgreSQL(), 'This migration can only be executed on \'PostgreSQL\'.');
    }

    protected function isPostgreSQL(): bool
    {
        return
            class_exists(PostgreSQLPlatform::class) &&
            is_a($this->connection->getDatabasePlatform(), PostgreSQLPlatform::class, true)
        ;
    }

    /** @deprecated This method is deprecated and will be removed in Sylius 3.0 */
    protected function markAsExecuted(string $version): void
    {
        $this->connection->executeQuery(
            sprintf('INSERT INTO sylius_migrations (version, executed_at) VALUES (\'%s\', NOW())', $version),
        );
        $this->connection->commit();
    }

    /** @deprecated This method is deprecated and will be removed in Sylius 3.0 */
    protected function getVersion(): string
    {
        return addslashes((new \ReflectionClass($this))->getName());
    }
}
