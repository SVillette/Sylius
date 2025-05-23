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

namespace Sylius\Behat\Page\Admin\Administrator;

use Sylius\Behat\Page\SymfonyPage;

class ImpersonateUserPage extends SymfonyPage implements ImpersonateUserPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_impersonate_user';
    }
}
