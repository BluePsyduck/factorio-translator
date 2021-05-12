<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Loader;

use BluePsyduck\FactorioTranslator\Loader\AbstractLoader;
use BluePsyduck\FactorioTranslator\Storage;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the AbstractLoader class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Loader\AbstractLoader
 */
class AbstractLoaderTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the parseContents method.
     * @throws ReflectionException
     * @covers ::parseContents
     */
    public function testParseContents(): void
    {
        $locale = 'foo';

        $contents = <<<EOT
        abc=def
            ghi=jkl
        ;fail=comment
        [mno]
        abc=pqr 
        EOT;

        $storage = $this->createMock(Storage::class);
        $storage->expects($this->exactly(3))
                ->method('set')
                ->withConsecutive(
                    [
                        $this->identicalTo($locale),
                        $this->identicalTo(''),
                        $this->identicalTo('abc'),
                        $this->identicalTo('def'),
                    ],
                    [
                        $this->identicalTo($locale),
                        $this->identicalTo(''),
                        $this->identicalTo('ghi'),
                        $this->identicalTo('jkl'),
                    ],
                    [
                        $this->identicalTo($locale),
                        $this->identicalTo('mno'),
                        $this->identicalTo('abc'),
                        $this->identicalTo('pqr '),
                    ],
                );

        /* @var AbstractLoader&MockObject $loader */
        $loader = $this->getMockBuilder(AbstractLoader::class)
                       ->getMockForAbstractClass();
        $loader->setStorage($storage);

        $this->invokeMethod($loader, 'parseContents', $locale, $contents);
    }
}
