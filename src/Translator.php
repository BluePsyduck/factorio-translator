<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator;

use BluePsyduck\FactorioTranslator\Exception\NoSupportedLoaderException;
use BluePsyduck\FactorioTranslator\Loader\LoaderInterface;
use BluePsyduck\FactorioTranslator\Placeholder\PlaceholderHandlerInterface;

/**
 * The main translator class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Translator
{
    protected Storage $storage;

    /**
     * @var array<LoaderInterface>|LoaderInterface[]
     */
    protected array $loaders;

    /**
     * @var array<PlaceholderHandlerInterface>|PlaceholderHandlerInterface[]
     */
    protected array $placeholderHandlers = [];

    /**
     * @param Storage $storage
     * @param array<LoaderInterface>|LoaderInterface[] $loaders
     */
    public function __construct(Storage $storage, array $loaders)
    {
        $this->storage = $storage;
        $this->loaders = $loaders;

        foreach ($this->loaders as $loader) {
            $this->initialize($loader);
        }
    }

    protected function initialize(object $instance): void
    {
        if ($instance instanceof StorageAwareInterface) {
            $instance->setStorage($this->storage);
        }
        if ($instance instanceof TranslatorAwareInterface) {
            $instance->setTranslator($this);
        }
    }

    public function addPlaceholderHandler(PlaceholderHandlerInterface $handler): void
    {
        $this->initialize($handler);
        $this->placeholderHandlers[] = $handler;
    }

    /**
     * @param string $path
     * @throws NoSupportedLoaderException
     */
    public function loadMod(string $path): void
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($path)) {
                $loader->load($path);
                return;
            }
        }

        throw new NoSupportedLoaderException($path);
    }

    /**
     * @param string $locale
     * @param mixed $localisedString
     * @return string
     */
    public function translate(string $locale, $localisedString): string
    {
        if (!is_array($localisedString)) {
            return (string) $localisedString;
        }

        $firstValue = array_shift($localisedString);
        if ($firstValue === '') {
            return $this->concatenate($locale, $localisedString);
        }

        return $this->doTranslate($locale, $firstValue, $localisedString);
    }

    /**
     * @param string $locale
     * @param array<mixed> $parts
     * @return string
     */
    protected function concatenate(string $locale, array $parts): string
    {
        $values = [];
        foreach ($parts as $part) {
            $values[] = $this->translate($locale, $part);
        }
        return implode($values);
    }

    /**
     * @param string $locale
     * @param string $key
     * @param array<mixed> $parameters
     * @return string
     */
    protected function doTranslate(string $locale, string $key, array $parameters): string
    {
        [$section, $name] = explode('.', $key);
        $result = $this->storage->get($locale, $section, $name);
        $result = $this->replacePlaceholders($locale, $result, $parameters);
        return $result;
    }

    /**
     * @param string $locale
     * @param string $string
     * @param array<mixed> $parameters
     * @return string
     */
    public function replacePlaceholders(string $locale, string $string, array $parameters): string
    {
        foreach ($this->placeholderHandlers as $handler) {
            $string = $handler->handle($locale, $string, $parameters);
        }
        return $string;
    }
}
