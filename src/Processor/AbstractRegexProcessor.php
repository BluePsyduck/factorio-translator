<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Processor;

/**
 * The abstract class for processors using a regular expression to find matches.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractRegexProcessor implements ProcessorInterface
{
    protected string $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param string $locale
     * @param string $string
     * @param array<mixed> $parameters
     * @return string
     */
    public function process(string $locale, string $string, array $parameters): string
    {
        $placeholders = $this->findPlaceholders($string);
        foreach ($placeholders as $placeholder => $values) {
            $replacement = $this->processMatch($locale, $values, $parameters);
            if ($replacement !== null) {
                $string = str_replace($placeholder, $replacement, $string);
            }
        }
        return $string;
    }

    /**
     * @param string $string
     * @return array<string,array<string>>
     */
    protected function findPlaceholders(string $string): array
    {
        $placeholders = [];
        if (preg_match_all($this->pattern, $string, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $placeholder = (string) array_shift($match);
                $placeholders[$placeholder] = $match;
            }
        }
        return $placeholders;
    }

    /**
     * Processes the match of the regular expression.
     * @param string $locale The locale the translator is currently running on, e.g. "en".
     * @param array<string> $values The values matched by the regular expression as 0-indexed array.
     * @param array<mixed> $parameters The additional parameters of the localised string.
     * @return string|null The replacement for the match, or null to keep the match as-is.
     */
    abstract protected function processMatch(string $locale, array $values, array $parameters): ?string;
}
