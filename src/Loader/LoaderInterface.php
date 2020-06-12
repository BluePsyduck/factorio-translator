<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Loader;

/**
 * The interface of the loaders.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface LoaderInterface
{
    public function supports(string $path): bool;
    public function load(string $path): void;
}
