<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\Placeholder\AbstractControlPlaceholderProcessor;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the AbstractControlPlaceholderProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \BluePsyduck\FactorioTranslator\Processor\Placeholder\AbstractControlPlaceholderProcessor
 */
class AbstractControlPlaceholderProcessorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @param array<string> $mockedMethods
     * @return AbstractControlPlaceholderProcessor&MockObject
     */
    private function createInstance(array $mockedMethods = []): AbstractControlPlaceholderProcessor
    {
        return $this->getMockBuilder(AbstractControlPlaceholderProcessor::class)
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
        $values = ['CONTROL', '42', 'def'];
        $parameters = ['ghi'];
        $processedValue = 'jkl';

        $instance = $this->createInstance(['processControl']);
        $instance->expects($this->once())
                 ->method('processControl')
                 ->with($this->identicalTo($locale), $this->identicalTo('def'), $this->identicalTo(42))
                 ->willReturn($processedValue);

        $result = $this->invokeMethod($instance, 'processMatch', $locale, $values, $parameters);

        $this->assertSame($processedValue, $result);
    }
}
