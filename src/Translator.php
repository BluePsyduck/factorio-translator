<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator;

use BluePsyduck\FactorioTranslator\Loader\LoaderInterface;

/**
 * THe main translator class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Translator
{
    protected LocaleStorage $storage;

    /**
     * @var array<LoaderInterface>|LoaderInterface[]
     */
    protected array $loaders;

    /**
     * @param LocaleStorage $storage
     * @param array<LoaderInterface>|LoaderInterface[] $loaders
     */
    public function __construct(LocaleStorage $storage, array $loaders)
    {
        $this->storage = $storage;
        $this->loaders = $loaders;
    }

    public function loadMod(string $path): void
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($path)) {
                $loader->load($path);
                return;
            }
        }

        // @todo throw exception
    }
}
