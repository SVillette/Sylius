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

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Behat\Step\When;
use Sylius\Behat\Element\Shop\Account\RegisterElementInterface;
use Sylius\Behat\Page\Shop\Account\DashboardPageInterface;
use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\Account\RegisterThankYouPageInterface;
use Sylius\Behat\Page\Shop\Account\VerificationPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Webmozart\Assert\Assert;

final class RegistrationAfterCheckoutContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private LoginPageInterface $loginPage,
        private ThankYouPageInterface $thankYouPage,
        private HomePageInterface $homePage,
        private VerificationPageInterface $verificationPage,
        private RegisterThankYouPageInterface $registerThankYouPage,
        private DashboardPageInterface $dashboardPage,
        private RegisterElementInterface $registerElement,
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    #[When('I specify a password as :password')]
    public function iSpecifyThePasswordAs(string $password): void
    {
        $this->registerElement->specifyPassword($password);
    }

    /**
     * @When /^I confirm (this password)$/
     */
    public function iConfirmThisPassword(string $password): void
    {
        $this->registerElement->verifyPassword($password);
    }

    /**
     * @When I register this account
     */
    public function iRegisterThisAccount(): void
    {
        $this->registerElement->register();
    }

    /**
     * @When I verify my account using link sent to :customer
     */
    public function iVerifyMyAccountUsingLink(CustomerInterface $customer): void
    {
        $user = $customer->getUser();
        Assert::notNull($user, 'No account for given customer');

        $this->verificationPage->verifyAccount($user->getEmailVerificationToken());
    }

    /**
     * @Then the registration form should be prefilled with :email email
     */
    public function theRegistrationFormShouldBePrefilledWithEmail(string $email): void
    {
        $this->thankYouPage->createAccount();

        Assert::same($this->registerElement->getEmail(), $email);
    }

    /**
     * @Then I should be able to log in as :email with :password password
     */
    public function iShouldBeAbleToLogInAsWithPassword(string $email, string $password): void
    {
        $this->loginPage->open();
        $this->loginPage->specifyUsername($email);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();

        Assert::true($this->homePage->hasLogoutButton());
    }

    /**
     * @Then I should be on registration thank you page
     */
    public function iShouldBeOnRegistrationThankYouPage(): void
    {
        $registeredCustomer = $this->customerRepository->findLatest(1)[0];
        Assert::true($this->registerThankYouPage->isOpen(['id' => $registeredCustomer->getId()]));
    }

    /**
     * @Then I should be on my account dashboard
     */
    public function iShouldBeOnMyAccountDashboard(): void
    {
        Assert::true($this->dashboardPage->isOpen());
    }
}
