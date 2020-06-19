<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Processor\RichText;

use BluePsyduck\FactorioTranslator\Processor\AbstractRegexProcessor;

/**
 * The class for erasing all (remaining or mismatched) rich text tags from the strings without replacement.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RichTextEraser extends AbstractRegexProcessor
{
    protected const PATTERN = '#\[(.+=.+|[./].+)\]#U';

    public function __construct()
    {
        parent::__construct(self::PATTERN);
    }

    /**
     * @param string $locale
     * @param array<string>|string[] $values
     * @param array<mixed> $parameters
     * @return string|null
     */
    protected function processMatch(string $locale, array $values, array $parameters): ?string
    {
        return '';
    }
}
