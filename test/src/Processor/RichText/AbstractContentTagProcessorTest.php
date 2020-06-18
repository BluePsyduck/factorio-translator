<?php

declare(strict_types=1);

namespace BluePsyduckTest\FactorioTranslator\Processor\RichText;

use BluePsyduck\FactorioTranslator\Processor\RichText\AbstractContentTagProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the AbstractContentTagProcessor class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \BluePsyduck\FactorioTranslator\Processor\RichText\AbstractContentTagProcessor
 */
class AbstractContentTagProcessorTest extends TestCase
{
    /**
     * @covers ::process
     */
    public function testProcess(): void
    {
        $locale = 'foo';
        $string =  'abc [color=red] def [font=bold] ghi [.font] jkl [/color] mno';
        $parameters = ['bar'];

        /* @var AbstractContentTagProcessor&MockObject $processor */
        $processor = $this->getMockBuilder(AbstractContentTagProcessor::class)
                          ->onlyMethods(['processTag'])
                          ->getMockForAbstractClass();
        $processor->expects($this->exactly(2))
                  ->method('processTag')
                  ->withConsecutive(
                      [
                          $this->identicalTo($locale),
                          $this->identicalTo('font'),
                          $this->identicalTo('bold'),
                          $this->identicalTo(' ghi ')
                      ],
                      [
                          $this->identicalTo($locale),
                          $this->identicalTo('color'),
                          $this->identicalTo('red'),
                          $this->identicalTo(' def pqr jkl ')
                      ],
                  )
                  ->willReturnOnConsecutiveCalls(
                      'pqr',
                      'stu',
                  );

        $result = $processor->process($locale, $string, $parameters);

        $this->assertSame('abc stu mno', $result);
    }

    /**
     * @covers ::process
     */
    public function testProcessWithMismatchedTags(): void
    {
        $locale = 'foo';
        $string =  'abc [color=red] def [font=bold] ghi [.color] jkl [/font] mno';
        $parameters = ['bar'];

        /* @var AbstractContentTagProcessor&MockObject $processor */
        $processor = $this->getMockBuilder(AbstractContentTagProcessor::class)
                          ->onlyMethods(['processTag'])
                          ->getMockForAbstractClass();
        $processor->expects($this->once())
                  ->method('processTag')
                  ->with(
                      $this->identicalTo($locale),
                      $this->identicalTo('font'),
                      $this->identicalTo('bold'),
                      $this->identicalTo(' ghi [.color] jkl ')
                  )
                  ->willReturn(null);

        $result = $processor->process($locale, $string, $parameters);

        $this->assertSame('abc [color=red] def [font=bold] ghi [.color] jkl [/font] mno', $result);
    }
}
