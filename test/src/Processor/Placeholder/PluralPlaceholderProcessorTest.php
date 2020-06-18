<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\Placeholder\PluralPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Translator;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the PluralPlaceholderProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Processor\Placeholder\PluralPlaceholderProcessor
 */
class PluralPlaceholderProcessorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $processor = new PluralPlaceholderProcessor();

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
        $values = ['2', 'def'];
        $parameters = ['42', '1337', '21'];
        $expectedParameter = 1337;
        $expectedConditions = 'def';
        $translatedValue = 'ghi';

        $processor = $this->getMockBuilder(PluralPlaceholderProcessor::class)
                          ->onlyMethods(['processConditions'])
                          ->getMock();
        $processor->expects($this->once())
                  ->method('processConditions')
                  ->with(
                      $this->identicalTo($locale),
                      $this->identicalTo($expectedConditions),
                      $this->identicalTo($expectedParameter)
                  )
                  ->willReturn($translatedValue);

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
        $values = ['2', 'def'];
        $parameters = ['42'];

        $processor = $this->getMockBuilder(PluralPlaceholderProcessor::class)
                          ->onlyMethods(['processConditions'])
                          ->getMock();
        $processor->expects($this->never())
                  ->method('processConditions');

        $result = $this->invokeMethod($processor, 'processMatch', $locale, $values, $parameters);

        $this->assertNull($result);
    }

    /**
     * @throws ReflectionException
     * @covers ::processConditions
     */
    public function testProcessConditions(): void
    {
        $locale = 'foo';
        $conditions = 'abc,def=ghi|jkl=mno|rest=pqr';
        $number = 42;
        $expectedString = 'mno';
        $translatedValue = 'stu';

        $translator = $this->createMock(Translator::class);
        $translator->expects($this->once())
                   ->method('applyProcessors')
                   ->with($this->identicalTo($locale), $this->identicalTo($expectedString), $this->identicalTo([]))
                   ->willReturn($translatedValue);

        $processor = $this->getMockBuilder(PluralPlaceholderProcessor::class)
                          ->onlyMethods(['evaluateCondition'])
                          ->getMock();
        $processor->expects($this->exactly(3))
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
        $processor->setTranslator($translator);

        $result = $this->invokeMethod($processor, 'processConditions', $locale, $conditions, $number);

        $this->assertSame($translatedValue, $result);
    }

    /**
     * @throws ReflectionException
     * @covers ::processConditions
     */
    public function testProcessConditionsWithoutMatch(): void
    {
        $locale = 'foo';
        $conditions = 'invalid|invalid=abc';
        $number = 42;

        $translator = $this->createMock(Translator::class);
        $translator->expects($this->never())
                   ->method('applyProcessors');

        $processor = $this->getMockBuilder(PluralPlaceholderProcessor::class)
                          ->onlyMethods(['evaluateCondition'])
                          ->getMock();
        $processor->expects($this->once())
                  ->method('evaluateCondition')
                  ->with($this->identicalTo('invalid'), $this->identicalTo($number))
                  ->willReturn(false);
        $processor->setTranslator($translator);

        $result = $this->invokeMethod($processor, 'processConditions', $locale, $conditions, $number);

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
     * @covers ::evaluateCondition
     * @dataProvider provideEvaluateCondition
     */
    public function testEvaluateCondition(string $condition, int $number, bool $expectedResult): void
    {
        $processor = new PluralPlaceholderProcessor();
        $result = $this->invokeMethod($processor, 'evaluateCondition', $condition, $number);

        $this->assertSame($expectedResult, $result);
    }
}
