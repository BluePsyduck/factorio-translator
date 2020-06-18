<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\AbstractRegexProcessor;
use BluePsyduck\FactorioTranslator\TranslatorAwareInterface;
use BluePsyduck\FactorioTranslator\TranslatorAwareTrait;

/**
 * The class processing item placeholders like __ITEM__electronic-circuit__.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ItemPlaceholderProcessor extends AbstractRegexProcessor implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    protected const PATTERN = '#__ITEM__(.+)__#U';

    public function __construct()
    {
        parent::__construct(self::PATTERN);
    }

    /**
     * @param string $locale
     * @param array<string>|string[] $values
     * @param array<mixed> $parameters
     * @return string|null
     */
    protected function processMatch(string $locale, array $values, array $parameters): ?string
    {
        return $this->translator->translateWithFallback($locale, ["item-name.{$values[0]}"]);
    }
}
