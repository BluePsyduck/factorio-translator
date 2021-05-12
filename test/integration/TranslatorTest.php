<?php

declare(strict_types=1);

namespace BluePsyduckIntegrationTest\FactorioTranslator;

use BluePsyduck\FactorioTranslator\Exception\NoSupportedLoaderException;
use BluePsyduck\FactorioTranslator\Loader\ModDirectoryLoader;
use BluePsyduck\FactorioTranslator\Processor\Placeholder\AbstractControlPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Processor\Placeholder\EntityPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Processor\Placeholder\ItemPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Processor\Placeholder\PluralPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Processor\Placeholder\PositionPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Processor\RichText\AbstractContentTagProcessor;
use BluePsyduck\FactorioTranslator\Processor\RichText\AbstractStandaloneTagProcessor;
use BluePsyduck\FactorioTranslator\Translator;
use PHPUnit\Framework\TestCase;

/**
 * The integration test of the Translator class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TranslatorTest extends TestCase
{
    protected static Translator $translator;

    /**
     * @throws NoSupportedLoaderException
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // We are setting up the translator only once and re-use it through all tests to avoid parsing the locale files
        // for each of the many test cases.
        $translator = new Translator();
        $translator->addLoader(new ModDirectoryLoader());
        $translator->loadMod(__DIR__ . '/../asset/integration');

        $translator->addProcessor(new PositionPlaceholderProcessor())
                   ->addProcessor(new EntityPlaceholderProcessor())
                   ->addProcessor(new ItemPlaceholderProcessor())
                   ->addProcessor(new PluralPlaceholderProcessor());

        $translator->addProcessor(new class extends AbstractControlPlaceholderProcessor {
            protected function processControl(string $locale, string $controlName, int $version): ?string
            {
                return "{control={$version} {$controlName}}";
            }
        });
        $translator->addProcessor(new class extends AbstractStandaloneTagProcessor {
            protected function processTag(string $locale, string $name, string $value): ?string
            {
                return "{{$name}={$value}}";
            }
        });
        $translator->addProcessor(new class extends AbstractContentTagProcessor {
            protected function processTag(string $locale, string $name, string $value, string $content): ?string
            {
                return "{{$name}={$value}}{$content}{/{$name}}";
            }
        });

        self::$translator = $translator;
    }

    /**
     * @return array<mixed>
     */
    public function provideTranslate(): array
    {
        // phpcs:disable Generic.Files.LineLength
        return [
            // Basic translations
            ['en', ['item-name.electronic-circuit'], 'Electronic circuit'],
            ['de', ['item-name.electronic-circuit'], 'Elektronischer Schaltkreis'],

            // Untranslated strings
            ['en', 'item-name.electronic-circuit', 'item-name.electronic-circuit'],

            // Recursive translation with English fallback
            ['en', ['recipe-name.fill-barrel', ['fluid-name.crude-oil']], 'Fill Crude oil barrel'],
            ['de', ['recipe-name.fill-barrel', ['fluid-name.crude-oil']], 'Fülle Rohöl in Fass'],
            ['en', ['recipe-name.fill-barrel', ['fluid-name.lubricant']], 'Fill Lubricant barrel'],
            ['de', ['recipe-name.fill-barrel', ['fluid-name.lubricant']], 'Fülle Lubricant in Fass'],

            // Control placeholders
            ['en', ['item-description.green-wire'], 'Used to connect machines to the circuit network using {control=0 build}.'],
            ['de', ['item-description.green-wire'], 'Verbindet Maschinen mit dem Schaltungsnetz. {control=2 build}, um Verbindungen zu erzeugen oder zu kappen.'],

            // Entity placeholders
            ['en', ['autoplace-control-names.coal'], 'Coal'],
            ['de', ['autoplace-control-names.coal'], 'Kohle'],

            // Item placeholders
            ['en', ['goal-get-resources', 21, 42, 1337, 7331], "Gather resources: \nIron plate: 21/42\nCopper plate: 1337/7331"],
            ['de', ['goal-get-resources', 21, 42, 1337, 7331], "Sammle Gegenstände im Inventar:\nEisenplatte: 21/42\nKupferplatte: 1337/7331"],

            // Plural forms
            ['en', ['hours', 1], '1 hour'],
            ['en', ['hours', 42], '42 hours'],
            ['de', ['hours', 1], '1 Stunde'],
            ['de', ['hours', 42], '42 Stunden'],
            ['pl', ['hours', 1], '1 godzina'],
            ['pl', ['hours', 12], '12 godzin'],
            ['pl', ['hours', 42], '42 godziny'],
            ['pl', ['hours', 1337], '1337 godzin'],

            // Rich text
            ['en', ['item-description.empty-dt-fuel'], 'Fill it with tritium {item=tritium} and heavy water {fluid=heavy-water} to get a charged cell.'],
            ['de', ['item-description.empty-dt-fuel'], 'Befülle sie mit Tritium {item=tritium} und schwerem Wasser {fluid=heavy-water}, um sie zu laden.'],

            ['en', ['entity-name.imersite'], '{color=173, 19, 173}Imersite cave{/color}'],
            ['de', ['entity-name.imersite'], '{color=173, 19, 173}Imersitgrotte{/color}'],

            // Concatenating
            ['en', ['', ['color.blue'], ' ' , ['tile-name.refined-concrete']], 'Blue Refined concrete'],
            ['en', ['', 'foo ', '[item=iron-plate]', ' bar'], 'foo {item=iron-plate} bar'],
        ];
        // phpcs:enable Generic.Files.LineLength
    }

    /**
     * @covers ::translate
     * @dataProvider provideTranslate
     * @param string $locale
     * @param mixed $localisedString
     * @param string $expectedResult
     */
    public function testTranslate(string $locale, $localisedString, string $expectedResult): void
    {
        $result = self::$translator->translate($locale, $localisedString);

        $this->assertSame($expectedResult, $result);
    }
}
