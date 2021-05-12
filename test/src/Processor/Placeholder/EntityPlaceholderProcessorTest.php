<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\Placeholder\EntityPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Translator;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the EntityPlaceholderProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \BluePsyduck\FactorioTranslator\Processor\Placeholder\EntityPlaceholderProcessor
 */
class EntityPlaceholderProcessorTest extends TestCase
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
     * @return EntityPlaceholderProcessor&MockObject
     */
    private function createInstance(array $mockedMethods = []): EntityPlaceholderProcessor
    {
        $instance = $this->getMockBuilder(EntityPlaceholderProcessor::class)
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
        $values = ['def'];
        $parameters = ['ghi'];
        $expectedLocalisedString = ['entity-name.def'];
        $translatedValue = 'jkl';

        $this->translator->expects($this->once())
                         ->method('translateWithFallback')
                         ->with($this->identicalTo($locale), $this->identicalTo($expectedLocalisedString))
                         ->willReturn($translatedValue);

        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'processMatch', $locale, $values, $parameters);

        $this->assertSame($translatedValue, $result);
    }
}
