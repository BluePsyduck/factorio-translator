<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Placeholder;

use BluePsyduck\FactorioTranslator\Storage;
use BluePsyduck\FactorioTranslator\StorageAwareInterface;

/**
 * The class handling item placeholders like __ITEM__electronic-circuit__.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ItemPlaceholderHandler extends AbstractRegexPlaceholder implements StorageAwareInterface
{
    protected Storage $storage;

    public function __construct()
    {
        parent::__construct('#__ITEM__(.*)__#U');
    }

    public function setStorage(Storage $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * @param string $locale
     * @param string $value
     * @param array<mixed> $parameters
     * @return string|null
     */
    protected function process(string $locale, string $value, array $parameters): ?string
    {
        return $this->storage->get($locale, 'item-name', $value);
    }
}
