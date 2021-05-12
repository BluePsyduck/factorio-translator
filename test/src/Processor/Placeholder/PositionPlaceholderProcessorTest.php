<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\Placeholder\PositionPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Translator;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the PositionPlaceholderProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \BluePsyduck\FactorioTranslator\Processor\Placeholder\PositionPlaceholderProcessor
 */
class PositionPlaceholderProcessorTest extends TestCase
{
    use ReflectionTrait;

    /** @var Translator&MockObject */
    private Translator $translator;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(Translator::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return PositionPlaceholderProcessor&MockObject
     */
    private function createInstance(array $mockedMethods = []): PositionPlaceholderProcessor
    {
        $instance = $this->getMockBuilder(PositionPlaceholderProcessor::class)
                         ->disableProxyingToOriginalMethods()
                         ->onlyMethods($mockedMethods)
                         ->getMock();
        $instance->setTranslator($this->translator);
        return $instance;
    }

    /**
     * @throws ReflectionException
     */
    public function testProcessMatch(): void
    {
        $locale = 'abc';
        $values = ['2'];
        $parameters = ['ghi', 'jkl', 'mno'];
        $expectedLocalisedString = 'jkl';
        $translatedValue = 'pqr';

        $this->translator->expects($this->once())
                         ->method('translateWithFallback')
                         ->with($this->identicalTo($locale), $this->identicalTo($expectedLocalisedString))
                         ->willReturn($translatedValue);

        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'processMatch', $locale, $values, $parameters);

        $this->assertSame($translatedValue, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testProcessMatchWithMissingParameter(): void
    {
        $locale = 'abc';
        $values = ['42'];
        $parameters = ['ghi', 'jkl', 'mno'];

        $this->translator->expects($this->never())
                         ->method('translateWithFallback');

        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'processMatch', $locale, $values, $parameters);

        $this->assertNull($result);
    }
}
