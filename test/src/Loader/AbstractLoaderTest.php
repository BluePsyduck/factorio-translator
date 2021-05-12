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
 * @covers \BluePsyduck\FactorioTranslator\Loader\AbstractLoader
 */
class AbstractLoaderTest extends TestCase
{
    use ReflectionTrait;

    /** @var Storage&MockObject */
    private Storage $storage;

    protected function setUp(): void
    {
        $this->storage = $this->createMock(Storage::class);
    }

    /**
     * @param array<string> $mockedMethods
     * @return AbstractLoader&MockObject
     */
    private function createInstance(array $mockedMethods = []): AbstractLoader
    {
        $instance = $this->getMockBuilder(AbstractLoader::class)
                         ->disableProxyingToOriginalMethods()
                         ->onlyMethods($mockedMethods)
                         ->getMockForAbstractClass();
        $instance->setStorage($this->storage);
        return $instance;
    }

    /**
     * @throws ReflectionException
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

        $this->storage->expects($this->exactly(3))
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

        $instance = $this->createInstance();
        $this->invokeMethod($instance, 'parseContents', $locale, $contents);
    }
}
