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

namespace Sylius\Bundle\ShippingBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class ShippingMethodRule extends Constraint
{
    public string $invalidType = 'sylius.shipping_method.rule.invalid_type';

    public function validatedBy(): string
    {
        return 'sylius_shipping_method_rule';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
