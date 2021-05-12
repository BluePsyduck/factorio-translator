<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Exception;

use BluePsyduck\FactorioTranslator\Exception\NoSupportedLoaderException;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the NoSupportedLoaderException class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \BluePsyduck\FactorioTranslator\Exception\NoSupportedLoaderException
 */
class NoSupportedLoaderExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $path = 'abc';
        $expectedMessage = 'No supported loader found for abc';
        $previous = $this->createMock(Exception::class);

        $exception = new NoSupportedLoaderException($path, $previous);

        $this->assertSame($expectedMessage, $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
