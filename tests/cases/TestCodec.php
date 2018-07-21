<?php
/** @license MIT
 * Copyright 2017 J. King, Dustin Wilson et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\UTF8\TestCase\Codec;

use MensBeam\UTF8\UTF8String;

class TestConf extends \PHPUnit\Framework\TestCase {
    
    /** 
     * @dataProvider provideStrings
     * @covers \MensBeam\UTF8\UTF8String::__construct
     * @covers \MensBeam\UTF8\UTF8String::nextOrd
    */
    public function testDecodeMultipleCharactersAsCodePoints(string $input, array $exp) {
        $s = new UTF8String($input);
        while (($p = $s->nextOrd()) !== false) {
            $out[] = $p ?? 0xFFFD;
        }
        $this->assertEquals($exp, $out);
    }
    
    /** 
     * @dataProvider provideStrings
     * @covers \MensBeam\UTF8\UTF8String::__construct
     * @covers \MensBeam\UTF8\UTF8String::nextChr
    */
    public function testDecodeMultipleCharactersAsStrings(string $input, array $exp) {
        $exp = array_map(function($v) {
            return \IntlChar::chr($v);
        }, $exp);
        $s = new UTF8String($input);
        while (($c = $s->nextChr()) !== "") {
            $out[] = $c;
        }
        $this->assertEquals($exp, $out);
    }
    
    /** 
     * @dataProvider provideStrings
     * @covers \MensBeam\UTF8\UTF8String::seek
     * @covers \MensBeam\UTF8\UTF8String::sync
     * @covers \MensBeam\UTF8\UTF8String::posChar
    */
    public function testSTepBackThroughAString(string $input, array $points) {
        $s = new UTF8String($input);
        $a = 0;
        while (($p1 = $s->nextOrd() ?? 0xFFFD) !== false) {
            $this->assertTrue($s->seek(-1));
            $p2 = $s->nextOrd() ?? 0xFFFD;
            $this->assertSame($p1, $p2, "Mismatch at character position $a");
            $this->assertSame(++$a, $s->posChar(), "Character position should be $a");
        }
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
