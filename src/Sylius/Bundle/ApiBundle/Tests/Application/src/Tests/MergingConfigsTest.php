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

namespace Sylius\Bundle\ApiBundle\Application\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\Test;

final class MergingConfigsTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles();

        $this->setUpTest();
    }

    #[Test]
    public function it_allows_to_add_a_new_operation_with_xml(): void
    {
        static::createClient()->request('GET', '/api/v2/shop/channels-new-path-xml');

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['@type' => 'hydra:Collection']);
    }

    #[Test]
    public function it_allows_to_add_a_new_operation_with_yaml(): void
    {
        static::createClient()->request('GET', '/api/v2/shop/currencies-new-path-yaml');

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['@type' => 'hydra:Collection']);
    }

    #[Test]
    public function it_allows_to_overwrite_the_path_of_an_existing_endpoint_with_xml(): void
    {
        static::createClient()->request('GET', '/api/v2/shop/channels/WEB');

        self::assertResponseStatusCodeSame(404);

        static::createClient()->request('GET', '/api/v2/shop/channels/new-xml/WEB');

        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function it_allows_to_overwrite_the_path_of_an_existing_endpoint_with_yaml(): void
    {
        static::createClient()->request('GET', '/api/v2/shop/currencies/USD');

        self::assertResponseStatusCodeSame(404);

        static::createClient()->request('GET', '/api/v2/shop/currencies/new-yaml/USD');

        self::assertResponseIsSuccessful();
    }

    #[Test]
    public function it_allows_to_overwrite_the_input_class_of_an_existing_endpoint_with_yaml(): void
    {
        static::createClient()->request('POST', '/api/v2/shop/bar', [
            'json' => [
                'foo' => 'test',
                'bar' => 'test',
            ],
        ]);

        self::assertResponseStatusCodeSame(400);

        static::createClient()->request('POST', '/api/v2/shop/bar', [
            'json' => [
                'baz' => 'test',
            ],
        ]);

        self::assertResponseIsSuccessful();
    }
}
