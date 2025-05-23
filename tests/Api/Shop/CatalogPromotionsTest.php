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

namespace Sylius\Tests\Api\Shop;

use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CatalogPromotionsTest extends JsonApiTestCase
{
    #[Test]
    public function it_gets_catalog_promotion(): void
    {
        $catalogPromotion = $this->loadFixturesAndGetCatalogPromotion();

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/catalog-promotions/%s', $catalogPromotion->getCode()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/catalog_promotion/get_catalog_promotion_response',
            Response::HTTP_OK,
        );
    }

    private function loadFixturesAndGetCatalogPromotion(): CatalogPromotionInterface
    {
        $fixtures = $this->loadFixturesFromFiles(['channel/channel.yaml', 'catalog_promotion/catalog_promotion.yaml']);

        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $fixtures['catalog_promotion'];

        return $catalogPromotion;
    }
}
