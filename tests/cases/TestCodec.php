<?php
/** @license MIT
 * Copyright 2017 J. King, Dustin Wilson et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\UTF8\TestCase\Codec;

use MensBeam\UTF8\UTF8;

/** @covers \MensBeam\UTF8\UTF8 */
class TestConf extends \PHPUnit\Framework\TestCase {

    /** @group optional */
    public function testDecodeSingleCharacter() {
        for ($a = 0; $a <= 0x10FFFF; $a++) {
            // the UTF-8 encoding of the code point
            $bytes = \IntlChar::chr($a);
            // the expected result of decoding the bytes: surrogates are supposed to result in failures on every byte
            $exp1 = ($a >= 55296 && $a <= 57343) ? array_fill(0, strlen($bytes), false) : [$a];
            // the expected next-character poisitions: surrogates are supposed to return multiple positions; others always return only the end of the string
            $exp2 = ($a >= 55296 && $a <= 57343) ? range(1, strlen($bytes)) : [strlen($bytes)];
            $act1 = [];
            $act2 = [];
            $pos = 0;
            do {
                $act1[] = UTF8::ord($bytes, $pos, $pos);
                $act2[] = $pos;
            } while ($pos < strlen($bytes));
            $this->assertSame($exp1, $act1, 'Character '.strtoupper(bin2hex(\IntlChar::chr($a))).' was not decoded correctly.');
            $this->assertSame($exp2, $act2, 'Next offset for character '.strtoupper(bin2hex(\IntlChar::chr($a))).' is incorrect.');
        }
    }
    
    /** @dataProvider provideStrings */
    public function testDecodeMultipleCharacters(string $input, array $exp) {
        $pos = 0;
        $out = [];
        $eof = strlen($input);
        while ($pos < $eof) {
            $p = UTF8::ord($input, $pos, $pos);
            $out[] = is_int($p) ? $p : 0xFFFD;
        }
        $this->assertEquals($exp, $out);
    }

    public function provideStrings() {
        return [
            'sanity check' => ["\x61\x62\x63\x31\x32\x33", [97, 98, 99, 49, 50, 51]],
            'multibyte control' => ["\xE5\x8F\xA4\xE6\xB1\xA0\xE3\x82\x84\xE8\x9B\x99\xE9\xA3\x9B\xE3\x81\xB3\xE8\xBE\xBC\xE3\x82\x80\xE6\xB0\xB4\xE3\x81\xAE\xE9\x9F\xB3", [21476, 27744, 12420, 34521, 39131, 12403, 36796, 12416, 27700, 12398, 38899]],
            'invalid code' => ["\xFF", [65533]],
            'ends early' => ["\xC0", [65533]],
            'ends early 2' => ["\xE0", [65533]],
            'invalid trail' => ["\xC0\x00", [65533, 0]],
            'invalid trail 2' => ["\xC0\xC0", [65533, 65533]],
            'invalid trail 3' => ["\xE0\x00", [65533, 0]],
            'invalid trail 4' => ["\xE0\xC0", [65533, 65533]],
            'invalid trail 5' => ["\xE0\x80\x00", [65533, 65533, 0]],
            'invalid trail 6' => ["\xE0\x80\xC0", [65533, 65533, 65533]],
            '> 0x10FFFF' => ["\xFC\x80\x80\x80\x80\x80", [65533, 65533, 65533, 65533, 65533, 65533]],
            'obsolete lead byte' => ["\xFE\x80\x80\x80\x80\x80", [65533, 65533, 65533, 65533, 65533, 65533]],
            'overlong U+0000 - 2 bytes' => ["\xC0\x80", [65533, 65533]],
            'overlong U+0000 - 3 bytes' => ["\xE0\x80\x80", [65533, 65533, 65533]],
            'overlong U+0000 - 4 bytes' => ["\xF0\x80\x80\x80", [65533, 65533, 65533, 65533]],
            'overlong U+0000 - 5 bytes' => ["\xF8\x80\x80\x80\x80", [65533, 65533, 65533, 65533, 65533]],
            'overlong U+0000 - 6 bytes' => ["\xFC\x80\x80\x80\x80\x80", [65533, 65533, 65533, 65533, 65533, 65533]],
            'overlong U+007F - 2 bytes' => ["\xC1\xBF", [65533, 65533]],
            'overlong U+007F - 3 bytes' => ["\xE0\x81\xBF", [65533, 65533, 65533]],
            'overlong U+007F - 4 bytes' => ["\xF0\x80\x81\xBF", [65533, 65533, 65533, 65533]],
            'overlong U+007F - 5 bytes' => ["\xF8\x80\x80\x81\xBF", [65533, 65533, 65533, 65533, 65533]],
            'overlong U+007F - 6 bytes' => ["\xFC\x80\x80\x80\x81\xBF", [65533, 65533, 65533, 65533, 65533, 65533]],
            'overlong U+07FF - 3 bytes' => ["\xE0\x9F\xBF", [65533, 65533, 65533]],
            'overlong U+07FF - 4 bytes' => ["\xF0\x80\x9F\xBF", [65533, 65533, 65533, 65533]],
            'overlong U+07FF - 5 bytes' => ["\xF8\x80\x80\x9F\xBF", [65533, 65533, 65533, 65533, 65533]],
            'overlong U+07FF - 6 bytes' => ["\xFC\x80\x80\x80\x9F\xBF", [65533, 65533, 65533, 65533, 65533, 65533]],
            'overlong U+FFFF - 4 bytes' => ["\xF0\x8F\xBF\xBF", [65533, 65533, 65533, 65533]],
            'overlong U+FFFF - 5 bytes' => ["\xF8\x80\x8F\xBF\xBF", [65533, 65533, 65533, 65533, 65533]],
            'overlong U+FFFF - 6 bytes' => ["\xFC\x80\x80\x8F\xBF\xBF", [65533, 65533, 65533, 65533, 65533, 65533]],
            'overlong U+10FFFF - 5 bytes' => ["\xF8\x84\x8F\xBF\xBF", [65533, 65533, 65533, 65533, 65533]],
            'overlong U+10FFFF - 6 bytes' => ["\xFC\x80\x84\x8F\xBF\xBF", [65533, 65533, 65533, 65533, 65533, 65533]],
            'lead surrogate' => ["\xED\xA0\x80", [65533, 65533, 65533]],
            'trail surrogate' => ["\xED\xB0\x80", [65533, 65533, 65533]],
            'surrogate pair' => ["\xED\xA0\x80\xED\xB0\x80", [65533, 65533, 65533, 65533, 65533, 65533]],
            'mixed sample' => ["\x7A\xC2\xA2\xE6\xB0\xB4\xF0\x9D\x84\x9E\xEF\xA3\xBF\xF4\x8F\xBF\xBD\xEF\xBF\xBE", [122, 162, 27700, 119070, 63743, 1114109, 65534]],
        ];
    }
}
