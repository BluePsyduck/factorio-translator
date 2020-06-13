<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Placeholder;

/**
 * The abstract class handling the control placeholders like __CONTROL__build__.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractControlPlaceholderHandler extends AbstractRegexPlaceholder
{
    public function __construct()
    {
        parent::__construct('#__CONTROL__(.*)__#U');
    }

    /**
     * @param string $locale
     * @param array<string>|string[] $values
     * @param array<mixed> $parameters
     * @return string|null
     */
    protected function process(string $locale, array $values, array $parameters): ?string
    {
        return $this->processControl($locale, $values[0]);
    }

    abstract protected function processControl(string $locale, string $controlName): ?string;
}
