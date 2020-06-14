<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Placeholder;

/**
 * The abstract class for placeholders using a regular expression to match.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractRegexPlaceholder implements PlaceholderHandlerInterface
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
    public function handle(string $locale, string $string, array $parameters): string
    {
        $placeholders = $this->findPlaceholders($string);
        foreach ($placeholders as $placeholder => $values) {
            $replacement = $this->process($locale, $values, $parameters);
            if ($replacement !== null) {
                $string = str_replace($placeholder, $replacement, $string);
            }
        }
        return $string;
    }

    /**
     * @param string $string
     * @return array<string,array<string>>|string[][]
     */
    protected function findPlaceholders(string $string): array
    {
        $placeholders = [];
        if (preg_match_all($this->pattern, $string, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $placeholder = array_shift($match);
                $placeholders[$placeholder] = $match;
            }
        }
        return $placeholders;
    }

    /**
     * @param string $locale
     * @param array<string>|string[] $values
     * @param array<mixed> $parameters
     * @return string|null
     */
    abstract protected function process(string $locale, array $values, array $parameters): ?string;
}
