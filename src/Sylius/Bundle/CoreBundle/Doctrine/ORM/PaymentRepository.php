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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

/**
 * @template T of PaymentInterface
 *
 * @implements PaymentRepositoryInterface<T>
 */
class PaymentRepository extends EntityRepository implements PaymentRepositoryInterface
{
    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state != :state')
            ->setParameter('state', PaymentInterface::STATE_CART)
        ;
    }

    public function findOneByOrderId(mixed $paymentId, mixed $orderId): ?PaymentInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :paymentId')
            ->andWhere('o.order = :orderId')
            ->setParameter('paymentId', $paymentId)
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByOrderToken(mixed $paymentId, string $orderToken): ?PaymentInterface
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.order', 'o')
            ->andWhere('p.id = :paymentId')
            ->andWhere('o.tokenValue = :orderToken')
            ->setParameter('paymentId', $paymentId)
            ->setParameter('orderToken', $orderToken)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByCustomer(mixed $id, CustomerInterface $customer): ?PaymentInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.order', 'ord')
            ->innerJoin('ord.customer', 'customer')
            ->andWhere('o.id = :id')
            ->andWhere('customer = :customer')
            ->setParameter('id', $id)
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByCustomerAndOrderToken(mixed $id, CustomerInterface $customer, string $token): ?PaymentInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.order', 'ord')
            ->innerJoin('ord.customer', 'customer')
            ->andWhere('o.id = :id')
            ->andWhere('customer = :customer')
            ->andWhere('ord.tokenValue = :token')
            ->setParameter('id', $id)
            ->setParameter('customer', $customer)
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countNewByChannel(ChannelInterface $channel): int
    {
        return $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->innerJoin('o.order', 'orders')
            ->andWhere('o.state = :state')
            ->andWhere('orders.channel = :channel')
            ->setParameter('state', PaymentInterface::STATE_NEW)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
