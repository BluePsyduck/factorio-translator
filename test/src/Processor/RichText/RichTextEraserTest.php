<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\RichText;

use BluePsyduck\FactorioTranslator\Processor\RichText\RichTextEraser;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the RichTextEraser class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Processor\RichText\RichTextEraser
 */
class RichTextEraserTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $processor = new RichTextEraser();

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
        $values = ['def'];
        $parameters = ['ghi'];

        $processor = new RichTextEraser();
        $result = $this->invokeMethod($processor, 'processMatch', $locale, $values, $parameters);

        $this->assertSame('', $result);
    }
}
