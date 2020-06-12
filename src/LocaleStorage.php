<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator;

/**
 * The storage of all the translated values of the locale files.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class LocaleStorage
{
    /**
     * @var array<string,array<string,array<string,string>>>|string[][][]
     */
    protected array $values = [];

    public function set(string $locale, string $section, string $name, string $value): void
    {
        $this->values[$locale][$section][$name] = $value;
    }

    public function get(string $locale, string $section, string $name): string
    {
        return $this->values[$locale][$section][$name] ?? '';
    }
}
