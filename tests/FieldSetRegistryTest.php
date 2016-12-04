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

use Rollerworks\Component\Search\Exception\InvalidArgumentException;
use Rollerworks\Component\Search\FieldSetConfiguratorInterface;
use Rollerworks\Component\Search\FieldSetRegistry;

final class FieldSetRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_loads_configurator_lazily()
    {
        $configurator = $this->createMock(FieldSetConfiguratorInterface::class);
        $configurator2 = $this->createMock(FieldSetConfiguratorInterface::class);

        $registry = new FieldSetRegistry(
            [
                'set' => function () use ($configurator) {
                    return $configurator;
                },
                'set2' => function () use ($configurator2) {
                    return $configurator2;
                },
            ]
        );

        self::assertTrue($registry->hasConfigurator('set'));
        self::assertTrue($registry->hasConfigurator('set2'));

        self::assertSame($configurator, $registry->getConfigurator('set'));
        self::assertSame($configurator2, $registry->getConfigurator('set2'));

        // Ensure they still work, after initializing.
        self::assertFalse($registry->hasConfigurator('set3'));
        self::assertTrue($registry->hasConfigurator('set'));

        self::assertSame($configurator, $registry->getConfigurator('set'));
        self::assertSame($configurator2, $registry->getConfigurator('set2'));
    }

    /** @test */
    public function it_loads_configurator_by_fqcn()
    {
        $configurator = $this->createMock(FieldSetConfiguratorInterface::class);
        $configurator2 = $this->createMock(FieldSetConfiguratorInterface::class);

        $registry = new FieldSetRegistry(
            [
                'set' => function () use ($configurator) {
                    return $configurator;
                },
            ]
        );

        $name = get_class($configurator2);

        self::assertTrue($registry->hasConfigurator('set'));
        self::assertTrue($registry->hasConfigurator($name));
        self::assertFalse($registry->hasConfigurator('set2'));

        self::assertSame($configurator, $registry->getConfigurator('set'));
        self::assertSame($name, get_class($registry->getConfigurator($name)));
    }

    /** @test */
    public function it_checks_registered_before_className()
    {
        $configurator = $this->createMock(FieldSetConfiguratorInterface::class);
        $configurator2 = $this->createMock(FieldSetConfiguratorInterface::class);
        $name = get_class($configurator2);

        $registry = new FieldSetRegistry(
            [
                'set' => function () use ($configurator) {
                    return $configurator;
                },
                $name => function () use ($configurator2) {
                    return $configurator2;
                },
            ]
        );

        $name = get_class($configurator2);

        self::assertTrue($registry->hasConfigurator('set'));
        self::assertTrue($registry->hasConfigurator($name));
        self::assertFalse($registry->hasConfigurator('set2'));

        self::assertSame($configurator, $registry->getConfigurator('set'));
        self::assertSame($configurator2, $registry->getConfigurator($name));
    }

    /** @test */
    public function it_errors_when_configurator_is_not_registered_and_class_is_a_configurator()
    {
        $configurator = $this->createMock(FieldSetConfiguratorInterface::class);
        $configurator2 = \stdClass::class;

        $registry = new FieldSetRegistry(
            [
                'set' => function () use ($configurator) {
                    return $configurator;
                },
            ]
        );

        self::assertTrue($registry->hasConfigurator('set'));
        self::assertFalse($registry->hasConfigurator('set2'));
        self::assertFalse($registry->hasConfigurator($configurator2));

        self::assertSame($configurator, $registry->getConfigurator('set'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not load FieldSet configurator "stdClass"');

        $registry->getConfigurator($configurator2);
    }

    /** @test */
    public function it_errors_when_configurator_is_not_registered_class_does_not_exist()
    {
        $configurator = $this->createMock(FieldSetConfiguratorInterface::class);
        $configurator2 = 'f4394832948_foobar_cow';

        $registry = new FieldSetRegistry(
            [
                'set' => function () use ($configurator) {
                    return $configurator;
                },
            ]
        );

        self::assertTrue($registry->hasConfigurator('set'));
        self::assertFalse($registry->hasConfigurator('set2'));
        self::assertFalse($registry->hasConfigurator($configurator2));

        self::assertSame($configurator, $registry->getConfigurator('set'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Could not load FieldSet configurator "f4394832948_foobar_cow"');

        $registry->getConfigurator($configurator2);
    }
}