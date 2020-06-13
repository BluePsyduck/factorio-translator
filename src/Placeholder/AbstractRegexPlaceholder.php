<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Placeholder;

/**
 *
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
        foreach ($placeholders as $placeholder => $value) {
            $replacement = $this->process($locale, $value, $parameters);
            if ($replacement !== null) {
                $string = str_replace($placeholder, $replacement, $string);
            }
        }
        return $string;
    }

    /**
     * @param string $string
     * @return array<string,string>|string[]
     */
    protected function findPlaceholders(string $string): array
    {
        $placeholders = [];
        if (preg_match_all($this->pattern, $string, $matches) > 0) {
            foreach ($matches[0] as $i => $placeholder) {
                $placeholders[$placeholder] = $matches[1][$i];
            }
        }
        return $placeholders;
    }

    /**
     * @param string $locale
     * @param string $value
     * @param array<mixed> $parameters
     * @return string|null
     */
    abstract protected function process(string $locale, string $value, array $parameters): ?string;
}
