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
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class CurrenciesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    #[Test]
    public function it_gets_a_currency(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'currency.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var CurrencyInterface $currency */
        $currency = $fixtures['currency_gbp'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/currencies/%s', $currency->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/currency/get_currency_response',
            Response::HTTP_OK,
        );
    }

    #[Test]
    public function it_gets_currencies(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'currency.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/currencies', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/currency/get_currencies_response',
            Response::HTTP_OK,
        );
    }

    #[Test]
    public function it_does_not_allow_creating_a_currency_with_invalid_code(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            'POST',
            '/api/v2/admin/currencies',
            [],
            [],
            $header,
            json_encode([
                'code' => 'lol',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/currency/post_currency_with_invalid_code_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    #[Test]
    public function it_creates_a_currency(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/currencies',
            server: $header,
            content: json_encode([
                'code' => 'KRW',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/currency/post_currency_response',
            Response::HTTP_CREATED,
        );
    }
}
