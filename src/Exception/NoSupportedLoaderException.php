<?php

declare(strict_types=1);

namespace BluePsyduck\FactorioTranslator\Exception;

use Exception;
use Throwable;

/**
 * The exception thrown when no supported loader was found for a mod.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class NoSupportedLoaderException extends Exception
{
    protected const MESSAGE_TEMPLATE = 'No supported loader found for %s';

    public function __construct(string $path, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE_TEMPLATE, $path), 0, $previous);
    }
}
