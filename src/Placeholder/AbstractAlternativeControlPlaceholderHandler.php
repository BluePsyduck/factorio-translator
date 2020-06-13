<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Placeholder;

/**
 * The abstract class handling the control placeholders like __ALT_CONTROL__1__build__.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractAlternativeControlPlaceholderHandler extends AbstractRegexPlaceholder
{
    public function __construct()
    {
        parent::__construct('#__ALT_CONTROL__(\d+)__(.*)__#U');
    }

    /**
     * @param string $locale
     * @param array<string>|string[] $values
     * @param array<mixed> $parameters
     * @return string|null
     */
    protected function process(string $locale, array $values, array $parameters): ?string
    {
        [$version, $name] = $values;
        return $this->processControl($locale, $name, (int) $version);
    }

    abstract protected function processControl(string $locale, string $controlName, int $version): ?string;
}
