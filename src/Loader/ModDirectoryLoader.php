<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Loader;

/**
 * The loader for already extracted mod directories.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModDirectoryLoader extends AbstractLoader implements LoaderInterface
{
    protected const GLOB_PATTERN = 'locale/*/*.cfg';

    public function supports(string $path): bool
    {
        return is_dir($path);
    }

    public function load(string $path): void
    {
        $files = $this->findFiles($path);
        foreach ($files as $file) {
            $parts = explode('/', $file);
            $locale = $parts[count($parts) - 2];
            $this->parseContents($locale, (string) file_get_contents($file));
        }
    }

    /**
     * @param string $path
     * @return array<string>|string[]
     */
    protected function findFiles(string $path): array
    {
        $result = glob($path . '/' . self::GLOB_PATTERN);
        return ($result === false) ? [] : $result;
    }
}
