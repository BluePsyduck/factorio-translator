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
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Processor\Placeholder\AbstractControlPlaceholderProcessor
 */
class AbstractControlPlaceholderProcessorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $processor = $this->getMockBuilder(AbstractControlPlaceholderProcessor::class)
                          ->getMockForAbstractClass();

        $this->assertGreaterThan(0, strlen($this->extractProperty($processor, 'pattern')));
    }

    /**
     * @throws ReflectionException
     * @covers ::processMatch
     */
    public function testProcessMatch(): void
    {
        $locale = 'abc';
        $values = ['CONTROL', '42', 'def'];
        $parameters = ['ghi'];
        $processedValue = 'jkl';

        /* @var AbstractControlPlaceholderProcessor&MockObject $processor */
        $processor = $this->getMockBuilder(AbstractControlPlaceholderProcessor::class)
                          ->onlyMethods(['processControl'])
                          ->getMockForAbstractClass();
        $processor->expects($this->once())
                  ->method('processControl')
                  ->with($this->identicalTo($locale), $this->identicalTo('def'), $this->identicalTo(42))
                  ->willReturn($processedValue);

        $result = $this->invokeMethod($processor, 'processMatch', $locale, $values, $parameters);

        $this->assertSame($processedValue, $result);
    }
}
