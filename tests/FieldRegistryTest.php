<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests;

use Prophecy\Argument;
use Rollerworks\Component\Search\FieldRegistry;
use Rollerworks\Component\Search\FieldTypeExtensionInterface;
use Rollerworks\Component\Search\FieldTypeInterface;
use Rollerworks\Component\Search\PreloadedExtension;
use Rollerworks\Component\Search\ResolvedFieldTypeFactory;
use Rollerworks\Component\Search\ResolvedFieldTypeInterface;
use Rollerworks\Component\Search\Tests\Fixtures\BarType;
use Rollerworks\Component\Search\Tests\Fixtures\FooSubType;
use Rollerworks\Component\Search\Tests\Fixtures\FooType;

final class FieldRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_loads_types_from_extensions()
    {
        $extension = new PreloadedExtension([FooType::class => $fooType = new FooType()]);
        $extension2 = new PreloadedExtension([FooSubType::class => $fooSubType = new FooSubType()]);
        $barType = new BarType();

        $resolvedFieldTypeFactory = $this->prophesize(ResolvedFieldTypeFactory::class);
        $resolvedFieldTypeFactory->createResolvedType(Argument::type(FooType::class), [], null)->willReturn($resolvedFooType = $this->createResolvedTypeMock($fooType));
        $resolvedFieldTypeFactory->createResolvedType(Argument::type(FooSubType::class), [], $resolvedFooType)->willReturn($this->createResolvedTypeMock($fooSubType));
        $resolvedFieldTypeFactory->createResolvedType(Argument::type(BarType::class), [], null)->willReturn($this->createResolvedTypeMock($barType));

        $registry = new FieldRegistry([$extension, $extension2], $resolvedFieldTypeFactory->reveal());

        $this->assertTrue($registry->hasType(FooType::class));
        $this->assertTrue($registry->hasType(FooType::class)); // once the type is loaded it's cached internally
        $this->assertTrue($registry->hasType(FooSubType::class));
        $this->assertTrue($registry->hasType(BarType::class)); // auto loaded by FQCN
        $this->assertFalse($registry->hasType('text'));

        $this->assertInstanceOf(ResolvedFieldTypeInterface::class, $registry->getType(FooType::class));
        $this->assertInstanceOf(ResolvedFieldTypeInterface::class, $registry->getType(FooSubType::class));
        $this->assertInstanceOf(ResolvedFieldTypeInterface::class, $registry->getType(BarType::class));
    }

    /**
     * @test
     */
    public function it_loads_type_extensions()
    {
        $extension = new PreloadedExtension([FooType::class => $fooType = new FooType()]);
        $extension2 = new PreloadedExtension(
            [
                FooSubType::class => $fooSubType = new FooSubType(),
            ],
            [
                BarType::class => [$barTypeExtension = $this->createTypeExtensionMock(BarType::class)],
                FooSubType::class => [$fooSubTypeExtension = $this->createTypeExtensionMock(FooSubType::class)],
            ]
        );

        $barType = new BarType();

        $resolvedFieldTypeFactory = $this->prophesize(ResolvedFieldTypeFactory::class);
        $resolvedFieldTypeFactory->createResolvedType(Argument::type(FooType::class), [], null)->willReturn($resolvedFooType = $this->createResolvedTypeMock($fooType));
        $resolvedFieldTypeFactory->createResolvedType(Argument::type(FooSubType::class), [$fooSubTypeExtension], $resolvedFooType)->willReturn($this->createResolvedTypeMock($fooSubType));
        $resolvedFieldTypeFactory->createResolvedType(Argument::type(BarType::class), [$barTypeExtension], null)->willReturn($this->createResolvedTypeMock($barType));

        $registry = new FieldRegistry([$extension, $extension2], $resolvedFieldTypeFactory->reveal());

        $this->assertTrue($registry->hasType(FooType::class));
        $this->assertTrue($registry->hasType(FooSubType::class));
        $this->assertTrue($registry->hasType(BarType::class)); // auto loaded by FQCN
        $this->assertFalse($registry->hasType('text'));

        $this->assertInstanceOf(ResolvedFieldTypeInterface::class, $registry->getType(FooType::class));
        $this->assertInstanceOf(ResolvedFieldTypeInterface::class, $registry->getType(FooSubType::class));
        $this->assertInstanceOf(ResolvedFieldTypeInterface::class, $registry->getType(BarType::class));
    }

    private function createResolvedTypeMock(FieldTypeInterface $type): ResolvedFieldTypeInterface
    {
        $resolvedType = $this->createMock(ResolvedFieldTypeInterface::class);
        $resolvedType->expects($this->any())->method('getInnerType')->willReturn($type);

        return $resolvedType;
    }

    private function createTypeExtensionMock(string $name)
    {
        $fieldExtension = $this->createMock(FieldTypeExtensionInterface::class);
        $fieldExtension->expects($this->any())->method('getExtendedType')->willReturn($name);

        return $fieldExtension;
    }
}