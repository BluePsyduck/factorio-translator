<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Processor\RichText;

use BluePsyduck\FactorioTranslator\Processor\AbstractRegexProcessor;

/**
 * The abstract class for standalone tags like [item=electronic-circuit].
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractStandaloneTagProcessor extends AbstractRegexProcessor
{
    protected const PATTERN = '#\[(.+)=(.+)\]#U';
    protected const TAG_NAMES = [
        'img',
        'item',
        'entity',
        'technology',
        'recipe',
        'item-group',
        'fluid',
        'tile',
        'virtual-signal',
        'achievement',
        'gps',
        'special-item',
        'armor',
        'train',
        'train-stop',
    ];

    public function __construct()
    {
        parent::__construct(self::PATTERN);
    }

    /**
     * @param string $locale
     * @param array<string> $values
     * @param array<mixed> $parameters
     * @return string|null
     */
    protected function processMatch(string $locale, array $values, array $parameters): ?string
    {
        [$name, $value] = $values;
        if (!in_array($name, self::TAG_NAMES, true)) {
            return null;
        }

        return $this->processTag($locale, $name, $value);
    }

    /**
     * Processes the tag.
     * @param string $locale The locale the translator is currently running on.
     * @param string $name The name of the tag, e.g. "item".
     * @param string $value The unparsed value of the tag, e.g. "electronic-circuit".
     * @return string|null The replacement for the tag, or null to keep the tag as-is.
     */
    abstract protected function processTag(string $locale, string $name, string $value): ?string;
}
