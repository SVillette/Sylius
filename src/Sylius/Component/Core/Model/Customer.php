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

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Customer\Model\Customer as BaseCustomer;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;
use Webmozart\Assert\Assert;

class Customer extends BaseCustomer implements CustomerInterface
{
    /** @var Collection<array-key, OrderInterface> */
    protected $orders;

    /** @var AddressInterface|null */
    protected $defaultAddress;

    /** @var Collection<array-key, AddressInterface> */
    protected $addresses;

    /** @var ShopUserInterface|null */
    protected $user;

    public function __construct()
    {
        parent::__construct();
        $this->orders = new ArrayCollection();
        $this->addresses = new ArrayCollection();
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function getDefaultAddress(): ?AddressInterface
    {
        return $this->defaultAddress;
    }

    public function setDefaultAddress(?AddressInterface $defaultAddress): void
    {
        $this->defaultAddress = $defaultAddress;

        if (null !== $defaultAddress) {
            $this->addAddress($defaultAddress);
        }
    }

    public function addAddress(AddressInterface $address): void
    {
        if (!$this->hasAddress($address)) {
            $this->addresses[] = $address;
            $address->setCustomer($this);
        }
    }

    public function removeAddress(AddressInterface $address): void
    {
        $this->addresses->removeElement($address);
        $address->setCustomer(null);
    }

    public function hasAddress(AddressInterface $address): bool
    {
        return $this->addresses->contains($address);
    }

    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function getUser(): ?BaseUserInterface
    {
        return $this->user;
    }

    public function setUser(?BaseUserInterface $user): void
    {
        if ($this->user === $user) {
            return;
        }

        /** @var ShopUserInterface|null $user */
        Assert::nullOrIsInstanceOf($user, ShopUserInterface::class);

        $previousUser = $this->user;
        $this->user = $user;

        if ($previousUser instanceof ShopUserInterface) {
            $previousUser->setCustomer(null);
        }

        if ($user instanceof ShopUserInterface) {
            $user->setCustomer($this);
        }
    }

    public function hasUser(): bool
    {
        return null !== $this->user;
    }
}
