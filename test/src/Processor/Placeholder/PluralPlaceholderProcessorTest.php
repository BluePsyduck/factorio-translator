<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\Placeholder\PluralPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Translator;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the PluralPlaceholderProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \BluePsyduck\FactorioTranslator\Processor\Placeholder\PluralPlaceholderProcessor
 */
class PluralPlaceholderProcessorTest extends TestCase
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
     * @return PluralPlaceholderProcessor&MockObject
     */
    private function createInstance(array $mockedMethods = []): PluralPlaceholderProcessor
    {
        $instance = $this->getMockBuilder(PluralPlaceholderProcessor::class)
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
        $values = ['2', 'def'];
        $parameters = ['42', '1337', '21'];
        $expectedParameter = 1337;
        $expectedConditions = 'def';
        $translatedValue = 'ghi';

        $instance = $this->createInstance(['processConditions']);
        $instance->expects($this->once())
                 ->method('processConditions')
                 ->with(
                     $this->identicalTo($locale),
                     $this->identicalTo($expectedConditions),
                     $this->identicalTo($expectedParameter)
                 )
                 ->willReturn($translatedValue);

        $result = $this->invokeMethod($instance, 'processMatch', $locale, $values, $parameters);

        $this->assertSame($translatedValue, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testProcessMatchWithMissingParameter(): void
    {
        $locale = 'abc';
        $values = ['2', 'def'];
        $parameters = ['42'];

        $instance = $this->createInstance(['processConditions']);
        $instance->expects($this->never())
                 ->method('processConditions');

        $result = $this->invokeMethod($instance, 'processMatch', $locale, $values, $parameters);

        $this->assertNull($result);
    }

    /**
     * @throws ReflectionException
     */
    public function testProcessConditions(): void
    {
        $locale = 'foo';
        $conditions = 'abc,def=ghi|jkl=mno|rest=pqr';
        $number = 42;
        $expectedString = 'mno';
        $translatedValue = 'stu';

        $this->translator->expects($this->once())
                         ->method('applyProcessors')
                         ->with(
                             $this->identicalTo($locale),
                             $this->identicalTo($expectedString),
                             $this->identicalTo([]),
                         )
                         ->willReturn($translatedValue);

        $instance = $this->createInstance(['evaluateCondition']);
        $instance->expects($this->exactly(3))
                 ->method('evaluateCondition')
                 ->withConsecutive(
                     [$this->identicalTo('abc'), $this->identicalTo($number)],
                     [$this->identicalTo('def'), $this->identicalTo($number)],
                     [$this->identicalTo('jkl'), $this->identicalTo($number)],
                 )
                 ->willReturnOnConsecutiveCalls(
                     false,
                     false,
                     true,
                 );

        $result = $this->invokeMethod($instance, 'processConditions', $locale, $conditions, $number);

        $this->assertSame($translatedValue, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testProcessConditionsWithoutMatch(): void
    {
        $locale = 'foo';
        $conditions = 'invalid|invalid=abc';
        $number = 42;

        $this->translator->expects($this->never())
                         ->method('applyProcessors');

        $instance = $this->createInstance(['evaluateCondition']);
        $instance->expects($this->once())
                 ->method('evaluateCondition')
                 ->with($this->identicalTo('invalid'), $this->identicalTo($number))
                 ->willReturn(false);

        $result = $this->invokeMethod($instance, 'processConditions', $locale, $conditions, $number);

        $this->assertNull($result);
    }

    /**
     * @return array<mixed>
     */
    public function provideEvaluateCondition(): array
    {
        return [
            ['rest', 42, true],

            ['42', 42, true],
            ['1337', 42, false],
            ['2', 42, false],
            ['42', 2, false],

            ['ends in 2', 42, true],
            ['ends in 2', 21, false],
            ['ends in 42', 42, true],
            ['ends in 42', 2, false],

            ['invalid', 42, false],
        ];
    }

    /**
     * @param string $condition
     * @param int $number
     * @param bool $expectedResult
     * @throws ReflectionException
     * @dataProvider provideEvaluateCondition
     */
    public function testEvaluateCondition(string $condition, int $number, bool $expectedResult): void
    {
        $instance = $this->createInstance();
        $result = $this->invokeMethod($instance, 'evaluateCondition', $condition, $number);

        $this->assertSame($expectedResult, $result);
    }
}
