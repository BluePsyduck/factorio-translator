<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor;

use BluePsyduck\FactorioTranslator\Processor\AbstractRegexProcessor;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the AbstractRegexProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Processor\AbstractRegexProcessor
 */
class AbstractRegexProcessorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $pattern = 'abc';

        /* @var AbstractRegexProcessor&MockObject $processor */
        $processor = $this->getMockBuilder(AbstractRegexProcessor::class)
                          ->setConstructorArgs([$pattern])
                          ->getMockForAbstractClass();

        $this->assertSame($pattern, $this->extractProperty($processor, 'pattern'));
    }

    /**
     * Tests the process method.
     * @covers ::process
     */
    public function testProcess(): void
    {
        $locale = 'foo';
        $parameters = ['bar', 'baz'];
        $string = 'w abc42 x def21 y abc42 z';
        $placeholders = [
            'abc42' => ['abc', '42'],
            'def21' => ['def', '21'],
        ];
        $expectedResult = 'w cba x fed y cba z';

        /* @var AbstractRegexProcessor&MockObject $processor */
        $processor = $this->getMockBuilder(AbstractRegexProcessor::class)
                          ->onlyMethods(['findPlaceholders', 'processMatch'])
                          ->setConstructorArgs(['#.*#'])
                          ->getMockForAbstractClass();
        $processor->expects($this->once())
                  ->method('findPlaceholders')
                  ->with($this->identicalTo($string))
                  ->willReturn($placeholders);
        $processor->expects($this->exactly(2))
                  ->method('processMatch')
                  ->withConsecutive(
                      [$this->identicalTo($locale), $this->identicalTo(['abc', '42']), $this->identicalTo($parameters)],
                      [$this->identicalTo($locale), $this->identicalTo(['def', '21']), $this->identicalTo($parameters)],
                  )
                  ->willReturnOnConsecutiveCalls(
                      'cba',
                      'fed',
                  );

        $result = $processor->process($locale, $string, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the findPlaceholders method.
     * @throws ReflectionException
     * @covers ::findPlaceholders
     */
    public function testFindPlaceholders(): void
    {
        $pattern = '#([a-z]+)([0-9]+)#';
        $string = 'abc42 def21 abc42';
        $expectedResult = [
            'abc42' => ['abc', '42'],
            'def21' => ['def', '21'],
        ];

        /* @var AbstractRegexProcessor&MockObject $processor */
        $processor = $this->getMockBuilder(AbstractRegexProcessor::class)
                          ->setConstructorArgs([$pattern])
                          ->getMockForAbstractClass();
        $result = $this->invokeMethod($processor, 'findPlaceholders', $string);

        $this->assertSame($expectedResult, $result);
    }
}
