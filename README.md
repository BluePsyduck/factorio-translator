# Factorio Translator Library

[![Latest Stable Version](https://poser.pugx.org/bluepsyduck/factorio-translator/v/stable)](https://packagist.org/packages/bluepsyduck/factorio-translator) 
[![License](https://poser.pugx.org/bluepsyduck/factorio-translator/license)](https://packagist.org/packages/bluepsyduck/factorio-translator) 
[![Build Status](https://travis-ci.com/BluePsyduck/factorio-translator.svg?branch=master)](https://travis-ci.com/BluePsyduck/factorio-translator) 
[![codecov](https://codecov.io/gh/BluePsyduck/factorio-translator/branch/master/graph/badge.svg)](https://codecov.io/gh/BluePsyduck/factorio-translator)

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

## Initialization

Before the translator can be used, it requires some initialization, so it knows what it actually should do.

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

### 3. Add text processors

The translator uses text processors to process translated strings further. An example of such processing is to replace
special references in the string with their actual values. While the translator ships with some basic processors, it may
be required to implement your own processors to get all the features out of the translators.

#### Standard processors

The translator comes with some standard processors. It is recommended to always add these to the translator, as they
handle very basic features of localised strings.

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

$translator->addProcessor(new class extends AbstractControlPlaceholderProcessor implements TranslatorAwareInterface {
    use TranslatorAwareTrait;

    protected function processControl(string $locale, string $controlName, int $version): ?string
    {
        // Use the translated name of the control (as it appears in the options menu), and put it into square brackets. 
        return '[' . $this->translator->translateWithFallback($locale, ["controls.{$controlName}"]) . ']';
    }
});
```

## Usage

TBC

## Further reading

Further information and documentations on how the localised strings and the translation system works in Factorio can be 
found on the following websites:

- Factorio API: [LocalisedString](https://lua-api.factorio.com/latest/Concepts.html#LocalisedString)
- Factorio wiki: [Tutorial:Localisation](https://wiki.factorio.com/Tutorial:Localisation)
- Factorio wiki: [Rich text](https://wiki.factorio.com/Rich_text)
