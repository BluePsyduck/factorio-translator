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
 * @covers \BluePsyduck\FactorioTranslator\Loader\ModArchiveLoader
 */
class ModArchiveLoaderTest extends TestCase
{
    /**
     * @param array<string> $mockedMethods
     * @return ModArchiveLoader&MockObject
     */
    private function createInstance(array $mockedMethods = []): ModArchiveLoader
    {
        return $this->getMockBuilder(ModArchiveLoader::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->getMock();
    }

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
     * @dataProvider provideSupports
     */
    public function testSupports(string $path, bool $expectedResult): void
    {
        $instance = $this->createInstance();
        $result = $instance->supports($path);

        $this->assertSame($expectedResult, $result);
    }

    public function testLoad(): void
    {
        $path = __DIR__ . '/../../asset/mod-archive.zip';
        $expectedLocale = 'foo';
        $expectedContents = "foo=bar\n";

        $instance = $this->createInstance(['parseContents']);
        $instance->expects($this->once())
                 ->method('parseContents')
                 ->with($this->identicalTo($expectedLocale), $this->identicalTo($expectedContents));

        $instance->load($path);
    }
}
