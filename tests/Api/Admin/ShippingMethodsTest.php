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
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ShippingMethodsTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();
        $this->setUpDefaultPostHeaders();
        $this->setUpDefaultPutHeaders();

        parent::setUp();
    }

    #[Test]
    public function it_gets_shipping_methods(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        $this->requestGet('/api/v2/admin/shipping-methods');

        $this->assertResponseSuccessful('admin/shipping_method/get_shipping_methods_response');
    }

    #[Test]
    public function it_gets_a_shipping_method(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->requestGet(sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()));

        $this->assertResponseSuccessful('admin/shipping_method/get_shipping_method_response');
    }

    #[Test]
    public function it_creates_a_shipping_method(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'zones.yaml',
        ]);

        $this->requestPost(
            uri: '/api/v2/admin/shipping-methods',
            body: [
                'name' => 'UPS',
                'code' => 'UPS',
                'shippingChargesCalculator' => 'flat_rate',
                'shippingChargesCalculatorConfiguration' => [
                    'WEB' => [
                        'amount' => 500,
                    ],
                    'MOBILE' => [
                        'amount' => 500,
                    ],
                ],
                'zone' => '/api/v2/admin/zones/EU',
                'channels' => [
                    '/api/v2/admin/channels/WEB',
                ],
                'rules' => [
                    [
                        'type' => 'total_weight_greater_than_or_equal',
                        'configuration' => [
                            'weight' => 123,
                        ],
                    ],
                    [
                        'type' => 'total_weight_less_than_or_equal',
                        'configuration' => [
                            'weight' => 123,
                        ],
                    ],
                    [
                        'type' => 'order_total_greater_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 123,
                            ],
                            'WEB' => [
                                'amount' => 123,
                            ],
                        ],
                    ],
                    [
                        'type' => 'order_total_less_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 123,
                            ],
                            'WEB' => [
                                'amount' => 123,
                            ],
                        ],
                    ],
                ],
                'translations' => [
                    'en_US' => [
                        'name' => 'UPS',
                        'description' => 'This is a UPS shipping method.',
                    ],
                ],
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/post_shipping_method_response',
            Response::HTTP_CREATED,
        );
    }

    #[Test]
    public function it_does_not_create_a_shipping_method_with_wrong_calculator_configuration(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'zones.yaml',
        ]);

        $this->requestPost(
            uri: '/api/v2/admin/shipping-methods',
            body: [
                'name' => 'UPS',
                'code' => 'UPS',
                'shippingChargesCalculator' => 'flat_rate',
                'shippingChargesCalculatorConfiguration' => [
                    'WEB' => [
                        'amount' => 'wrong_value',
                    ],
                    'MOBILE' => [
                        'amount' => 500,
                    ],
                ],
                'zone' => '/api/v2/admin/zones/EU',
                'channels' => [
                    '/api/v2/admin/channels/WEB',
                ],
                'translations' => [
                    'en_US' => [
                        'name' => 'UPS',
                        'description' => 'This is a UPS shipping method.',
                    ],
                ],
            ],
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'configuration[WEB][amount]',
                    'message' => 'This value should be a valid number.',
                ],
                [
                    'propertyPath' => 'configuration[WEB][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
            ],
        );
    }

    #[Test]
    public function it_does_not_create_a_shipping_method_with_wrong_rule_configuration(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'zones.yaml',
        ]);

        $this->requestPost(
            uri: '/api/v2/admin/shipping-methods',
            body: [
                'name' => 'UPS',
                'code' => 'UPS',
                'shippingChargesCalculator' => 'flat_rate',
                'shippingChargesCalculatorConfiguration' => [
                    'WEB' => [
                        'amount' => 500,
                    ],
                    'MOBILE' => [
                        'amount' => 500,
                    ],
                ],
                'zone' => '/api/v2/admin/zones/EU',
                'channels' => [
                    '/api/v2/admin/channels/WEB',
                ],
                'rules' => [
                    [
                        'type' => 'total_weight_greater_than_or_equal',
                        'configuration' => [
                            'weight' => 'wrong_value',
                        ],
                    ],
                    [
                        'type' => 'total_weight_less_than_or_equal',
                        'configuration' => [
                            'weight' => 'wrong_value',
                        ],
                    ],
                    [
                        'type' => 'order_total_greater_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 'wrong_value',
                            ],
                            'WEB' => [
                                'amount' => 'wrong_value',
                            ],
                        ],
                    ],
                    [
                        'type' => 'order_total_less_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 'wrong_value',
                            ],
                            'WEB' => [
                                'amount' => 'wrong_value',
                            ],
                        ],
                    ],
                ],
                'translations' => [
                    'en_US' => [
                        'name' => 'UPS',
                        'description' => 'This is a UPS shipping method.',
                    ],
                ],
            ],
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'rules[0].configuration[weight]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[1].configuration[weight]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[2].configuration[MOBILE][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[2].configuration[WEB][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[3].configuration[MOBILE][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[3].configuration[WEB][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
            ],
        );
    }

    #[Test]
    public function it_updates_a_shipping_method_rules(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            body: [
                'rules' => [
                    [
                        'type' => 'total_weight_greater_than_or_equal',
                        'configuration' => [
                            'weight' => 123,
                        ],
                    ],
                    [
                        'type' => 'total_weight_less_than_or_equal',
                        'configuration' => [
                            'weight' => 123,
                        ],
                    ],
                    [
                        'type' => 'order_total_greater_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 123,
                            ],
                            'WEB' => [
                                'amount' => 123,
                            ],
                        ],
                    ],
                    [
                        'type' => 'order_total_less_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 123,
                            ],
                            'WEB' => [
                                'amount' => 123,
                            ],
                        ],
                    ],
                ],
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/update_shipping_method_rules_response',
        );
    }

    #[Test]
    public function it_updates_shipping_method_calculator_configuration(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            body: [
                'calculator' => 'per_unit_rate',
                'calculatorConfiguration' => [
                    'WEB' => [
                        'amount' => 123,
                    ],
                    'MOBILE' => [
                        'amount' => 123,
                    ],
                ],
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/update_shipping_method_calculator_configuration_response',
        );
    }

    #[Test]
    public function it_does_not_update_shipping_method_calculator_configuration_with_wrong_configuration(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            body: [
                'calculator' => 'per_unit_rate',
                'configuration' => [
                    'WEB' => [
                        'amount' => 'wrong_value',
                    ],
                    'WRONG_CODE' => [
                        'amount' => 'wrong_value',
                    ],
                ],
            ],
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'configuration[WEB][amount]',
                    'message' => 'This value should be a valid number.',
                ],
                [
                    'propertyPath' => 'configuration[WEB][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'configuration[MOBILE]',
                    'message' => 'This field is missing.',
                ],
                [
                    'propertyPath' => 'configuration[WRONG_CODE]',
                    'message' => 'This field was not expected.',
                ],
            ],
        );
    }

    #[Test]
    public function it_does_not_update_a_shipping_method_with_duplicate_locale_translation(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            body: [
                'translations' => [
                    'en_US' => [
                        'name' => 'New UPS',
                    ],
                ],
            ],
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'translations[en_US].locale',
                    'message' => 'A translation for the "en_US" locale code already exists.',
                ],
            ],
        );
    }

    #[Test]
    public function it_does_not_update_shipping_methods_rules_with_wrong_configuration(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            body: [
                'rules' => [
                    [
                        'type' => 'total_weight_greater_than_or_equal',
                        'configuration' => [
                            'weight' => 'wrong_value',
                        ],
                    ],
                    [
                        'type' => 'total_weight_less_than_or_equal',
                        'configuration' => [
                            'weight' => 'wrong_value',
                        ],
                    ],
                    [
                        'type' => 'order_total_greater_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 'wrong_value',
                            ],
                            'WEB' => [
                                'amount' => 'wrong_value',
                            ],
                        ],
                    ],
                    [
                        'type' => 'order_total_less_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 'wrong_value',
                            ],
                            'WEB' => [
                                'amount' => 'wrong_value',
                            ],
                        ],
                    ],
                ],
            ],
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'rules[0].configuration[weight]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[1].configuration[weight]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[2].configuration[MOBILE][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[2].configuration[WEB][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[3].configuration[MOBILE][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[3].configuration[WEB][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
            ],
        );
    }

    #[Test]
    public function it_does_not_update_shipping_methods_rules_with_wrong_types(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            body: [
                'rules' => [
                    [
                        'type' => 'wrong_type',
                        'configuration' => [
                            'weight' => 123,
                        ],
                    ],
                ],
            ],
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'rules[0].type',
                    'message' => 'Invalid rule type. Available rule types are total_weight_greater_than_or_equal, total_weight_less_than_or_equal, order_total_greater_than_or_equal, order_total_less_than_or_equal.',
                ],
            ],
        );
    }

    #[Test]
    public function it_archives_a_shipping_method(): void
    {
        $this->setUpDefaultPatchHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->requestPatch(sprintf('/api/v2/admin/shipping-methods/%s/archive', $shippingMethod->getCode()));

        $this->assertResponseSuccessful('admin/shipping_method/archive_shipping_method_response');
    }

    #[Test]
    public function it_restores_a_shipping_method(): void
    {
        $this->setUpDefaultPatchHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->requestPatch(sprintf('/api/v2/admin/shipping-methods/%s/archive', $shippingMethod->getCode()));
        $this->requestPatch(sprintf('/api/v2/admin/shipping-methods/%s/restore', $shippingMethod->getCode()));

        $this->assertResponseSuccessful('admin/shipping_method/restore_shipping_method_response');
    }

    #[Test]
    public function it_deletes_a_shipping_method(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->requestDelete(sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
