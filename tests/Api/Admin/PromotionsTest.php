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
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\CustomerGroupRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\NthOrderRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\ShippingCountryRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class PromotionsTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpAdminContext();

        $this->setUpDefaultPostHeaders();
        $this->setUpDefaultGetHeaders();
        $this->setUpDefaultPutHeaders();
        $this->setUpDefaultPatchHeaders();
        $this->setUpDefaultDeleteHeaders();

        parent::setUp();
    }

    #[Test]
    public function it_gets_promotions(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        $this->requestGet('/api/v2/admin/promotions');

        $this->assertResponseSuccessful('admin/promotion/get_promotions_response');
    }

    #[Test]
    public function it_gets_a_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->requestGet(sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()));

        $this->assertResponseSuccessful('admin/promotion/get_promotion_response');
    }

    #[Test]
    public function it_creates_promotion(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'country.yaml',
            'customer_group.yaml',
            'promotion/product.yaml',
            'promotion/taxon.yaml',
        ]);

        $this->requestPost(
            uri: '/api/v2/admin/promotions',
            body: [
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'channels' => [
                    '/api/v2/admin/channels/WEB',
                ],
                'description' => 'T-Shirts discount',
                'priority' => 22,
                'appliesToDiscounted' => false,
                'exclusive' => true,
                'usageLimit' => 3,
                'couponBased' => true,
                'startsAt' => '2023-10-04 12:30:00',
                'endsAt' => '2023-11-04 12:30:00',
                'translations' => ['en_US' => [
                    'label' => 'T-Shirts discount',
                ]],
                'rules' => [
                    [
                        'type' => CartQuantityRuleChecker::TYPE,
                        'configuration' => [
                            'count' => 6,
                        ],
                    ],
                    [
                        'type' => CustomerGroupRuleChecker::TYPE,
                        'configuration' => [
                            'group_code' => 'vip',
                        ],
                    ],
                    [
                        'type' => NthOrderRuleChecker::TYPE,
                        'configuration' => [
                            'nth' => 2,
                        ],
                    ],
                    [
                        'type' => ShippingCountryRuleChecker::TYPE,
                        'configuration' => [
                            'country' => 'US',
                        ],
                    ],
                    [
                        'type' => HasTaxonRuleChecker::TYPE,
                        'configuration' => [
                            'taxons' => ['MUGS', 'CAPS'],
                        ],
                    ],
                    [
                        'type' => TotalOfItemsFromTaxonRuleChecker::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'taxon' => 'MUGS',
                                'amount' => 1000,
                            ],
                            'MOBILE' => [
                                'taxon' => 'CAPS',
                                'amount' => 2000,
                            ],
                        ],
                    ],
                    [
                        'type' => ContainsProductRuleChecker::TYPE,
                        'configuration' => [
                            'product_code' => 'CAP',
                        ],
                    ],
                    [
                        'type' => ItemTotalRuleChecker::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'amount' => 333,
                            ],
                            'MOBILE' => [
                                'amount' => 222,
                            ],
                        ],
                    ],
                ],
                'actions' => [
                    [
                        'type' => FixedDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'amount' => 1000,
                            ],
                            'MOBILE' => [
                                'amount' => 2000,
                            ],
                        ],
                    ],
                    [
                        'type' => UnitFixedDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'amount' => 3000,
                                'filters' => [
                                    'price_range_filter' => [
                                        'min' => 1000,
                                        'max' => 10000,
                                    ],
                                ],
                            ],
                            'MOBILE' => [
                                'amount' => 4000,
                            ],
                        ],
                    ],
                    [
                        'type' => PercentageDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'percentage' => 0.5,
                        ],
                    ],
                    [
                        'type' => UnitPercentageDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'percentage' => 0.1,
                            ],
                            'MOBILE' => [
                                'percentage' => 1,
                            ],
                        ],
                    ],
                    [
                        'type' => ShippingPercentageDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'percentage' => 0.2,
                        ],
                    ],
                ],
            ],
        );

        $this->assertResponseCreated('admin/promotion/post_promotion_response');
    }

    #[Test]
    public function it_does_not_create_a_promotion_without_required_data(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);

        $this->requestPost(
            uri: '/api/v2/admin/promotions',
            body: [],
        );

        $this->assertResponseUnprocessableEntity('admin/promotion/post_promotion_without_required_data_response');
    }

    #[Test]
    public function it_does_not_create_a_promotion_with_taken_code(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        $this->requestPost(
            uri: '/api/v2/admin/promotions',
            body: [
                'name' => '50% Off on your first order',
                'code' => '50_off',
            ],
        );

        $this->assertResponseUnprocessableEntity('admin/promotion/post_promotion_with_taken_code_response');
    }

    #[Test]
    public function it_does_not_create_a_promotion_with_end_date_earlier_than_start_date(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);

        $this->requestPost(
            uri: '/api/v2/admin/promotions',
            body: [
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'startsAt' => '2023-12-04 12:30:00',
                'endsAt' => '2023-11-04 12:30:00',
            ],
        );

        $this->assertResponseUnprocessableEntity('admin/promotion/post_promotion_with_invalid_dates_response');
    }

    #[Test]
    public function it_does_not_create_a_promotion_with_invalid_rules(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'promotion/channel.yaml']);

        $this->requestPost(
            uri: '/api/v2/admin/promotions',
            body: [
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'rules' => [
                    [
                        'type' => CartQuantityRuleChecker::TYPE,
                        'configuration' => [
                            'count' => 'invalid',
                        ],
                    ],
                    [
                        'type' => CustomerGroupRuleChecker::TYPE,
                        'configuration' => [
                            'group_code' => 'wrong',
                        ],
                    ],
                    [
                        'type' => NthOrderRuleChecker::TYPE,
                        'configuration' => [
                            'nth' => 'invalid',
                        ],
                    ],
                    [
                        'type' => ShippingCountryRuleChecker::TYPE,
                        'configuration' => [
                            'country' => 'wrong',
                        ],
                    ],
                    [
                        'type' => HasTaxonRuleChecker::TYPE,
                        'configuration' => [
                            'taxons' => ['wrong'],
                        ],
                    ],
                    [
                        'type' => TotalOfItemsFromTaxonRuleChecker::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'taxon' => 'wrong',
                                'amount' => 'invalid',
                            ],
                        ],
                    ],
                    [
                        'type' => ContainsProductRuleChecker::TYPE,
                        'configuration' => [
                            'product_code' => 'wrong',
                        ],
                    ],
                    [
                        'type' => ItemTotalRuleChecker::TYPE,
                        'configuration' => [],
                    ],
                    [
                        'type' => 'wrong_type',
                        'configuration' => [],
                    ],
                ],
            ],
        );

        $this->assertResponseUnprocessableEntity('admin/promotion/post_promotion_with_invalid_rules_response');
    }

    #[Test]
    public function it_does_not_create_a_promotion_with_invalid_actions(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'promotion/channel.yaml']);

        $this->requestPost(
            uri: '/api/v2/admin/promotions',
            body: [
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'actions' => [
                    [
                        'type' => FixedDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'amount' => 'invalid',
                            ],
                        ],
                    ],
                    [
                        'type' => UnitFixedDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'filters' => 'invalid',
                            ],
                        ],
                    ],
                    [
                        'type' => PercentageDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'percentage' => -5,
                        ],
                    ],
                    [
                        'type' => UnitPercentageDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'percentage' => 110,
                            ],
                        ],
                    ],
                    [
                        'type' => ShippingPercentageDiscountPromotionActionCommand::TYPE,
                        'configuration' => [],
                    ],
                    [
                        'type' => 'wrong_type',
                        'configuration' => [],
                    ],
                ],
            ],
        );

        $this->assertResponseUnprocessableEntity('admin/promotion/post_promotion_with_invalid_actions_response');
    }

    #[Test]
    public function it_updates_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()),
            body: [
                'name' => 'Christmas',
                'code' => 'new_code',
                'appliesToDiscounted' => true,
                'exclusive' => true,
                'usageLimit' => 11,
                'couponBased' => false,
                'rules' => [
                    [
                        'type' => CartQuantityRuleChecker::TYPE,
                        'configuration' => [
                            'count' => 1,
                        ],
                    ],
                ],
                'actions' => [
                    [
                        'type' => FixedDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'amount' => 2,
                            ],
                            'MOBILE' => [
                                'amount' => 2,
                            ],
                        ],
                    ],
                ],
                'channels' => [
                    '/api/v2/admin/channels/MOBILE',
                ],
                'translations' => ['en_US' => [
                    '@id' => '/api/v2/admin/promotions/50_off/translations/en_US',
                    'label' => 'Christmas',
                ]],
            ],
        );

        $this->assertResponseSuccessful('admin/promotion/put_promotion_response');
    }

    #[Test]
    public function it_updates_promotion_to_last_priority_when_priority_is_minus_one(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()),
            body: [
                'priority' => -1,
            ],
        );

        $this->assertResponseSuccessful('admin/promotion/put_promotion_to_last_priority_when_priority_is_minus_one_response');
    }

    #[Test]
    public function it_does_not_update_a_promotion_with_duplicate_locale_translation(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()),
            body: [
                'translations' => ['en_US' => [
                    'label' => 'Christmas',
                ]],
            ],
        );

        $this->assertResponseUnprocessableEntity('admin/promotion/put_promotion_with_duplicate_locale_translation');
    }

    #[Test]
    public function it_deletes_a_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->requestDelete(sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    #[Test]
    public function it_does_not_delete_the_promotion_in_use(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'promotion/promotion.yaml',
            'promotion/promotion_order.yaml',
        ]);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->requestDelete(sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Test]
    public function it_archives_a_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'promotion/promotion.yaml',
        ]);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->requestPatch(
            uri: sprintf('/api/v2/admin/promotions/%s/archive', $promotion->getCode()),
        );

        $this->assertResponseSuccessful('admin/promotion/archive_promotion');
    }

    #[Test]
    public function it_restores_a_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'promotion/promotion.yaml',
        ]);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_back_to_school'];

        $this->requestPatch(sprintf('/api/v2/admin/promotions/%s/restore', $promotion->getCode()));

        $this->assertResponseSuccessful('admin/promotion/restore_promotion');
    }
}
