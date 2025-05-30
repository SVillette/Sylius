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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Context\Ui\Admin\Helper\SecurePasswordTrait;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Account\ChangeShopUserPassword;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class UserContext implements Context
{
    use SecurePasswordTrait;

    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private UserRepositoryInterface $userRepository,
        private ExampleFactoryInterface $userFactory,
        private ObjectManager $userManager,
        private MessageBusInterface $messageBus,
        private string $passwordResetTokenTtl,
    ) {
    }

    /**
     * @Given there is a user :email identified by :password
     * @Given there was account of :email with password :password
     * @Given there is a user :email
     */
    public function thereIsUserIdentifiedBy(string $email, string $password = 'sylius'): void
    {
        /** @var ShopUserInterface $user */
        $user = $this->userFactory->create(['email' => $email, 'password' => $this->replaceWithSecurePassword($password), 'enabled' => true]);

        $this->sharedStorage->set('user', $user);

        $this->userRepository->add($user);
    }

    /**
     * @Given I registered with previously used :email email and :password password
     * @Given I have already registered :email account
     */
    public function theCustomerCreatedAccountWithPassword(string $email, string $password = 'sylius'): void
    {
        /** @var ShopUserInterface $user */
        $user = $this->userFactory->create(['email' => $email, 'password' => $this->replaceWithSecurePassword($password), 'enabled' => true]);

        $user->setCustomer($this->sharedStorage->get('customer'));
        $this->sharedStorage->set('user', $user);

        $this->userRepository->add($user);
    }

    /**
     * @Given the account of :email was deleted
     * @Given my account :email was deleted
     */
    public function accountWasDeleted(string $email): void
    {
        /** @var ShopUserInterface $user */
        $user = $this->userRepository->findOneByEmail($email);

        $this->sharedStorage->set('customer', $user->getCustomer());

        $this->userRepository->remove($user);
    }

    /**
     * @Given its account was deleted
     */
    public function hisAccountWasDeleted(): void
    {
        $user = $this->sharedStorage->get('user');

        $this->userRepository->remove($user);
        $this->userManager->clear();
    }

    /**
     * @Given /^(this user) is not verified$/
     * @Given /^(I) have not verified my account (?:yet)$/
     */
    public function accountIsNotVerified(UserInterface $user): void
    {
        $user->setVerifiedAt(null);

        $this->userManager->flush();
    }

    /**
     * @Given /^(?:(I) have|(this user) has) already received a verification email$/
     */
    public function iHaveReceivedVerificationEmail(UserInterface $user): void
    {
        $this->prepareUserVerification($user);
    }

    /**
     * @Given a verification email has already been sent to :email
     */
    public function aVerificationEmailHasBeenSentTo(string $email): void
    {
        $user = $this->userRepository->findOneByEmail($email);

        $this->prepareUserVerification($user);
    }

    /**
     * @Given /^(I) have already verified my account$/
     */
    public function iHaveAlreadyVerifiedMyAccount(UserInterface $user): void
    {
        $user->setVerifiedAt(new \DateTime());

        $this->userManager->flush();
    }

    /**
     * @Given /^(?:(I) have|(this user) has) already received a resetting password email$/
     */
    public function iHaveReceivedResettingPasswordEmail(UserInterface $user): void
    {
        $this->prepareUserPasswordResetToken($user);
    }

    private function prepareUserVerification(UserInterface $user): void
    {
        $token = 'marryhadalittlelamb';
        $this->sharedStorage->set('verification_token', $token);

        $user->setEmailVerificationToken($token);

        $this->userManager->flush();
    }

    private function prepareUserPasswordResetToken(UserInterface $user): void
    {
        $token = 'itotallyforgotmypassword';

        $user->setPasswordResetToken($token);
        $user->setPasswordRequestedAt(new \DateTime());

        $this->userManager->flush();
    }

    /**
     * @Given /^(I) waited too long, and the token expired$/
     */
    public function iWaitedTooLongAndTheTokenExpired(UserInterface $user): void
    {
        /** @var \DateTime $passwordRequestedAt */
        $passwordRequestedAt = $user->getPasswordRequestedAt();

        // Subtracting the ttl twice because date operations tend to be wobbly
        // and might result in random fails due to skip years, daylight saving
        // time date changes, etc
        $interval = new \DateInterval($this->passwordResetTokenTtl);
        $passwordRequestedAt->sub($interval);
        $passwordRequestedAt->sub($interval);

        $user->setPasswordRequestedAt($passwordRequestedAt);

        $this->userManager->flush();
    }

    /**
     * @Given /^(I)'ve changed my password from "([^"]+)" to "([^"]+)"$/
     */
    public function iveChangedMyPasswordFromTo(UserInterface $user, string $currentPassword, string $newPassword): void
    {
        $currentPassword = $this->retrieveSecurePassword($currentPassword);

        $changeShopUserPassword = new ChangeShopUserPassword(
            newPassword: $this->replaceWithSecurePassword($newPassword),
            confirmNewPassword: $this->confirmSecurePassword($newPassword),
            currentPassword: $currentPassword,
            shopUserId: $user->getId(),
        );

        $this->messageBus->dispatch($changeShopUserPassword);
    }
}
