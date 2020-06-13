<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator;

/**
 * The trait implementing the TranslatorAwareInterface.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
trait TranslatorAwareTrait
{
    protected Translator $translator;

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }
}
