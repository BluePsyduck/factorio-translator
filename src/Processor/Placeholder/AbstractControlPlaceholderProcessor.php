<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\AbstractRegexProcessor;

/**
 * The abstract class processing the control placeholders like __CONTROL__build__ and __ALT_CONTROL__1__build__.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractControlPlaceholderProcessor extends AbstractRegexProcessor
{
    public function __construct()
    {
        parent::__construct('#__(CONTROL|ALT_CONTROL__(\d+))__(.+)__#U');
    }

    /**
     * @param string $locale
     * @param array<string>|string[] $values
     * @param array<mixed> $parameters
     * @return string|null
     */
    protected function processMatch(string $locale, array $values, array $parameters): ?string
    {
        return $this->processControl($locale, $values[2], (int) $values[1]);
    }

    /**
     * Processes the control placeholder.
     * @param string $locale The locale the translator is currently running on, e.g. "en".
     * @param string $controlName The name of the control, e.g. "build".
     * @param int $version The alternative version of the placeholder in case of __ALT_CONTROL__ syntax. 0 if the
     * placeholder was the default __CONTROL__ one.
     * @return string|null The replacement for the placeholder, or null to keep the placeholder as-is.
     */
    abstract protected function processControl(string $locale, string $controlName, int $version): ?string;
}
