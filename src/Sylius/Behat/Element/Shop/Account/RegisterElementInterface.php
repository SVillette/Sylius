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

namespace Sylius\Behat\Element\Shop\Account;

interface RegisterElementInterface
{
    public function register(): void;

    public function specifyEmail(?string $email): void;

    public function getEmail(): string;

    public function specifyFirstName(?string $firstName): void;

    public function specifyLastName(?string $lastName): void;

    public function specifyPassword(string $password): void;

    public function specifyPhoneNumber(string $phoneNumber): void;

    public function verifyPassword(string $password): void;

    public function subscribeToTheNewsletter(): void;

    public function getValidationMessage(string $element, array $parameters = []): string;
}
