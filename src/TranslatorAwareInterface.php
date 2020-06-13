<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator;

/**
 * The interface signaling awareness of the translator.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface TranslatorAwareInterface
{
    public function setTranslator(Translator $translator): void;
}
