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

namespace Sylius\Bundle\AdminBundle\PendingAction\Provider;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final readonly class CountProductVariantsOutOfStockProvider implements PendingActionCountProviderInterface
{
    public function __construct(private ProductVariantRepositoryInterface $productVariantRepository)
    {
    }

    public function count(?ChannelInterface $channel = null): int
    {
        return $this->productVariantRepository->countTrackedOutOfStock();
    }
}
