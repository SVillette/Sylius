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
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaxonImagesTest extends JsonApiTestCase
{
    #[Test]
    public function it_gets_a_taxon_image(): void
    {
        $fixtures = $this->loadFixturesFromFile('taxon_image.yaml');

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        /** @var TaxonInterface $taxon */
        $taxon = $taxonImage->getOwner();

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/taxons/%s/images/%s', $taxon->getCode(), $taxonImage->getId()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/taxon_image/get_taxon_image',
            Response::HTTP_OK,
        );
    }

    #[Test]
    public function it_gets_a_taxon_image_with_an_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFile('taxon_image.yaml');

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        /** @var TaxonInterface $taxon */
        $taxon = $taxonImage->getOwner();

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/taxons/%s/images/%s', $taxon->getCode(), $taxonImage->getId()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'sylius_small'],
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/taxon_image/get_taxon_image_with_image_filter',
            Response::HTTP_OK,
        );
    }

    #[Test]
    public function it_prevents_getting_a_taxon_image_with_an_invalid_image_filter(): void
    {
        $fixtures = $this->loadFixturesFromFile('taxon_image.yaml');

        /** @var TaxonImageInterface $taxonImage */
        $taxonImage = $fixtures['taxon_thumbnail'];

        /** @var TaxonInterface $taxon */
        $taxon = $taxonImage->getOwner();

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/taxons/%s/images/%s', $taxon->getCode(), $taxonImage->getId()),
            parameters: [ImageNormalizer::FILTER_QUERY_PARAMETER => 'invalid'],
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'common/image/invalid_filter',
            Response::HTTP_BAD_REQUEST,
        );
    }
}
