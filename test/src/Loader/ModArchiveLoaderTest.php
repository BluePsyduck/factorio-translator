<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Loader;

use BluePsyduck\FactorioTranslator\Loader\ModArchiveLoader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ModArchiveLoader class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Loader\ModArchiveLoader
 */
class ModArchiveLoaderTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function provideSupports(): array
    {
        return [
            [__DIR__ . '/../../asset/mod-archive.zip', true],
            [__DIR__ . '/../../asset/invalid-archive.zip', false],
            [__DIR__ . '/../../asset/mod-directory', false],
        ];
    }

    /**
     * @param string $path
     * @param bool $expectedResult
     * @covers ::supports
     * @dataProvider provideSupports
     */
    public function testSupports(string $path, bool $expectedResult): void
    {
        $loader = new ModArchiveLoader();
        $result = $loader->supports($path);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the load method.
     * @covers ::load
     */
    public function testLoad(): void
    {
        $path = __DIR__ . '/../../asset/mod-archive.zip';
        $expectedLocale = 'foo';
        $expectedContents = "foo=bar\n";

        /* @var ModArchiveLoader&MockObject $loader */
        $loader = $this->getMockBuilder(ModArchiveLoader::class)
                       ->onlyMethods(['parseContents'])
                       ->getMock();
        $loader->expects($this->once())
               ->method('parseContents')
               ->with($this->identicalTo($expectedLocale), $this->identicalTo($expectedContents));

        $loader->load($path);
    }
}
