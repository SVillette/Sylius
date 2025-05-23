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

namespace Sylius\Tests\Api\Admin;

use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Addressing\Model\ProvinceInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProvincesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    #[Test]
    public function it_gets_a_province(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'country.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProvinceInterface $province */
        $province = $fixtures['province_US_WY'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/countries/%s/provinces/%s', $province->getCountry()->getCode(), $province->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/province/get_province_response',
            Response::HTTP_OK,
        );
    }

    #[Test]
    public function it_updates_an_existing_province(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'country.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProvinceInterface $province */
        $province = $fixtures['province_US_MI'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/countries/%s/provinces/%s', $province->getCountry()->getCode(), $province->getCode()),
            server: $header,
            content: json_encode([
                'abbreviation' => 'Minn.',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/province/put_province_response',
            Response::HTTP_OK,
        );
    }
}
