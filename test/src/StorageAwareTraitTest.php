<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator;

use BluePsyduck\FactorioTranslator\Storage;
use BluePsyduck\FactorioTranslator\StorageAwareTrait;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the StorageAwareTrait class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\StorageAwareTrait
 */
class StorageAwareTraitTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the setStorage method.
     * @throws ReflectionException
     * @covers ::setStorage
     */
    public function testSetStorage(): void
    {
        $storage = $this->createMock(Storage::class);

        /* @var StorageAwareTrait&MockObject $trait */
        $trait = $this->getMockBuilder(StorageAwareTrait::class)
                      ->getMockForTrait();

        $trait->setStorage($storage);

        $this->assertSame($storage, $this->extractProperty($trait, 'storage'));
    }
}
