<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Processor\Placeholder;

use BluePsyduck\FactorioTranslator\Processor\AbstractRegexProcessor;
use BluePsyduck\FactorioTranslator\TranslatorAwareInterface;
use BluePsyduck\FactorioTranslator\TranslatorAwareTrait;

/**
 * The class processing the plural form placeholders.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class PluralPlaceholderProcessor extends AbstractRegexProcessor implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    public function __construct()
    {
        parent::__construct('#__plural_for_parameter_(\d+)_\{(.*)\}__#U');
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
        $parameter = (int) $parameters[$position - 1];

        return $this->processConditions($locale, $values[1], $parameter);
    }

    protected function processConditions(string $locale, string $conditions, int $number): ?string
    {
        foreach (explode('|', $conditions) as $condition) {
            if (strpos($condition, '=') === false) {
                continue;
            }

            [$cases, $string] = explode('=', $condition, 2);
            foreach (explode(',', $cases) as $case) {
                if ($this->evaluateCondition($case, $number)) {
                    return $this->translator->applyProcessors($locale, $string, []);
                }
            }
        }

        return null;
    }

    protected function evaluateCondition(string $condition, int $number): bool
    {
        // "rest" matches everything, representing the else case.
        if ($condition === 'rest') {
            return true;
        }

        // Numeric values must be matched exactly.
        if (preg_match('#^\d+$#', $condition) > 0) {
            return ((int) $condition) === $number;
        }

        // "ends in" defines a suffix to match.
        if (preg_match('#^ends in (\d+)$#', $condition, $match) > 0) {
            $suffix = $match[1];
            return substr((string) $number, -strlen($suffix)) === $suffix;
        }

        return false;
    }
}
