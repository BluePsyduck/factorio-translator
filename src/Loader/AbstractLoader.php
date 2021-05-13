<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Loader;

use BluePsyduck\FactorioTranslator\StorageAwareInterface;
use BluePsyduck\FactorioTranslator\StorageAwareTrait;

/**
 * The abstract class of the loaders, able to actually parse a locale file.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
abstract class AbstractLoader implements StorageAwareInterface
{
    use StorageAwareTrait;

    protected function parseContents(string $locale, string $contents): void
    {
        $section = '';
        foreach (explode(PHP_EOL, $contents) as $line) {
            $line = trim($line, "\r"); // Remove Windows linebreak remains
            $trimmedLine = trim($line);

            if (substr($trimmedLine, 0, 1) === ';') {
                continue;
            } elseif (strpos($line, '=') !== false) {
                [$name, $value] = explode('=', $line, 2);

                // Fix line breaks to be actual line breaks.
                $value = str_replace('\n', PHP_EOL, $value);

                $this->storage->set($locale, $section, trim($name), $value);
            } elseif (substr($trimmedLine, 0, 1) === '[' && substr($trimmedLine, -1) === ']') {
                $section = substr($trimmedLine, 1, -1);
            }
        }
    }
}
