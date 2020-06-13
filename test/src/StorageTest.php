<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator;

use BluePsyduck\FactorioTranslator\Storage;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the Storage class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Storage
 */
class StorageTest extends TestCase
{
    /**
     * @covers ::get
     * @covers ::set
     */
    public function testSetAndGet(): void
    {
        $storage = new Storage();

        $this->assertSame('', $storage->get('abc', 'def', 'ghi'));

        $storage->set('abc', 'def', 'ghi', 'jkl');
        $this->assertSame('jkl', $storage->get('abc', 'def', 'ghi'));

        $storage->set('abc', 'mno', 'ghi', 'pqr');
        $this->assertSame('jkl', $storage->get('abc', 'def', 'ghi'));
        $this->assertSame('pqr', $storage->get('abc', 'mno', 'ghi'));

        $storage->set('abc', 'def', 'ghi', 'stu');
        $this->assertSame('stu', $storage->get('abc', 'def', 'ghi'));
    }
}
