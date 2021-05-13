<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator;

use BluePsyduck\FactorioTranslator\Storage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the Storage class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \BluePsyduck\FactorioTranslator\Storage
 */
class StorageTest extends TestCase
{
    /**
     * @param array<string> $mockedMethods
     * @return Storage&MockObject
     */
    private function createInstance(array $mockedMethods = []): Storage
    {
        return $this->getMockBuilder(Storage::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->getMock();
    }

    public function testSetGetAndHas(): void
    {
        $instance = $this->createInstance();

        $this->assertFalse($instance->has('abc', 'def', 'ghi'));
        $this->assertSame('', $instance->get('abc', 'def', 'ghi'));

        $instance->set('abc', 'def', 'ghi', 'jkl');
        $this->assertTrue($instance->has('abc', 'def', 'ghi'));
        $this->assertSame('jkl', $instance->get('abc', 'def', 'ghi'));

        $instance->set('abc', 'mno', 'ghi', 'pqr');
        $this->assertSame('jkl', $instance->get('abc', 'def', 'ghi'));
        $this->assertSame('pqr', $instance->get('abc', 'mno', 'ghi'));

        $instance->set('abc', 'def', 'ghi', 'stu');
        $this->assertSame('stu', $instance->get('abc', 'def', 'ghi'));
    }

    public function testGetLocales(): void
    {
        $instance = $this->createInstance();

        $instance->set('foo', 'abc', 'def', 'ghi');
        $instance->set('foo', 'jkl', 'mno', 'pqr');
        $instance->set('bar', 'stu', 'vwx', 'yza');

        $result = $instance->getLocales();
        $this->assertSame(['foo', 'bar'], $result);
    }
}
