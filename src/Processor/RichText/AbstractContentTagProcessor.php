<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Processor\RichText;

use BluePsyduck\FactorioTranslator\Processor\ProcessorInterface;

/**
 * The abstract class for standalone tags like [color=red]foo[/color].
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractContentTagProcessor implements ProcessorInterface
{
    protected const PATTERN = '#\[((color|font)=(.+)|([./])(color|font))\]#U';

    /**
     * @param string $locale
     * @param string $string
     * @param array<mixed> $parameters
     * @return string
     */
    public function process(string $locale, string $string, array $parameters): string
    {
        if (preg_match_all(self::PATTERN, $string, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE) > 0) {
            $position = 0;
            $contents = [''];
            $openedTags = [];

            foreach ($matches as $match) {
                $contents[0] .= substr($string, $position, $match[0][1] - $position);
                $position = $match[0][1] + strlen($match[0][0]);

                if (isset($match[5])) {
                    // Closing tag
                    if (count($openedTags) === 0 || $match[5][0] !== $openedTags[0][0]) {
                        // Tag mismatch, ignore closing tag, copy as-is.
                        $contents[0] .= substr($string, $match[0][1], strlen($match[0][0]));
                        continue;
                    }

                    [$name, $value] = array_shift($openedTags);
                    $content = (string) array_shift($contents);
                    $replacement = $this->processTag($locale, $name, $value, $content);
                    if ($replacement === null) {
                        $contents[0] .= "[{$name}={$value}]{$content}[{$match[4][0]}{$name}]";
                    } else {
                        $contents[0] .= $replacement;
                    }
                } else {
                    // Opening tag
                    $name = $match[2][0];
                    $value = $match[3][0];

                    array_unshift($openedTags, [$name, $value]);
                    array_unshift($contents, '');
                }
            }

            // Fix any unmatched opening tags, copying them back to the contents as-is.
            foreach ($openedTags as [$name, $value]) {
                $content = array_shift($contents);
                $contents[0] .= "[{$name}={$value}]{$content}";
            }

            // Copy the end of the string after the last tag.
            $contents[0] .= substr($string, $position);

            return $contents[0];
        }

        return $string;
    }

    /**
     * Processes the tag and its content.
     * @param string $locale The locale the translator is currently running on.
     * @param string $name The name of the tag, e.g. "color".
     * @param string $value The unparsed value of the tag, e.g. "red".
     * @param string $content The content between the opening and closing tags.
     * @return string|null The replacement of the tag, or null to keep both tags as they are.
     */
    abstract protected function processTag(string $locale, string $name, string $value, string $content): ?string;
}
