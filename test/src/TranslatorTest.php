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
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Translator
 */
class TranslatorTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var Storage&MockObject
     */
    protected $storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storage = $this->createMock(Storage::class);
    }

    /**
     * @throws ReflectionException
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $translator = new Translator($this->storage);

        $this->assertSame($this->storage, $this->extractProperty($translator, 'storage'));
    }

    /**
     * @throws ReflectionException
     * @covers ::addLoader
     */
    public function testAddLoader(): void
    {
        $loader1 = $this->createMock(LoaderInterface::class);
        $loader2 = $this->createMock(LoaderInterface::class);
        $loader3 = $this->createMock(LoaderInterface::class);

        $loaders = [$loader1, $loader2];
        $expectedLoaders = [$loader1, $loader2, $loader3];

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['initialize'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->once())
                   ->method('initialize')
                   ->with($this->identicalTo($loader3));
        $this->injectProperty($translator, 'loaders', $loaders);

        $result = $translator->addLoader($loader3);

        $this->assertSame($translator, $result);
        $this->assertSame($expectedLoaders, $this->extractProperty($translator, 'loaders'));
    }

    /**
     * @throws ReflectionException
     * @covers ::addProcessor
     */
    public function testAddProcessor(): void
    {
        $processor1 = $this->createMock(ProcessorInterface::class);
        $processor2 = $this->createMock(ProcessorInterface::class);
        $processor3 = $this->createMock(ProcessorInterface::class);

        $processors = [$processor1, $processor2];
        $expectedProcessors = [$processor1, $processor2, $processor3];

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['initialize'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->once())
                   ->method('initialize')
                   ->with($this->identicalTo($processor3));
        $this->injectProperty($translator, 'processors', $processors);

        $result = $translator->addProcessor($processor3);

        $this->assertSame($translator, $result);
        $this->assertSame($expectedProcessors, $this->extractProperty($translator, 'processors'));
    }

    /**
     * @throws ReflectionException
     * @covers ::initialize
     */
    public function testInitializeWithStorageInterface(): void
    {
        $translator = new Translator($this->storage);

        $instance = $this->createMock(StorageAwareInterface::class);
        $instance->expects($this->once())
                 ->method('setStorage')
                 ->with($this->identicalTo($this->storage));

        $this->invokeMethod($translator, 'initialize', $instance);
    }

    /**
     * @throws ReflectionException
     * @covers ::initialize
     */
    public function testInitializeWithTranslatorInterface(): void
    {
        $translator = new Translator($this->storage);

        $instance = $this->createMock(TranslatorAwareInterface::class);
        $instance->expects($this->once())
                 ->method('setTranslator')
                 ->with($this->identicalTo($translator));

        $this->invokeMethod($translator, 'initialize', $instance);
    }

    /**
     * @throws NoSupportedLoaderException
     * @throws ReflectionException
     * @covers ::loadMod
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

        $translator = new Translator($this->storage);
        $this->injectProperty($translator, 'loaders', [$loader1, $loader2]);

        $result = $translator->loadMod($path);

        $this->assertSame($translator, $result);
    }

    /**
     * @throws NoSupportedLoaderException
     * @throws ReflectionException
     * @covers ::loadMod
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

        $translator = new Translator($this->storage);
        $this->injectProperty($translator, 'loaders', [$loader1, $loader2]);

        $translator->loadMod($path);
    }

    /**
     * @covers ::translate
     */
    public function testTranslateWithUntranslatedValue(): void
    {
        $locale = 'abc';
        $localisedString = 'def';
        $processedString = 'ghi';

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['applyProcessors'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->once())
                   ->method('applyProcessors')
                   ->with($this->identicalTo($locale), $this->identicalTo($localisedString), $this->identicalTo([]))
                   ->willReturn($processedString);
        $result = $translator->translate($locale, $localisedString);

        $this->assertSame($processedString, $result);
    }

    /**
     * @covers ::translate
     */
    public function testTranslateWithConcatenatedValue(): void
    {
        $locale = 'abc';
        $localisedString = ['', 'def', 'ghi'];
        $parts = ['def', 'ghi'];
        $concatenatedValue = 'jkl';

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['concatenate'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->once())
                   ->method('concatenate')
                   ->with($this->identicalTo($locale), $this->identicalTo($parts))
                   ->willReturn($concatenatedValue);

        $result = $translator->translate($locale, $localisedString);

        $this->assertSame($concatenatedValue, $result);
    }

    /**
     * @covers ::translate
     */
    public function testTranslateWithActualTranslation(): void
    {
        $locale = 'abc';
        $localisedString = ['def', 'ghi', 'jkl'];
        $key = 'def';
        $parameters = ['ghi', 'jkl'];
        $translatedString = 'mno';

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['doTranslate'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->once())
                   ->method('doTranslate')
                   ->with($this->identicalTo($locale), $this->identicalTo($key), $this->identicalTo($parameters))
                   ->willReturn($translatedString);

        $result = $translator->translate($locale, $localisedString);

        $this->assertSame($translatedString, $result);
    }

    /**
     * @covers ::translateWithFallback
     */
    public function testTranslateWithFallback(): void
    {
        $locale = 'abc';
        $localisedString = ['def'];
        $translation = 'ghi';

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['translate'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->exactly(2))
                   ->method('translate')
                   ->withConsecutive(
                       [$this->identicalTo($locale), $this->identicalTo($localisedString)],
                       [$this->identicalTo('en'), $this->identicalTo($localisedString)],
                   )
                   ->willReturnOnConsecutiveCalls(
                       '',
                       $translation,
                   );

        $result = $translator->translateWithFallback($locale, $localisedString);

        $this->assertSame($translation, $result);
    }

    /**
     * @covers ::translateWithFallback
     */
    public function testTranslateWithFallbackWithoutFallback(): void
    {
        $locale = 'abc';
        $localisedString = ['def'];
        $translation = 'ghi';

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['translate'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->once())
                   ->method('translate')
                   ->with($this->identicalTo($locale), $this->identicalTo($localisedString))
                   ->willReturn($translation);

        $result = $translator->translateWithFallback($locale, $localisedString);

        $this->assertSame($translation, $result);
    }

    /**
     * @throws ReflectionException
     * @covers ::concatenate
     */
    public function testConcatenate(): void
    {
        $locale = 'abc';
        $parts = ['def', 'ghi'];
        $expectedResult = 'jklmno';

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['translateWithFallback'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->exactly(2))
                   ->method('translateWithFallback')
                   ->withConsecutive(
                       [$this->identicalTo($locale), $this->identicalTo('def')],
                       [$this->identicalTo($locale), $this->identicalTo('ghi')],
                   )
                   ->willReturnOnConsecutiveCalls(
                       'jkl',
                       'mno'
                   );

        $result = $this->invokeMethod($translator, 'concatenate', $locale, $parts);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @throws ReflectionException
     * @covers ::doTranslate
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

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['applyProcessors'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->once())
                   ->method('applyProcessors')
                   ->with(
                       $this->identicalTo($locale),
                       $this->identicalTo($storageValue),
                       $this->identicalTo($parameters),
                   )
                   ->willReturn($processedValue);

        $result = $this->invokeMethod($translator, 'doTranslate', $locale, $key, $parameters);

        $this->assertSame($processedValue, $result);
    }

    /**
     * @throws ReflectionException
     * @covers ::doTranslate
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

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['applyProcessors'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->once())
                   ->method('applyProcessors')
                   ->with(
                       $this->identicalTo($locale),
                       $this->identicalTo($storageValue),
                       $this->identicalTo($parameters),
                   )
                   ->willReturn($processedValue);

        $result = $this->invokeMethod($translator, 'doTranslate', $locale, $key, $parameters);

        $this->assertSame($processedValue, $result);
    }

    /**
     * @throws ReflectionException
     * @covers ::doTranslate
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

        $translator = $this->getMockBuilder(Translator::class)
                           ->onlyMethods(['applyProcessors'])
                           ->setConstructorArgs([$this->storage])
                           ->getMock();
        $translator->expects($this->never())
                   ->method('applyProcessors');

        $result = $this->invokeMethod($translator, 'doTranslate', $locale, $key, $parameters);

        $this->assertSame('', $result);
    }

    /**
     * @throws ReflectionException
     * @covers ::applyProcessors
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

        $translator = new Translator($this->storage);
        $this->injectProperty($translator, 'processors', [$processor1, $processor2]);

        $result = $translator->applyProcessors($locale, $string1, $parameters);

        $this->assertSame($string3, $result);
    }

    /**
     * @covers ::getAllLocales
     */
    public function testGetAllLocales(): void
    {
        $locales = ['abc', 'def'];

        $this->storage->expects($this->once())
                      ->method('getLocales')
                      ->willReturn($locales);

        $translator = new Translator($this->storage);
        $result = $translator->getAllLocales();

        $this->assertSame($locales, $result);
    }
}
