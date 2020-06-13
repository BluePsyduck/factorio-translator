<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator;

/**
 * The trait implementing the StorageAwareInterface.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
trait StorageAwareTrait
{
    protected Storage $storage;

    public function setStorage(Storage $storage): void
    {
        $this->storage = $storage;
    }
}
