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

namespace Sylius\Behat\Page\Admin;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\ProductInterface;

interface DashboardPageInterface extends SymfonyPageInterface
{
    public function getTotalSales(): string;

    public function getNumberOfPaidOrders(): int;

    public function getNumberOfNewOrdersInTheList(): int;

    public function getNumberOfNewCustomers(): int;

    public function getNumberOfNewCustomersInTheList(): int;

    public function getAverageOrderValue(): string;

    public function getDashboardHeader(): string;

    public function logOut(): void;

    public function chooseChannel(string $channelName): void;

    public function chooseYearSplitByMonthsInterval(): void;

    public function chooseMonthSplitByDaysInterval(): void;

    public function choosePreviousPeriod(): void;

    public function chooseNextPeriod(): void;

    public function searchForProductViaNavbar(ProductInterface $productName): void;

    public function getNumberOfOrdersToProcess(): int;

    public function getNumberOfPendingPayments(): int;

    public function getNumberOfProductReviewsToApprove(): int;

    public function getNumberOfProductVariantsOutOfStock(): int;

    public function getNumberOfShipmentsToShip(): int;
}
