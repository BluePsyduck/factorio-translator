<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator;

use BluePsyduck\FactorioTranslator\Translator;
use BluePsyduck\FactorioTranslator\TranslatorAwareTrait;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the TranslatorAwareTrait class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\TranslatorAwareTrait
 */
class TranslatorAwareTraitTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the setTranslator method.
     * @throws ReflectionException
     * @covers ::setTranslator
     */
    public function testSetTranslator(): void
    {
        $translator = $this->createMock(Translator::class);

        /* @var TranslatorAwareTrait&MockObject $trait */
        $trait = $this->getMockBuilder(TranslatorAwareTrait::class)
                      ->getMockForTrait();

        $trait->setTranslator($translator);

        $this->assertSame($translator, $this->extractProperty($trait, 'translator'));
    }
}
