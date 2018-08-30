<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\TestCase\Encoding;

use MensBeam\Intl\Encoding\UTF16LE;
use MensBeam\Intl\Encoding\UTF16BE;

class TestUTF16BE extends TestUTF16LE {
    protected $testedClass = UTF16BE::class;
    /*
        Char 0  U+007A   (2 byte)  Offset 0
        Char 1  U+00A2   (2 bytes) Offset 2
        Char 2  U+6C34   (2 bytes) Offset 4
        Char 3  U+1D11E  (4 bytes) Offset 6
        Char 4  U+F8FF   (2 bytes) Offset 10
        Char 5  U+10FFFD (4 bytes) Offset 12
        Char 6  U+FFFE   (2 bytes) Offset 16
        End of string at char 7, offset 18
    */
    protected $seekString = "007A 00A2 6C34 D834DD1E F8FF DBFFDFFD FFFE";
    protected $seekCodes = [0x007A, 0x00A2, 0x6C34, 0x1D11E, 0xF8FF, 0x10FFFD, 0xFFFE];
    protected $seekOffsets = [0, 2, 4, 6, 10, 12, 16, 18];
    /* This string contains an invalid character sequence sandwiched between two null characters */
    protected $brokenChar = "0000 DC00 0000";
    protected $lowerA = "\x00a";

    public function provideStrings() {
        foreach (parent::provideStrings() as $name => $test) {
            list($string, $codes) = $test;
            $words = explode(" ", $string);
            foreach($words as $a => $word) {
                if (strlen($word) == 4) {
                    $words[$a] = $word[2].$word[3].$word[0].$word[1];
                }
            }
            $string = implode(" ",$words);
            yield $name => [$string, $codes];
        }
    }
}
