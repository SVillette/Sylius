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

namespace spec\Sylius\Bundle\AttributeBundle\Doctrine\ORM\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AttributeBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber;

final class LoadMetadataSubscriberSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            'product' => [
                'subject' => 'Some\App\Product\Entity\Product',
                'attribute' => [
                    'classes' => [
                        'model' => 'Some\App\Product\Entity\Attribute',
                    ],
                ],
                'attribute_value' => [
                    'classes' => [
                        'model' => 'Some\App\Product\Entity\AttributeValue',
                    ],
                ],
            ],
        ]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(LoadMetadataSubscriber::class);
    }

    function it_is_a_doctrine_event_subscriber(): void
    {
        $this->shouldImplement(EventSubscriber::class);
    }

    function it_subscribes_load_class_metadata_event(): void
    {
        $this->getSubscribedEvents()->shouldReturn(['loadClassMetadata']);
    }

    function it_maps_many_to_one_associations_from_the_attribute_value_model_to_the_subject_model_and_the_attribute_model(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadata $metadata,
        EntityManager $entityManager,
        ClassMetadataFactory $classMetadataFactory,
        ClassMetadata $classMetadata,
    ): void {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $eventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($classMetadataFactory);
        $classMetadata->fieldMappings = [
            'id' => [
                'columnName' => 'id',
            ],
        ];
        $classMetadataFactory->getMetadataFor('Some\App\Product\Entity\Product')->willReturn($classMetadata);
        $classMetadataFactory->getMetadataFor('Some\App\Product\Entity\Attribute')->willReturn($classMetadata);

        $metadata->getName()->willReturn('Some\App\Product\Entity\AttributeValue');
        $metadata->hasAssociation('subject')->willReturn(false);
        $metadata->hasAssociation('attribute')->willReturn(false);

        $subjectMapping = [
            'fieldName' => 'subject',
            'targetEntity' => 'Some\App\Product\Entity\Product',
            'inversedBy' => 'attributes',
            'joinColumns' => [[
                'name' => 'product_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
                'onDelete' => 'CASCADE',
            ]],
        ];

        $attributeMapping = [
            'fieldName' => 'attribute',
            'targetEntity' => 'Some\App\Product\Entity\Attribute',
            'joinColumns' => [[
                'name' => 'attribute_id',
                'referencedColumnName' => 'id',
                'nullable' => false,
            ]],
        ];

        $metadata->mapManyToOne($subjectMapping)->shouldBeCalled();
        $metadata->mapManyToOne($attributeMapping)->shouldBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_does_not_map_relations_for_attribute_value_model_if_the_relations_already_exist(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadata $metadata,
        EntityManager $entityManager,
        ClassMetadataFactory $classMetadataFactory,
    ): void {
        $eventArgs->getClassMetadata()->willReturn($metadata);
        $eventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($classMetadataFactory);

        $metadata->getName()->willReturn('Some\App\Product\Entity\AttributeValue');
        $metadata->hasAssociation('subject')->willReturn(true);
        $metadata->hasAssociation('attribute')->willReturn(true);

        $metadata->mapManyToOne(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_does_not_add_a_many_to_one_mapping_if_the_class_is_not_a_configured_attribute_value_model(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadata $metadata,
        EntityManager $entityManager,
        ClassMetadataFactory $classMetadataFactory,
    ): void {
        $eventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($classMetadataFactory);

        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('KeepMoving\ThisClass\DoesNot\Concern\You');

        $metadata->mapManyToOne(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }

    function it_does_not_add_values_one_to_many_mapping_if_the_class_is_not_a_configured_attribute_model(
        LoadClassMetadataEventArgs $eventArgs,
        ClassMetadata $metadata,
        EntityManager $entityManager,
        ClassMetadataFactory $classMetadataFactory,
    ): void {
        $eventArgs->getEntityManager()->willReturn($entityManager);
        $entityManager->getMetadataFactory()->willReturn($classMetadataFactory);

        $eventArgs->getClassMetadata()->willReturn($metadata);
        $metadata->getName()->willReturn('KeepMoving\ThisClass\DoesNot\Concern\You');

        $metadata->mapOneToMany(Argument::any())->shouldNotBeCalled();

        $this->loadClassMetadata($eventArgs);
    }
}
