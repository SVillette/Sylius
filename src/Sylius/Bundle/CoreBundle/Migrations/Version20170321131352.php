<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Sylius\Bundle\CoreBundle\Doctrine\Migrations\AbstractMySqlMigration;

class Version20170321131352 extends AbstractMySqlMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_variant DROP available_on, DROP available_until');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sylius_product_variant ADD available_on DATETIME DEFAULT NULL, ADD available_until DATETIME DEFAULT NULL');
    }
}
