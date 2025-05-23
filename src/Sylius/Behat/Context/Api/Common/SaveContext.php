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

namespace Sylius\Behat\Context\Api\Common;

use Behat\Behat\Context\Context;
use Behat\Step\When;
use Sylius\Behat\Client\ApiClientInterface;

final readonly class SaveContext implements Context
{
    public function __construct(private ApiClientInterface $client)
    {
    }

    #[When('I save my changes')]
    #[When('I try to save my changes')]
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    #[When('I save my changes to the images')]
    public function iSaveMyChangesToTheImages(): void
    {
        // Intentionally left blank
    }
}
