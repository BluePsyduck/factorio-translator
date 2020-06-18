<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\Placeholder\ItemPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Translator;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the ItemPlaceholderProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Processor\Placeholder\ItemPlaceholderProcessor
 */
class ItemPlaceholderProcessorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $processor = new ItemPlaceholderProcessor();

        $this->assertGreaterThan(0, strlen($this->extractProperty($processor, 'pattern')));
    }

    /**
     * @throws ReflectionException
     * @covers ::processMatch
     */
    public function testProcessMatch(): void
    {
        $locale = 'abc';
        $values = ['def'];
        $parameters = ['ghi'];
        $expectedLocalisedString = ['item-name.def'];
        $translatedValue = 'jkl';

        $translator = $this->createMock(Translator::class);
        $translator->expects($this->once())
                   ->method('translateWithFallback')
                   ->with($this->identicalTo($locale), $this->identicalTo($expectedLocalisedString))
                   ->willReturn($translatedValue);

        $processor = new ItemPlaceholderProcessor();
        $processor->setTranslator($translator);

        $result = $this->invokeMethod($processor, 'processMatch', $locale, $values, $parameters);

        $this->assertSame($translatedValue, $result);
    }
}
