# Factorio Translator Library

[![GitHub release (latest SemVer)](https://img.shields.io/github/v/release/BluePsyduck/factorio-translator)](https://github.com/BluePsyduck/factorio-translator/releases)
[![GitHub](https://img.shields.io/github/license/BluePsyduck/factorio-translator)](LICENSE.md)
[![build](https://img.shields.io/github/workflow/status/BluePsyduck/factorio-translator/CI?logo=github)](https://github.com/BluePsyduck/factorio-translator/actions)
[![Codecov](https://img.shields.io/codecov/c/gh/BluePsyduck/factorio-translator?logo=codecov)](https://codecov.io/gh/BluePsyduck/factorio-translator)

The Factorio Translator Library is a library implementing the translator of Factorio in PHP. Given the locale files 
shipped with the mods it is able to translate the localised strings used by Factorio into any locale provided by the 
mods.

## Features

- Translate any localised string to any locale, providing the locale files of mods.
- Load mods either in their archive format (e.g. downloaded from the Factorio mod portal), or an already extracted
  directory (as are the core and base mods from Factorio itself).  
- Resolve positional parameters `__1__` from the localised strings.
- Resolve simple references like `__ITEM__electronic-circuit__` and `__ENTITY__iron-ore__` by replacing them with their
  translated name.
- Resolve plural forms like `__plural_for_parameter_1_{1=hour|rest=hours}__`.
- Process control references like `__CONTROL__build__` and `__ALT_CONTROL__1__build__` by providing an abstract class
  for implementation.
- Process RichText tags like `[item=electronic-circuit]` and `[color=red]text[/color]` by providing abstract classes for
  implementation.
  
## Installation

The library is available through composer. Install the package using the following command:

```
composer require bluepsyduck/factorio-translator
```

## Usage

The translator requires some setup steps before it can actually be used. Otherwise it won't do much at all.

### 1. Create instance

Creating the actual instance is straight forward: It does not need any dependencies:

```php
use BluePsyduck\FactorioTranslator\Translator;

$translator = new Translator();
```

### 2. Add mod loaders

To be able to actually add mods (or rather their locale files) to the translator, mod loaders must be added first.

```php
use BluePsyduck\FactorioTranslator\Loader\ModArchiveLoader;
use BluePsyduck\FactorioTranslator\Loader\ModDirectoryLoader;

$translator->addLoader(new ModDirectoryLoader())
           ->addLoader(new ModArchiveLoader());
```

The library ships with two loaders, covering the most common use cases:

- **ModArchiveLoader:** Loads the mods from the zipped archive. This loader can be used to load any mods downloaded
  from the Factorio mod portal.
- **ModDirectoryLoader:** Loads mods from a directory. This loader can be used to load the `core` and `base` mods from
  the Factorio game itself, as they are shipped uncompressed with the game. 

### 3. Load the mods

After the loaders have been added, it is now time to load the mods to use for the translations. Simply add the paths
to the mods to the translator, and the loaders will take care of the rest:

```php

$translator->loadMod('/path/to/factorio/data/core')
           ->loadMod('/path/to/factorio/data/base')
           ->loadMod('/path/to/factorio/mods/my-fancy-mod_1.33.7.zip');
```

Some notes on loading mods:

- The `core` mod from Factorio should always be loaded first, and the `base` mod should always be loaded second. Not 
  doing so may lead to missing translations.
- The order of the mods actually matters: If two mods provide a translation for the same key, the later one added will 
  win. This is the same is Factorio would do it. Ideally all mods get added in the same order as Factorio loads them.

### 4. Add text processors

The translator uses text processors to process translated strings further. An example of such processing is to replace
special references in the string with their actual values. While the translator ships with some basic processors, it may
be required to implement your own processors to get all the features out of the translators.

#### Standard processors

The translator comes with some standard processors. It is recommended to always add these to the translator, as they
handle very basic features of localised strings.

- **PositionPlaceholderProcessor:** Handles position references for parameters like `__1__`.
- **EntityPlaceholderProcessor:** Handles entity references like `__ENTITY__iron-ore__`, replacing it with the 
  translated name of the entity.
- **ItemPlaceholderProcessor:** Handles item references like `__ITEM__electronic-circuit__`, replacing it with the
  translated name of the item.
- **PluralPlaceholderProcessor:** Handles the special plural form syntax of Factorio, like 
  `__plural_for_parameter_1_{1=hour|rest=hours}__`. 

Add these processors as following:

```php
use BluePsyduck\FactorioTranslator\Processor\Placeholder\PositionPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Processor\Placeholder\EntityPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Processor\Placeholder\ItemPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\Processor\Placeholder\PluralPlaceholderProcessor;

$translator->addProcessor(new PositionPlaceholderProcessor()) // Handles e.g. __1__
           ->addProcessor(new EntityPlaceholderProcessor())   // Handles e.g. __ENTITY__iron-ore__
           ->addProcessor(new ItemPlaceholderProcessor())     // Handles e.g. __ITEM_electronic-circuit__
           ->addProcessor(new PluralPlaceholderProcessor());  // Handles e.g. __plural_for_parameter_1_{...}__
```

#### Advanced processors

The translator also provides some abstract processor classes, which needs further implementation before they can 
actually be used. These abstract processors include:

- **AbstractControlPlaceholderProcessor:** Handles placeholders like `__CONTROL__build__` and 
  `__ALT_CONTROL__1__build__`. A derived class must provide the replacement of the control.
- **AbstractStandaloneTagProcessor:** Handles simple RichText tags without content, e.g. `[item=electronic-circuit]`. 
  The abstract class handles parsing of the tags (but not of the tag value), and the derived class must provide the
  replacement for the tag.
- **AbstractContentTagProcessor:** Handles complex RichText tags, having content between them, like `[color=red]foo[/color]`
  and `[font=bold]foo[/font]`. The abstract class already handles parsing the string for the tags, and the derived 
  class must provide the replacement for them, e.g. HTML format.

The following example shows how to implement and use the control placeholder processor:

```php
use BluePsyduck\FactorioTranslator\Processor\Placeholder\AbstractControlPlaceholderProcessor;
use BluePsyduck\FactorioTranslator\TranslatorAwareInterface;
use BluePsyduck\FactorioTranslator\TranslatorAwareTrait;

$translator->addProcessor(
    new class extends AbstractControlPlaceholderProcessor implements TranslatorAwareInterface {
        use TranslatorAwareTrait;

        protected function processControl(string $locale, string $controlName, int $version): ?string
        {
            // Use the translated name of the control (as it appears in the options menu), 
            // and put it into square brackets. 
            $control = $this->translator->translateWithFallback($locale, ["controls.{$controlName}"]);
            return "[{$control}]"; 
        }
    }
);
```

### 5. Usage

After all these steps, it is time to actually use the translator to translate localised strings. This is rather simple:

```php
echo $translator->translate('en', ['item-name.electronic-circuit']); // Electronic circuit
echo $translator->translate('de', ['item-name.electronic-circuit']); // Elektronischer Schaltkreis
```

The first parameter is the locale to translate the localised string into. The values are the same as used by Factorio.
A list of all locales can be obtained by calling `$translator->getAllLocales()`.

If a translation is not available, `translate()` will return an empty string. If you want to fallback to English 
instead, use the method `translateWithFallback()` instead.

Note that the localised strings must be specified in PHP syntax, i.e. the lua tables must be transformed to PHP arrays.
The translator does not understand the lua syntax.

## Further reading

Further information and documentations on how the localised strings and the translation system works in Factorio can be 
found on the following websites:

- Factorio API: [LocalisedString](https://lua-api.factorio.com/latest/Concepts.html#LocalisedString)
- Factorio wiki: [Tutorial:Localisation](https://wiki.factorio.com/Tutorial:Localisation)
- Factorio wiki: [Rich text](https://wiki.factorio.com/Rich_text)
