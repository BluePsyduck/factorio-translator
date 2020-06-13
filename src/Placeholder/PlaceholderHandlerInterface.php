<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Placeholder;

/**
 * The interface of the placeholder handlers.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface PlaceholderHandlerInterface
{
    /**
     * @param string $locale
     * @param string $string
     * @param array<mixed> $parameters
     * @return string
     */
    public function handle(string $locale, string $string, array $parameters): string;
}
