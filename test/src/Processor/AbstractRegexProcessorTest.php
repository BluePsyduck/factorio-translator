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
 * @covers \BluePsyduck\FactorioTranslator\Processor\AbstractRegexProcessor
 */
class AbstractRegexProcessorTest extends TestCase
{
    use ReflectionTrait;

    private string $pattern = 'foo';

    /**
     * @param array<string> $mockedMethods
     * @return AbstractRegexProcessor&MockObject
     */
    private function createInstance(array $mockedMethods = []): AbstractRegexProcessor
    {
        return $this->getMockBuilder(AbstractRegexProcessor::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->pattern,
                    ])
                    ->getMockForAbstractClass();
    }

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

        $instance = $this->createInstance(['findPlaceholders', 'processMatch']);
        $instance->expects($this->once())
                 ->method('findPlaceholders')
                 ->with($this->identicalTo($string))
                 ->willReturn($placeholders);
        $instance->expects($this->exactly(2))
                 ->method('processMatch')
                 ->withConsecutive(
                     [$this->identicalTo($locale), $this->identicalTo(['abc', '42']), $this->identicalTo($parameters)],
                     [$this->identicalTo($locale), $this->identicalTo(['def', '21']), $this->identicalTo($parameters)],
                 )
                 ->willReturnOnConsecutiveCalls(
                     'cba',
                     'fed',
                 );

        $result = $instance->process($locale, $string, $parameters);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testFindPlaceholders(): void
    {
        $string = 'abc42 def21 abc42';
        $expectedResult = [
            'abc42' => ['abc', '42'],
            'def21' => ['def', '21'],
        ];

        $this->pattern = '#([a-z]+)([0-9]+)#';

        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'findPlaceholders', $string);

        $this->assertSame($expectedResult, $result);
    }
}
