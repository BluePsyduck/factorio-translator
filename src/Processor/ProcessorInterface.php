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
     * @param string $locale
     * @param string $string
     * @param array<mixed> $parameters
     * @return string
     */
    public function process(string $locale, string $string, array $parameters): string;
}
