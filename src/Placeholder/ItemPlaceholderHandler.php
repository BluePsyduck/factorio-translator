<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Placeholder;

use BluePsyduck\FactorioTranslator\TranslatorAwareInterface;
use BluePsyduck\FactorioTranslator\TranslatorAwareTrait;

/**
 * The class handling item placeholders like __ITEM__electronic-circuit__.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ItemPlaceholderHandler extends AbstractRegexPlaceholder implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    public function __construct()
    {
        parent::__construct('#__ITEM__(.*)__#U');
    }

    /**
     * @param string $locale
     * @param array<string>|string[] $values
     * @param array<mixed> $parameters
     * @return string|null
     */
    protected function process(string $locale, array $values, array $parameters): ?string
    {
        return $this->translator->translateWithFallback($locale, ["item-name.{$values[0]}"]);
    }
}
