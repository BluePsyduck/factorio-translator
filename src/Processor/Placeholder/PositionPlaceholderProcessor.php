<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\AbstractRegexProcessor;
use BluePsyduck\FactorioTranslator\TranslatorAwareInterface;
use BluePsyduck\FactorioTranslator\TranslatorAwareTrait;

/**
 * The class processing the positional placeholders like __1__.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class PositionPlaceholderProcessor extends AbstractRegexProcessor implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    protected const PATTERN = '#__(\d+)__#U';

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
        $position = (int) $values[0];
        if (!isset($parameters[$position - 1])) {
            return null;
        }

        return $this->translator->translateWithFallback($locale, $parameters[$position - 1]);
    }
}
