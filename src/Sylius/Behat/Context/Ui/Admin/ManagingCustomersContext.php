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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Step\When;
use Sylius\Behat\Element\Admin\Customer\FormElementInterface;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Page\Admin\Customer\IndexPageInterface as CustomerIndexPageInterface;
use Sylius\Behat\Page\Admin\Customer\ShowPageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

final class ManagingCustomersContext implements Context
{
    /**
     * @param CustomerIndexPageInterface $indexPage
     */
    public function __construct(
        private CreatePageInterface $createPage,
        private IndexPageInterface $indexPage,
        private UpdatePageInterface $updatePage,
        private ShowPageInterface $showPage,
        private IndexPageInterface $ordersIndexPage,
        private CurrentPageResolverInterface $currentPageResolver,
        private FormElementInterface $formElement,
    ) {
    }

    /**
     * @When I want to create a new customer
     * @When I want to create a new customer account
     */
    public function iWantToCreateANewCustomer()
    {
        $this->createPage->open();
    }

    /**
     * @When /^I specify (?:their|his) first name as "([^"]*)"$/
     */
    public function iSpecifyItsFirstNameAs($name): void
    {
        $this->formElement->specifyFirstName($name);
    }

    /**
     * @When /^I specify (?:their|his) last name as "([^"]*)"$/
     */
    public function iSpecifyItsLastNameAs($name)
    {
        $this->formElement->specifyLastName($name);
    }

    /**
     * @When I specify their email as :name
     * @When I do not specify their email
     */
    public function iSpecifyItsEmailAs($email = null)
    {
        $this->formElement->specifyEmail($email ?? '');
    }

    /**
     * @When I change their email to :email
     * @When I remove its email
     */
    public function iChangeTheirEmailTo($email = null): void
    {
        $this->formElement->specifyEmail($email ?? '');
    }

    /**
     * @When I add them
     * @When I try to add them
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When I filter by group :groupName
     * @When I filter by groups :firstGroup and :secondGroup
     */
    public function iFilterByGroup(string ...$groupsNames): void
    {
        foreach ($groupsNames as $groupName) {
            $this->indexPage->setFilterGroup($groupName);
        }

        $this->indexPage->filter();
    }

