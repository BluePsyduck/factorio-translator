<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator;

use BluePsyduck\FactorioTranslator\Exception\NoSupportedLoaderException;
use BluePsyduck\FactorioTranslator\Loader\LoaderInterface;
use BluePsyduck\FactorioTranslator\Processor\ProcessorInterface;
use BluePsyduck\FactorioTranslator\Storage;
use BluePsyduck\FactorioTranslator\StorageAwareInterface;
use BluePsyduck\FactorioTranslator\Translator;
use BluePsyduck\FactorioTranslator\TranslatorAwareInterface;
use BluePsyduck\TestHelper\ReflectionTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the Translator class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \BluePsyduck\FactorioTranslator\Translator
 */
class TranslatorTest extends TestCase
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
     * @return Translator&MockObject
     */
    private function createInstance(array $mockedMethods = []): Translator
    {
        return $this->getMockBuilder(Translator::class)
                    ->disableProxyingToOriginalMethods()
                    ->onlyMethods($mockedMethods)
                    ->setConstructorArgs([
                        $this->storage,
                    ])
                    ->getMock();
    }

    /**
     * @throws ReflectionException
     */
    public function testAddLoader(): void
    {
        $loader1 = $this->createMock(LoaderInterface::class);
        $loader2 = $this->createMock(LoaderInterface::class);
        $loader3 = $this->createMock(LoaderInterface::class);

        $loaders = [$loader1, $loader2];
        $expectedLoaders = [$loader1, $loader2, $loader3];

        $instance = $this->createInstance(['initialize']);
        $instance->expects($this->once())
                 ->method('initialize')
                 ->with($this->identicalTo($loader3));
        $this->injectProperty($instance, 'loaders', $loaders);

        $result = $instance->addLoader($loader3);

        $this->assertSame($instance, $result);
        $this->assertSame($expectedLoaders, $this->extractProperty($instance, 'loaders'));
    }

    /**
     * @throws ReflectionException
     */
    public function testAddProcessor(): void
    {
        $processor1 = $this->createMock(ProcessorInterface::class);
        $processor2 = $this->createMock(ProcessorInterface::class);
        $processor3 = $this->createMock(ProcessorInterface::class);

        $processors = [$processor1, $processor2];
        $expectedProcessors = [$processor1, $processor2, $processor3];

        $instance = $this->createInstance(['initialize']);
        $instance->expects($this->once())
                 ->method('initialize')
                 ->with($this->identicalTo($processor3));
        $this->injectProperty($instance, 'processors', $processors);

        $result = $instance->addProcessor($processor3);

        $this->assertSame($instance, $result);
        $this->assertSame($expectedProcessors, $this->extractProperty($instance, 'processors'));
    }

    /**
     * @throws ReflectionException
     */
    public function testInitializeWithStorageInterface(): void
    {
        $instance = $this->createInstance();

        $object = $this->createMock(StorageAwareInterface::class);
        $object->expects($this->once())
               ->method('setStorage')
               ->with($this->identicalTo($this->storage));

        $this->invokeMethod($instance, 'initialize', $object);
    }

    /**
     * @throws ReflectionException
     */
    public function testInitializeWithTranslatorInterface(): void
    {
        $instance = $this->createInstance();

        $object = $this->createMock(TranslatorAwareInterface::class);
        $object->expects($this->once())
               ->method('setTranslator')
               ->with($this->identicalTo($instance));

        $this->invokeMethod($instance, 'initialize', $object);
    }

    /**
     * @throws NoSupportedLoaderException
     * @throws ReflectionException
     */
    public function testLoadMod(): void
    {
        $path = 'abc';

        $loader1 = $this->createMock(LoaderInterface::class);
        $loader1->expects($this->once())
                ->method('supports')
                ->with($this->identicalTo($path))
                ->willReturn(false);
        $loader1->expects($this->never())
                ->method('load');

        $loader2 = $this->createMock(LoaderInterface::class);
        $loader2->expects($this->once())
                ->method('supports')
                ->with($this->identicalTo($path))
                ->willReturn(true);
        $loader2->expects($this->once())
                ->method('load')
                ->with($this->identicalTo($path));

        $instance = $this->createInstance();
        $this->injectProperty($instance, 'loaders', [$loader1, $loader2]);

        $result = $instance->loadMod($path);

        $this->assertSame($instance, $result);
    }

    /**
     * @throws NoSupportedLoaderException
     * @throws ReflectionException
     */
    public function testLoadModWithException(): void
    {
        $path = 'abc';

        $loader1 = $this->createMock(LoaderInterface::class);
        $loader1->expects($this->once())
                ->method('supports')
                ->with($this->identicalTo($path))
                ->willReturn(false);
        $loader1->expects($this->never())
                ->method('load');

        $loader2 = $this->createMock(LoaderInterface::class);
        $loader2->expects($this->once())
                ->method('supports')
                ->with($this->identicalTo($path))
                ->willReturn(false);
        $loader2->expects($this->never())
                ->method('load');

        $this->expectException(NoSupportedLoaderException::class);

        $instance = $this->createInstance();
        $this->injectProperty($instance, 'loaders', [$loader1, $loader2]);

        $instance->loadMod($path);
    }

    public function testTranslateWithUntranslatedValue(): void
    {
        $locale = 'abc';
        $localisedString = 'def';
        $processedString = 'ghi';

        $instance = $this->createInstance(['applyProcessors']);
        $instance->expects($this->once())
                 ->method('applyProcessors')
                 ->with($this->identicalTo($locale), $this->identicalTo($localisedString), $this->identicalTo([]))
                 ->willReturn($processedString);
        $result = $instance->translate($locale, $localisedString);

        $this->assertSame($processedString, $result);
    }

    public function testTranslateWithConcatenatedValue(): void
    {
        $locale = 'abc';
        $localisedString = ['', 'def', 'ghi'];
        $parts = ['def', 'ghi'];
        $concatenatedValue = 'jkl';

        $instance = $this->createInstance(['concatenate']);
        $instance->expects($this->once())
                 ->method('concatenate')
                 ->with($this->identicalTo($locale), $this->identicalTo($parts))
                 ->willReturn($concatenatedValue);

        $result = $instance->translate($locale, $localisedString);

        $this->assertSame($concatenatedValue, $result);
    }

    public function testTranslateWithActualTranslation(): void
    {
        $locale = 'abc';
        $localisedString = ['def', 'ghi', 'jkl'];
        $key = 'def';
        $parameters = ['ghi', 'jkl'];
        $translatedString = 'mno';

        $instance = $this->createInstance(['doTranslate']);
        $instance->expects($this->once())
                 ->method('doTranslate')
                 ->with($this->identicalTo($locale), $this->identicalTo($key), $this->identicalTo($parameters))
                 ->willReturn($translatedString);

        $result = $instance->translate($locale, $localisedString);

        $this->assertSame($translatedString, $result);
    }

    public function testTranslateWithFallback(): void
    {
        $locale = 'abc';
        $localisedString = ['def'];
        $translation = 'ghi';

        $instance = $this->createInstance(['translate']);
        $instance->expects($this->exactly(2))
                 ->method('translate')
                 ->withConsecutive(
                     [$this->identicalTo($locale), $this->identicalTo($localisedString)],
                     [$this->identicalTo('en'), $this->identicalTo($localisedString)],
                 )
                 ->willReturnOnConsecutiveCalls(
                     '',
                     $translation,
                 );

        $result = $instance->translateWithFallback($locale, $localisedString);

        $this->assertSame($translation, $result);
    }

    public function testTranslateWithFallbackWithoutFallback(): void
    {
        $locale = 'abc';
        $localisedString = ['def'];
        $translation = 'ghi';

        $instance = $this->createInstance(['translate']);
        $instance->expects($this->once())
                 ->method('translate')
                 ->with($this->identicalTo($locale), $this->identicalTo($localisedString))
                 ->willReturn($translation);

        $result = $instance->translateWithFallback($locale, $localisedString);

        $this->assertSame($translation, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testConcatenate(): void
    {
        $locale = 'abc';
        $parts = ['def', 'ghi'];
        $expectedResult = 'jklmno';

        $instance = $this->createInstance(['translateWithFallback']);
        $instance->expects($this->exactly(2))
                 ->method('translateWithFallback')
                 ->withConsecutive(
                     [$this->identicalTo($locale), $this->identicalTo('def')],
                     [$this->identicalTo($locale), $this->identicalTo('ghi')],
                 )
                 ->willReturnOnConsecutiveCalls(
                     'jkl',
                     'mno'
                 );

        $result = $this->invokeMethod($instance, 'concatenate', $locale, $parts);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testDoTranslate(): void
    {
        $locale = 'abc';
        $key = 'def.ghi';
        $section = 'def';
        $name = 'ghi';
        $parameters = ['jkl', 'mno'];
        $storageValue = 'pqr';
        $processedValue = 'stu';

        $this->storage->expects($this->once())
                      ->method('has')
                      ->with($this->identicalTo($locale), $this->identicalTo($section), $this->identicalTo($name))
                      ->willReturn(true);
        $this->storage->expects($this->once())
                      ->method('get')
                      ->with($this->identicalTo($locale), $this->identicalTo($section), $this->identicalTo($name))
                      ->willReturn($storageValue);

        $instance = $this->createInstance(['applyProcessors']);
        $instance->expects($this->once())
                 ->method('applyProcessors')
                 ->with(
                     $this->identicalTo($locale),
                     $this->identicalTo($storageValue),
                     $this->identicalTo($parameters),
                 )
                 ->willReturn($processedValue);

        $result = $this->invokeMethod($instance, 'doTranslate', $locale, $key, $parameters);

        $this->assertSame($processedValue, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testDoTranslateWithMissingSection(): void
    {
        $locale = 'abc';
        $key = 'ghi';
        $section = '';
        $name = 'ghi';
        $parameters = ['jkl', 'mno'];
        $storageValue = 'pqr';
        $processedValue = 'stu';

        $this->storage->expects($this->once())
                      ->method('has')
                      ->with($this->identicalTo($locale), $this->identicalTo($section), $this->identicalTo($name))
                      ->willReturn(true);
        $this->storage->expects($this->once())
                      ->method('get')
                      ->with($this->identicalTo($locale), $this->identicalTo($section), $this->identicalTo($name))
                      ->willReturn($storageValue);

        $instance = $this->createInstance(['applyProcessors']);
        $instance->expects($this->once())
                 ->method('applyProcessors')
                 ->with(
                     $this->identicalTo($locale),
                     $this->identicalTo($storageValue),
                     $this->identicalTo($parameters),
                 )
                 ->willReturn($processedValue);

        $result = $this->invokeMethod($instance, 'doTranslate', $locale, $key, $parameters);

        $this->assertSame($processedValue, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testDoTranslateWithUnknownKey(): void
    {
        $locale = 'abc';
        $key = 'def.ghi';
        $section = 'def';
        $name = 'ghi';
        $parameters = ['jkl', 'mno'];

        $this->storage->expects($this->once())
                      ->method('has')
                      ->with($this->identicalTo($locale), $this->identicalTo($section), $this->identicalTo($name))
                      ->willReturn(false);
        $this->storage->expects($this->never())
                      ->method('get');

        $instance = $this->createInstance(['applyProcessors']);
        $instance->expects($this->never())
                 ->method('applyProcessors');

        $result = $this->invokeMethod($instance, 'doTranslate', $locale, $key, $parameters);

        $this->assertSame('', $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testApplyProcessors(): void
    {
        $locale = 'foo';
        $string1 = 'abc';
        $string2 = 'def';
        $string3 = 'ghi';
        $parameters = ['bar'];

        $processor1 = $this->createMock(ProcessorInterface::class);
        $processor1->expects($this->once())
                   ->method('process')
                   ->with($this->identicalTo($locale), $this->identicalTo($string1), $this->identicalTo($parameters))
                   ->willReturn($string2);

        $processor2 = $this->createMock(ProcessorInterface::class);
        $processor2->expects($this->once())
                   ->method('process')
                   ->with($this->identicalTo($locale), $this->identicalTo($string2), $this->identicalTo($parameters))
                   ->willReturn($string3);

        $instance = $this->createInstance();
        $this->injectProperty($instance, 'processors', [$processor1, $processor2]);

        $result = $instance->applyProcessors($locale, $string1, $parameters);

        $this->assertSame($string3, $result);
    }

    public function testGetAllLocales(): void
    {
        $locales = ['abc', 'def'];

        $this->storage->expects($this->once())
                      ->method('getLocales')
                      ->willReturn($locales);

        $instance = $this->createInstance();
        $result = $instance->getAllLocales();

        $this->assertSame($locales, $result);
    }
}
