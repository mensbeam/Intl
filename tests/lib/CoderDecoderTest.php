<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Test;

use MensBeam\Intl\Encoding\EncoderException;

abstract class CoderDecoderTest extends DecoderTest {
    public function testEncodeCodePoints(bool $fatal, $input, $exp) {
        $class = $this->testedClass;
        if ($exp instanceof \Throwable) {
            $this->expectException(get_class($exp));
            $this->expectExceptionCode($exp->getCode());
        } else {
            $exp = strtolower(str_replace(" ", "", $exp));
        }
        $out = $class::encode($input, $fatal);
        $this->assertSame($exp, bin2hex($out));
    }
}
