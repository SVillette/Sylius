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

namespace Sylius\Behat\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class ApiSecurityService implements SecurityServiceInterface
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private JWTTokenManagerInterface $jwtTokenManager,
        private string $firewallName,
    ) {
    }

    public function logIn(UserInterface $user): void
    {
        $this->sharedStorage->set('token', $this->jwtTokenManager->create($user));
        $this->sharedStorage->set('user', $user);
    }

    public function logOut(): void
    {
        $this->sharedStorage->set('token', null);
        $this->sharedStorage->set('user', null);
    }

    public function getCurrentToken(): TokenInterface
    {
        /** @var UserInterface $user */
        $user = $this->sharedStorage->get('user');
        /** @var string $token */
        $token = $this->sharedStorage->get('token');

        return new JWTPostAuthenticationToken($user, $this->firewallName, $user->getRoles(), $token);
    }

    public function restoreToken(TokenInterface $token): void
    {
        $this->sharedStorage->set('token', (string) $token);
    }
}
