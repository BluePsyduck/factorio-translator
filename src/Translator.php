<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator;

use BluePsyduck\FactorioTranslator\Exception\NoSupportedLoaderException;
use BluePsyduck\FactorioTranslator\Loader\LoaderInterface;
use BluePsyduck\FactorioTranslator\Processor\ProcessorInterface;

/**
 * The main translator class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Translator
{
    protected const FALLBACK_LOCALE = 'en';

    protected Storage $storage;

    /**
     * @var array<LoaderInterface>|LoaderInterface[]
     */
    protected array $loaders;

    /**
     * @var array<ProcessorInterface>|ProcessorInterface[]
     */
    protected array $processors = [];

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Adds a mod loader to the translator, to load the locale files of mods.
     * @param LoaderInterface $loader
     * @return $this
     */
    public function addLoader(LoaderInterface $loader): self
    {
        $this->initialize($loader);
        $this->loaders[] = $loader;
        return $this;
    }

    /**
     * Adds a text processor to the translator, which will be called after a string has been translated.
     * @param ProcessorInterface $processor
     * @return $this
     */
    public function addProcessor(ProcessorInterface $processor): self
    {
        $this->initialize($processor);
        $this->processors[] = $processor;
        return $this;
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

    /**
     * @param string $path
     * @return $this
     * @throws NoSupportedLoaderException
     */
    public function loadMod(string $path): self
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($path)) {
                $loader->load($path);
                return $this;
            }
        }

        throw new NoSupportedLoaderException($path);
    }

    /**
     * Translates the localised string into the specified locale.
     * @param string $locale The locale to use for the translation.
     * @param mixed $localisedString The localised string to translate.
     * @return string The translated string, or an empty string if no translation is available.
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
     * Translates the localised string into the specified locale, or falls back to English if the locale does not
     * have a translation.
     * @param string $locale The locale to use for the translation.
     * @param mixed $localisedString The localised string to translate.
     * @return string The translated string, or an empty string if no translation is available.
     */
    public function translateWithFallback(string $locale, $localisedString): string
    {
        $result = $this->translate($locale, $localisedString);
        if ($result === '' && $locale !== self::FALLBACK_LOCALE) {
            $result = $this->translate(self::FALLBACK_LOCALE, $localisedString);
        }
        return $result;
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
            $values[] = $this->translateWithFallback($locale, $part);
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
        if (strpos($key, '.') === false) {
            return '';
        }

        [$section, $name] = explode('.', $key);
        if (!$this->storage->has($locale, $section, $name)) {
            return '';
        }

        $result = $this->storage->get($locale, $section, $name);
        $result = $this->applyProcessors($locale, $result, $parameters);
        return $result;
    }

    /**
     * Applies all processors added to the translator to the string.
     * @param string $locale The locale to use. This locale will be passed to the processors for further translations.
     * @param string $string The string to process.
     * @param array<mixed> $parameters The additional parameters from the original localised string.
     * @return string The processed string.
     */
    public function applyProcessors(string $locale, string $string, array $parameters): string
    {
        foreach ($this->processors as $handler) {
            $string = $handler->process($locale, $string, $parameters);
        }
        return $string;
    }
}
