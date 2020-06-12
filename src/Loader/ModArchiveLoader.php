<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Loader;

use ZipArchive;

/**
 * The loader for still compressed mod archives.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModArchiveLoader extends AbstractLoader implements LoaderInterface
{
    protected const REGEXP_LOCALE_FILE = '#^[^/]+/locale/([^/]+)/[^/]+\.cfg$#';

    public function supports(string $path): bool
    {
        $archive = new ZipArchive();
        $result = $archive->open($path);
        return $result === true;
    }

    public function load(string $path): void
    {
        $archive = new ZipArchive();
        $archive->open($path);

        for ($i = 0; $i < $archive->numFiles; ++$i) {
            $stat = $archive->statIndex($i);
            if (is_array($stat) && preg_match(self::REGEXP_LOCALE_FILE, $stat['name'], $match) > 0) {
                $locale = $match[1];
                $stream = $archive->getStream($stat['name']);
                if (is_resource($stream)) {
                    $this->parseContents($locale, (string) stream_get_contents($stream));
                }
            }
        }
    }
}