    /**
     * @Then the customer :customer should appear in the store
     * @Then the customer :customer should still have this email
     */
    public function theCustomerShould(CustomerInterface $customer)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $customer->getEmail()]));
    }

    /**
     * @When I select :gender as its gender
     */
    public function iSelectGender($gender)
    {
        $this->formElement->chooseGender($gender);
    }

    /**
     * @When I select :group as their group
     */
    public function iSelectGroup($group)
    {
        $this->formElement->chooseGroup($group);
    }

    /**
     * @When I specify its birthday as :birthday
     */
    public function iSpecifyItsBirthdayAs($birthday)
    {
        $this->formElement->specifyBirthday($birthday);
    }

    /**
     * @When /^I want to edit (this customer)$/
     */
    public function iWantToEditThisCustomer(CustomerInterface $customer)
    {
        $this->updatePage->open(['id' => $customer->getId()]);
    }

    /**
     * @When I verify it
     */
    public function iTryToVerifyIt(): void
    {
        $this->formElement->verifyUser();
    }

    /**
     * @Then /^(this customer) should be verified$/
     */
    public function thisCustomerShouldBeVerified(CustomerInterface $customer): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isCustomerVerified($customer));
    }

    /**
     * @Then /^(this customer) with name "([^"]*)" should appear in the store$/
     */
    public function theCustomerWithNameShouldAppearInTheRegistry(CustomerInterface $customer, $name)
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::same($this->formElement->getFullName(), $name);
    }

    /**
     * @When I want to see all customers in store
     */
    public function iWantToSeeAllCustomersInStore()
    {
        $this->indexPage->open();
    }

    /**
     * @When /^I sort customers by (ascending|descending) registration date$/
     */
    public function iSortCustomersByRegistrationDate(string $order): void
    {
        $this->sortBy($order, 'createdAt');
    }

    /**
     * @When /^I sort customers by (ascending|descending) (email|first name|last name)$/
     */
    public function iSortCustomersByField(string $order, string $field): void
    {
        $this->sortBy($order, StringInflector::nameToCamelCase($field));
    }

    /**
     * @Then /^I should see (\d+) customers (?:in|on) the list$/
     * @Then /^I should see a single customer on the list$/
     */
    public function iShouldSeeCustomersInTheList($amountOfCustomers = 1)
    {
        Assert::same($this->indexPage->countItems(), (int) $amountOfCustomers);
    }

    /**
     * @Then I should see the customer :email in the list
     * @Then I should see the customer :email on the list
     */
    public function iShouldSeeTheCustomerInTheList($email)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @Then /^the (first|last) customer should be "([^"]+)"$/
     */
    public function theFirstLastCustomerShouldBe(string $placement, string $email): void
    {
        $index = 'first' === $placement ? 0 : $this->indexPage->countItems() - 1;

        Assert::same(
            $this->indexPage->getColumnFields('email')[$index],
            $email,
        );
    }

    /**
     * @Then /^I should be notified that ([^"]+) is required$/
     */
    public function iShouldBeNotifiedThatFirstNameIsRequired(string $elementName): void
    {
        Assert::same(
            $this->formElement->getValidationMessage(StringInflector::nameToLowercaseCode($elementName)),
            sprintf('Please enter your %s.', $elementName),
        );
    }

    /**
     * @Then /^I should be notified that ([^"]+) should be ([^"]+)$/
     */
    public function iShouldBeNotifiedThatTheElementShouldBe($elementName, $validationMessage)
    {
        Assert::same(
            $this->formElement->getValidationMessage(StringInflector::nameToLowercaseCode($elementName)),
            sprintf('%s must be %s.', ucfirst($elementName), $validationMessage),
        );
    }

    /**
     * @Then the customer with email :email should not appear in the store
     */
    public function theCustomerShouldNotAppearInTheStore($email)
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @When I remove its first name
     */
    public function iRemoveItsFirstName()
    {
        $this->formElement->specifyFirstName('');
    }

    /**
     * @Then /^(this customer) should have an empty first name$/
     * @Then the customer :customer should still have an empty first name
     */
    public function theCustomerShouldStillHaveAnEmptyFirstName(CustomerInterface $customer)
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::eq($this->formElement->getFirstName(), '');
    }

    /**
     * @When I remove its last name
     */
    public function iRemoveItsLastName()
    {
        $this->formElement->specifyLastName('');
    }

    /**
     * @Then /^(this customer) should have an empty last name$/
     * @Then the customer :customer should still have an empty last name
     */
    public function theCustomerShouldStillHaveAnEmptyLastName(CustomerInterface $customer)
    {
        $this->updatePage->open(['id' => $customer->getId()]);

        Assert::eq($this->formElement->getLastName(), '');
    }

    /**
     * @Then I should be notified that email is not valid
     */
    public function iShouldBeNotifiedThatEmailIsNotValid()
    {
        Assert::same($this->formElement->getValidationMessage('email'), 'This email is invalid.');
    }

    /**
     * @Then I should be notified that email must be unique
     */
    public function iShouldBeNotifiedThatEmailMustBeUnique()
    {
        Assert::same($this->formElement->getValidationMessage('email'), 'This email is already used.');
    }

    /**
     * @Then there should still be only one customer with email :email
     */
    public function thereShouldStillBeOnlyOneCustomerWithEmail($email)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['email' => $email]));
    }

    /**
     * @When I want to enable :customer
     * @When I want to disable :customer
     * @When I want to verify :customer
     */
    public function iWantToChangeStatusOf(CustomerInterface $customer): void
    {
        $this->updatePage->open(['id' => $customer->getId()]);
    }

    /**
     * @When I enable their account
     */
    public function iEnableIt()
    {
        $this->formElement->enable();
    }

    /**
     * @When I disable their account
     */
    public function iDisableIt()
    {
        $this->formElement->disable();
    }

    /**
     * @Then /^(this customer) should be enabled$/
     */
    public function thisCustomerShouldBeEnabled(CustomerInterface $customer): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isCustomerEnabled($customer), true);
    }

    /**
     * @Then /^(this customer) should be disabled$/
     */
    public function thisCustomerShouldBeDisabled(CustomerInterface $customer): void
    {
        $this->indexPage->open();

        Assert::eq($this->indexPage->isCustomerEnabled($customer), false);
    }

    #[When('I specify their password as :password')]
    public function iSpecifyItsPasswordAs($password): void
    {
        $this->formElement->specifyPassword($password);
    }

    /**
     * @When I browse orders of a customer :customer
     */
    public function iBrowseOrdersOfACustomer(CustomerInterface $customer): void
    {
        $this->ordersIndexPage->open(['id' => $customer->getId()]);
    }

    /**
     * @When I sort the orders :sortType by :field
     */
    public function iSortTheOrderByField(string $field): void
    {
        $this->ordersIndexPage->sort(ucfirst($field));
    }

    /**
     * @Then the customer :customer should have an account created
     * @Then /^(this customer) should have an account created$/
     */
    public function theyShouldHaveAnAccountCreated(CustomerInterface $customer): void
    {
        Assert::notNull(
            $customer->getUser()->getPassword(),
            'Customer should have an account, but they do not.',
        );
    }

    /**
     * @When I view details of the customer :customer
     * @When /^I view (their) details$/
     */
    public function iViewDetailsOfTheCustomer(CustomerInterface $customer)
    {
        $this->showPage->open(['id' => $customer->getId()]);
    }

    /**
     * @Then /^(?:their|his) name should be "([^"]+)"$/
     */
    public function hisNameShouldBe(string $name): void
    {
        Assert::same($this->showPage->getCustomerName(), $name);
    }

    /**
     * @Then he should be registered since :registrationDate
     */
    public function hisRegistrationDateShouldBe($registrationDate)
    {
        Assert::eq($this->showPage->getRegistrationDate(), new \DateTime($registrationDate));
    }

    /**
     * @Then /^(?:their|his) email should be "([^"]+)"$/
     */
    public function hisEmailShouldBe(string $email): void
    {
        Assert::same($this->showPage->getCustomerEmail(), $email);
    }

    /**
     * @Then /^(?:their|his) phone number should be "([^"]+)"$/
     */
    public function hisPhoneNumberShouldBe(string $phoneNumber): void
    {
        Assert::same($this->showPage->getCustomerPhoneNumber(), $phoneNumber);
    }

    /**
     * @Then their default address should be :firstName :lastName, :street, :postcode :city, :country
     */
    public function theirSDefaultAddressShouldBe(
        string $firstName,
        string $lastName,
        string $street,
        string $postcode,
        string $city,
        string $country,
    ): void {
        Assert::same(
            $this->showPage->getDefaultAddress(),
            sprintf('%s %s, %s, %s %s, %s', $firstName, $lastName, $street, $postcode, $city, ucwords($country)),
        );
    }

    /**
     * @Then I should see information about no existing account for this customer
     */
    public function iShouldSeeInformationAboutNoExistingAccountForThisCustomer()
    {
        Assert::false($this->showPage->hasAccount());
    }

    /**
     * @Then I should see that this customer is subscribed to the newsletter
     */
    public function iShouldSeeThatThisCustomerIsSubscribedToTheNewsletter()
    {
        Assert::true($this->showPage->isSubscribedToNewsletter());
    }

    /**
     * @Then I should not see information about email verification
     */
    public function iShouldSeeInformationAboutEmailVerification()
    {
        Assert::true($this->showPage->hasEmailVerificationInformation());
    }

    /**
     * @When I make them subscribed to the newsletter
     */
    public function iMakeThemSubscribedToTheNewsletter()
    {
        $this->formElement->subscribeToTheNewsletter();
    }

    /**
     * @When I change the password of user :customer to :newPassword
     */
    public function iChangeThePasswordOfUserTo(CustomerInterface $customer, $newPassword)
    {
        $this->updatePage->open(['id' => $customer->getId()]);
        $this->formElement->specifyPassword($newPassword);
        $this->updatePage->saveChanges();
    }

    /**
     * @Then this customer should be subscribed to the newsletter
     */
    public function thisCustomerShouldBeSubscribedToTheNewsletter()
    {
        Assert::true($this->formElement->isSubscribedToTheNewsletter());
    }

    /**
     * @Then the province in the default address should be :provinceName
     */
    public function theProvinceInTheDefaultAddressShouldBe($provinceName)
    {
        Assert::true($this->showPage->hasDefaultAddressProvinceName($provinceName));
    }

    /**
     * @Then this customer should have :groupName as their group
     */
    public function thisCustomerShouldHaveAsTheirGroup($groupName)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->updatePage, $this->showPage]);

        if ($currentPage instanceof ShowPageInterface) {
            Assert::same($currentPage->getGroupName(), $groupName);
        } else {
            Assert::same($this->formElement->getGroupName(), $groupName);
        }
    }

    /**
     * @Then I should see that this customer has verified the email
     */
    public function iShouldSeeThatThisCustomerHasVerifiedTheEmail()
    {
        Assert::true($this->showPage->hasVerifiedEmail());
    }

    /**
     * @Then I should see a single order in the list
     */
    public function iShouldSeeASingleOrderInTheList()
    {
        Assert::same($this->ordersIndexPage->countItems(), 1);
    }

    /**
     * @Then I should see the order with number :orderNumber in the list
     */
    public function iShouldSeeASingleOrderFromCustomer($orderNumber)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @Then I should not see the order with number :orderNumber in the list
     */
    public function iShouldNotSeeASingleOrderFromCustomer($orderNumber)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['number' => $orderNumber]));
    }

    /**
     * @When I do not specify any information
     */
    public function iDoNotSpecifyAnyInformation()
    {
        // Intentionally left blank.
    }

    /**
     * @Then I should still be on the customer creation page
     */
    public function iShouldBeOnTheCustomerCreationPage()
    {
        $this->createPage->verify();
    }

    /**
     * @When I do not choose create account option
     */
    public function iDoNotChooseCreateAccountOption()
    {
        // Intentionally left blank.
    }

    /**
     * @Then /^I should be notified that the password must be at least (\d+) characters long$/
     */
    public function iShouldBeNotifiedThatThePasswordMustBeAtLeastCharactersLong($amountOfCharacters)
    {
        Assert::same(
            $this->formElement->getValidationMessage('password'),
            sprintf('Password must be at least %d characters long.', $amountOfCharacters),
        );
    }

    /**
     * @Then I should see the customer has not placed any orders yet
     */
    public function iShouldSeeTheCustomerHasNotYetPlacedAnyOrders()
    {
        Assert::false($this->showPage->hasCustomerPlacedAnyOrders());
    }

    /**
     * @Then /^I should see that they have placed (\d+) orders? in the ("[^"]+" channel)$/
     */
    public function iShouldSeeThatTheyHavePlacedOrdersInTheChannel($ordersCount, ChannelInterface $channel)
    {
        Assert::same($this->showPage->getOrdersCountInChannel($channel->getCode()), (int) $ordersCount);
    }

    /**
     * @Then /^I should see that the overall total value of all their orders in the ("[^"]+" channel) is "([^"]+)"$/
     */
    public function iShouldSeeThatTheOverallTotalValueOfAllTheirOrdersInTheChannelIs(ChannelInterface $channel, $ordersValue)
    {
        Assert::same($this->showPage->getOrdersTotalInChannel($channel->getCode()), $ordersValue);
    }

    /**
     * @Then /^I should see that the average total value of their order in the ("[^"]+" channel) is "([^"]+)"$/
     */
    public function iShouldSeeThatTheAverageTotalValueOfTheirOrderInTheChannelIs(ChannelInterface $channel, $ordersValue)
    {
        Assert::same($this->showPage->getAverageTotalInChannel($channel->getCode()), $ordersValue);
    }

    private function sortBy(string $order, string $field): void
    {
        $this->indexPage->sortBy($field, str_starts_with($order, 'de') ? 'desc' : 'asc');
    }
}
