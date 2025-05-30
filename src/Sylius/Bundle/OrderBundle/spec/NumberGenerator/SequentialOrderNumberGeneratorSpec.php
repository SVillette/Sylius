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

namespace spec\Sylius\Bundle\OrderBundle\NumberGenerator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderSequenceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class SequentialOrderNumberGeneratorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $sequenceRepository, FactoryInterface $sequenceFactory): void
    {
        $this->beConstructedWith($sequenceRepository, $sequenceFactory);
    }

    function it_implements_an_order_number_generator_interface(): void
    {
        $this->shouldImplement(OrderNumberGeneratorInterface::class);
    }

    function it_generates_an_order_number(
        RepositoryInterface $sequenceRepository,
        OrderSequenceInterface $sequence,
        OrderInterface $order,
    ): void {
        $sequence->getIndex()->willReturn(6);
        $sequenceRepository->findOneBy([])->willReturn($sequence);

        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate($order)->shouldReturn('000000007');
    }

    function it_generates_an_order_number_when_sequence_is_null(
        RepositoryInterface $sequenceRepository,
        FactoryInterface $sequenceFactory,
        OrderSequenceInterface $sequence,
        OrderInterface $order,
    ): void {
        $sequence->getIndex()->willReturn(0);

        $sequenceRepository->findOneBy([])->willReturn(null);

        $sequenceFactory->createNew()->willReturn($sequence);
        $sequenceRepository->add($sequence)->shouldBeCalled();

        $sequence->incrementIndex()->shouldBeCalled();

        $this->generate($order)->shouldReturn('000000001');
    }
}
