<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\Placeholder\PositionPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Translator;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the PositionPlaceholderProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Processor\Placeholder\PositionPlaceholderProcessor
 */
class PositionPlaceholderProcessorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $processor = new PositionPlaceholderProcessor();

        $this->assertGreaterThan(0, strlen($this->extractProperty($processor, 'pattern')));
    }

    /**
     * Tests the processMatch method.
     * @throws ReflectionException
     * @covers ::processMatch
     */
    public function testProcessMatch(): void
    {
        $locale = 'abc';
        $values = ['2'];
        $parameters = ['ghi', 'jkl', 'mno'];
        $expectedLocalisedString = 'jkl';
        $translatedValue = 'pqr';

        $translator = $this->createMock(Translator::class);
        $translator->expects($this->once())
                   ->method('translateWithFallback')
                   ->with($this->identicalTo($locale), $this->identicalTo($expectedLocalisedString))
                   ->willReturn($translatedValue);

        $processor = new PositionPlaceholderProcessor();
        $processor->setTranslator($translator);

        $result = $this->invokeMethod($processor, 'processMatch', $locale, $values, $parameters);

        $this->assertSame($translatedValue, $result);
    }

    /**
     * Tests the processMatch method.
     * @throws ReflectionException
     * @covers ::processMatch
     */
    public function testProcessMatchWithMissingParameter(): void
    {
        $locale = 'abc';
        $values = ['42'];
        $parameters = ['ghi', 'jkl', 'mno'];

        $translator = $this->createMock(Translator::class);
        $translator->expects($this->never())
                   ->method('translateWithFallback');

        $processor = new PositionPlaceholderProcessor();
        $processor->setTranslator($translator);

        $result = $this->invokeMethod($processor, 'processMatch', $locale, $values, $parameters);

        $this->assertNull($result);
    }
}
