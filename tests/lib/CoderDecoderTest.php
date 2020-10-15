<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Test;

use \MensBeam\Intl\Encoding\Encoder;

abstract class CoderDecoderTest extends DecoderTest {
    public function testEncodeCodePoints(bool $fatal, $input, $exp) {
        $class = $this->testedClass;
        $label = $class::NAME;
        $e = new Encoder($label, $fatal);
        $input = (array) $input;
        if ($exp instanceof \Throwable) {
            $this->expectException(get_class($exp));
            $this->expectExceptionCode($exp->getCode());
        } else {
            $exp = strtolower(str_replace(" ", "", $exp));
        }
        $out = $e->encode($input);
        $this->assertSame($exp, bin2hex($out));
    }

    public function testEncodeCodePointsStatically(bool $fatal, $input, $exp) {
        $class = $this->testedClass;
        if (!method_exists($class, "encode")) {
            $this->assertTrue(true);
            return;
        }
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
