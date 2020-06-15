<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Processor;

/**
 * The interface of the text processors.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface ProcessorInterface
{
    /**
     * Processes the passed string.
     * @param string $locale The locale the translator is currently running on, e.g. "en".
     * @param string $string The string to process.
     * @param array<mixed> $parameters The additional parameters of the localised string.
     * @return string The processed string.
     */
    public function process(string $locale, string $string, array $parameters): string;
}
