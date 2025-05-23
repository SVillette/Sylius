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

namespace Sylius\Behat\Page\Shop\Order;

use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\DriverHelper;

class ThankYouPage extends SymfonyPage implements ThankYouPageInterface
{
    public function goToTheChangePaymentMethodPage(): void
    {
        $this->getElement('payment_method_page')->click();

        DriverHelper::waitForPageToLoad($this->getSession());
    }

    public function goToOrderDetailsInAccount(): void
    {
        $this->getElement('order_details_in_account')->click();
    }

    public function hasThankYouMessage(): bool
    {
        $thankYouMessage = $this->getElement('thank_you')->getText();

        return str_contains($thankYouMessage, 'Thank you!');
    }

    public function getInstructions(): string
    {
        return $this->getElement('instructions')->getText();
    }

    public function hasInstructions(): bool
    {
        return $this->hasElement('instructions');
    }

    public function hasChangePaymentMethodButton(): bool
    {
        return $this->hasElement('payment_method_page');
    }

    public function hasRegistrationButton(): bool
    {
        return $this->hasElement('create_account_button');
    }

    public function createAccount(): void
    {
        $this->getElement('create_account_button')->click();
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_thank_you';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'create_account_button' => '[data-test-button="create-an-account"]',
            'instructions' => '[data-test-payment-method-instructions]',
            'order_details_in_account' => '[data-test-button="show-order-in-account"]',
            'payment_method_page' => '[data-test-button="payment-method-page"]',
            'thank_you' => '[data-test-thank-you]',
        ]);
    }
}
