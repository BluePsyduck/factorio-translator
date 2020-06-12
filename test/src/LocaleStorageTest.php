<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator;

use BluePsyduck\FactorioTranslator\LocaleStorage;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the LocaleStorage class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\LocaleStorage
 */
class LocaleStorageTest extends TestCase
{
    /**
     * Tests the  method.
     * @covers ::get
     * @covers ::set
     */
    public function testSetAndGet(): void
    {
        $storage = new LocaleStorage();

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
