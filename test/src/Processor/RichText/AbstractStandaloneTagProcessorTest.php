<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\RichText;

use BluePsyduck\FactorioTranslator\Processor\RichText\AbstractStandaloneTagProcessor;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the AbstractStandaloneTagProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Processor\RichText\AbstractStandaloneTagProcessor
 */
class AbstractStandaloneTagProcessorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $processor = $this->getMockBuilder(AbstractStandaloneTagProcessor::class)
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
        $values = ['item', 'def'];
        $parameters = ['ghi'];
        $expectedName = 'item';
        $expectedValue = 'def';
        $processedValue = 'jkl';

        $processor = $this->getMockBuilder(AbstractStandaloneTagProcessor::class)
                          ->onlyMethods(['processTag'])
                          ->getMockForAbstractClass();
        $processor->expects($this->once())
                  ->method('processTag')
                  ->with(
                      $this->identicalTo($locale),
                      $this->identicalTo($expectedName),
                      $this->identicalTo($expectedValue),
                  )
                  ->willReturn($processedValue);

        $result = $this->invokeMethod($processor, 'processMatch', $locale, $values, $parameters);

        $this->assertSame($processedValue, $result);
    }

    /**
     * @throws ReflectionException
     * @covers ::processMatch
     */
    public function testProcessMatchWithUnknownName(): void
    {
        $locale = 'abc';
        $values = ['invalid', 'def'];
        $parameters = ['ghi'];

        $processor = $this->getMockBuilder(AbstractStandaloneTagProcessor::class)
                          ->onlyMethods(['processTag'])
                          ->getMockForAbstractClass();
        $processor->expects($this->never())
                  ->method('processTag');

        $result = $this->invokeMethod($processor, 'processMatch', $locale, $values, $parameters);

        $this->assertNull($result);
    }
}
