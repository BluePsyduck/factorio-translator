<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Placeholder;

use BluePsyduck\FactorioTranslator\TranslatorAwareInterface;
use BluePsyduck\FactorioTranslator\TranslatorAwareTrait;

/**
 * The class handling the positional placeholders like __1__.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class PositionPlaceholderHandler extends AbstractRegexPlaceholder implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    public function __construct()
    {
        parent::__construct('#__(\d+)__#');
    }

    /**
     * @param string $locale
     * @param string $value
     * @param array<mixed> $parameters
     * @return string|null
     */
    protected function process(string $locale, string $value, array $parameters): ?string
    {
        $position = (int) $value;
        if (!isset($parameters[$position - 1])) {
            return null;
        }

        return $this->translator->translate($locale, $parameters[$position - 1]);
    }
}
