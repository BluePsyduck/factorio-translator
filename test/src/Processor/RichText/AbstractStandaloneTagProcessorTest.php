<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\RichText;

use BluePsyduck\FactorioTranslator\Processor\RichText\AbstractStandaloneTagProcessor;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the AbstractStandaloneTagProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \BluePsyduck\FactorioTranslator\Processor\RichText\AbstractStandaloneTagProcessor
 */
class AbstractStandaloneTagProcessorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @param array<string> $mockedMethods
     * @return AbstractStandaloneTagProcessor&MockObject
     */
    private function createInstance(array $mockedMethods = []): AbstractStandaloneTagProcessor
    {
        return $this->getMockBuilder(AbstractStandaloneTagProcessor::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->getMockForAbstractClass();
    }

    /**
     * @throws ReflectionException
     */
    public function testProcessMatch(): void
    {
        $locale = 'abc';
        $values = ['item', 'def'];
        $parameters = ['ghi'];
        $expectedName = 'item';
        $expectedValue = 'def';
        $processedValue = 'jkl';

        $instance = $this->createInstance(['processTag']);
        $instance->expects($this->once())
                 ->method('processTag')
                 ->with(
                     $this->identicalTo($locale),
                     $this->identicalTo($expectedName),
                     $this->identicalTo($expectedValue),
                 )
                 ->willReturn($processedValue);

        $result = $this->invokeMethod($instance, 'processMatch', $locale, $values, $parameters);

        $this->assertSame($processedValue, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testProcessMatchWithUnknownName(): void
    {
        $locale = 'abc';
        $values = ['invalid', 'def'];
        $parameters = ['ghi'];

        $instance = $this->createInstance(['processTag']);
        $instance->expects($this->never())
                 ->method('processTag');

        $result = $this->invokeMethod($instance, 'processMatch', $locale, $values, $parameters);

        $this->assertNull($result);
    }
}
