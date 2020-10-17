<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\TestCase\Encoding;

use MensBeam\Intl\Encoding\ISO2022JP;
use MensBeam\Intl\Encoding\Encoding;
use MensBeam\Intl\Encoding\EncoderException;

class TestISO2022JP extends \MensBeam\Intl\Test\CoderDecoderTest {
    protected $testedClass = ISO2022JP::class;
    /*
        Char 0  U+007A   (1 byte)  Offset 0
        Esc: Katakana    (3 bytes) Offset 1
        Char 1  U+FF9C   (1 byte)  Offset 4
        Char 2  U+FF9F   (1 byte)  Offset 5
        Esc: Double-byte (3 bytes) Offset 6
        Char 3  U+79FB   (2 bytes) Offset 9
        Char 4  U+67B8   (2 bytes) Offset 11
        Char 5  U+9B91   (2 bytes) Offset 13
        Esc: ASCII       (3 bytes) Offset 15
        Char 6  U+007E   (1 byte)  Offset 18
        Esc: Roman       (3 bytes) Offset 19
        End of string at char 7, offset 22
    */
    protected $seekString = "7A 1B2849 5C 5F 1B2440 305C 5B4E 723A 1B2842 7E 1B284A";
    protected $seekCodes = [0x7A, 0xFF9C, 0xFF9F, 0x79FB, 0x67B8, 0x9B91, 0x7E];
    protected $seekOffsets = [0, 1, 5, 6, 11, 13, 15, 19];
    /* This string contains an invalid character sequence sandwiched between two null characters */
    protected $brokenChar = "00 FF 00";

    public function provideCodePoints() {
        return [
'U+0020 (HTML)'  => [false, [0x20], "20"],
'U+0020 (fatal)' => [true,  [0x20], "20"],
'U+005C (HTML)'  => [false, [0x5C], "5C"],
'U+005C (fatal)' => [true,  [0x5C], "5C"],
'U+007E (HTML)'  => [false, [0x7E], "7E"],
'U+007E (fatal)' => [true,  [0x7E], "7E"],
'U+00A5 (HTML)'  => [false, [0xA5], "1B 28 4A 5C 1B 28 42"],
'U+00A5 (fatal)' => [true,  [0xA5], "1B 28 4A 5C 1B 28 42"],
'U+203E (HTML)'  => [false, [0x203E], "1B 28 4A 7E 1B 28 42"],
'U+203E (fatal)' => [true,  [0x203E], "1B 28 4A 7E 1B 28 42"],
'U+FF61 (HTML)'  => [false, [0xFF61], "1B 24 42 21 23 1B 28 42"],
'U+FF61 (fatal)' => [true,  [0xFF61], "1B 24 42 21 23 1B 28 42"],
'U+FF9F (HTML)'  => [false, [0xFF9F], "1B 24 42 21 2C 1B 28 42"],
'U+FF9F (fatal)' => [true,  [0xFF9F], "1B 24 42 21 2C 1B 28 42"],
'U+2212 (HTML)'  => [false, [0x2212], "1B 24 42 21 5D 1B 28 42"],
'U+2212 (fatal)' => [true,  [0x2212], "1B 24 42 21 5D 1B 28 42"],
'U+2116 (HTML)'  => [false, [0x2116], "1B 24 42 2D 62 1B 28 42"],
'U+2116 (fatal)' => [true,  [0x2116], "1B 24 42 2D 62 1B 28 42"],
'U+FFE2 (HTML)'  => [false, [0xFFE2], "1B 24 42 22 4C 1B 28 42"],
'U+FFE2 (fatal)' => [true,  [0xFFE2], "1B 24 42 22 4C 1B 28 42"],
'U+00C6 (HTML)'  => [false, [0xC6], "26 23 31 39 38 3B"],
'U+00C6 (fatal)' => [true,  [0xC6], new EncoderException("", Encoding::E_UNAVAILABLE_CODE_POINT)],
'U+FFFD (HTML)'  => [false, [0xFFFD], "26 23 36 35 35 33 33 3B"],
'U+FFFD (fatal)' => [true,  [0xFFFD], new EncoderException("", Encoding::E_UNAVAILABLE_CODE_POINT)],
'Roman (HTML)'  => [false, [0xA5, 0x20, 0x203E], "1B 28 4A 5C 20 7E 1B 28 42"],
'Roman (fatal)' => [true,  [0xA5, 0x20, 0x203E], "1B 28 4A 5C 20 7E 1B 28 42"],
'Roman to ASCII (HTML)'  => [false, [0xA5, 0x5C], "1B 28 4A 5C 1B 28 42 5C"],
'Roman to ASCII (fatal)' => [true,  [0xA5, 0x5C], "1B 28 4A 5C 1B 28 42 5C"],
'Roman to error (HTML)'  => [false, [0xA5, 0x80], "1B 28 4A 5C 26 23 31 32 38 3B 1B 28 42"],
'Roman to error (fatal)' => [true,  [0xA5, 0x80], new EncoderException("", Encoding::E_UNAVAILABLE_CODE_POINT)],
'JIS (HTML)'  => [false, [0x2116, 0xFFE2, 0x2212], "1B 24 42 2D 62 22 4C 21 5D 1B 28 42"],
'JIS (fatal)' => [true,  [0x2116, 0xFFE2, 0x2212], "1B 24 42 2D 62 22 4C 21 5D 1B 28 42"],
'JIS to Roman (HTML)'  => [false, [0x2116, 0xA5], "1B 24 42 2D 62 1B 28 4A 5C 1B 28 42"],
'JIS to Roman (fatal)' => [true,  [0x2116, 0xA5], "1B 24 42 2D 62 1B 28 4A 5C 1B 28 42"],
'JIS to ASCII 1 (HTML)'  => [false, [0x2116, 0x20], "1B 24 42 2D 62 1B 28 42 20"],
'JIS to ASCII 1 (fatal)' => [true,  [0x2116, 0x20], "1B 24 42 2D 62 1B 28 42 20"],
'JIS to ASCII 2 (HTML)'  => [false, [0x2116, 0x5C], "1B 24 42 2D 62 1B 28 42 5C"],
'JIS to ASCII 2 (fatal)' => [true,  [0x2116, 0x5C], "1B 24 42 2D 62 1B 28 42 5C"],
'JIS to error (HTML)'  => [false, [0x2116, 0x80], "1B 24 42 2D 62 1B 28 42 26 23 31 32 38 3B"],
'JIS to error (fatal)' => [true,  [0x2116, 0x80], new EncoderException("", Encoding::E_UNAVAILABLE_CODE_POINT)],
'Escape characters (HTML)'  => [false, [0x1B, 0xE, 0xF], "26 23 36 35 35 33 33 3B 26 23 36 35 35 33 33 3B 26 23 36 35 35 33 33 3B"],
'Escape characters (fatal)' => [true,  [0x1B, 0xE, 0xF], new EncoderException("", Encoding::E_UNAVAILABLE_CODE_POINT)],
'-1 (HTML)'  => [false, [-1], new EncoderException("", Encoding::E_INVALID_CODE_POINT)],
'-1 (fatal)' => [true,  [-1], new EncoderException("", Encoding::E_INVALID_CODE_POINT)],
'0x110000 (HTML)'  => [false, [0x110000], new EncoderException("", Encoding::E_INVALID_CODE_POINT)],
'0x110000 (fatal)' => [true,  [0x110000], new EncoderException("", Encoding::E_INVALID_CODE_POINT)],
        ];
    }

    public function provideStrings() {
        return [
            'empty string' => ["", []],
            'Implied ASCII mode' => ["00 30 5C 7E 21 5F", [0, 48, 92, 126, 33, 95]],
            'Explicit ASCII mode' => ["1B2842 00 30 5C 7E 21 5F", [0, 48, 92, 126, 33, 95]],
            'Roman mode' => ["1B284A 00 30 5C 7E 21 5F", [0, 48, 165, 8254, 33, 95]],
            'Katakana mode' => ["1B2849 00 30 5C 7E 21 5F", [65533, 65392, 65436, 65533, 65377, 65439]],
            'Double-byte mode 1' => ["1B2440 00 305C 7E21 5F", [65533, 31227, 65533, 65533]],
            'Double-byte mode 2' => ["1B2442 00 305C 7E21 5F", [65533, 31227, 65533, 65533]],
            'Multiple modes' => ["5C 1B2849 21 1B2440 305C 1B284A 5C 1B2842 5C", [92, 65377, 31227, 165, 92]],
            'Double escape' => ["1B2849 1B2842 5C", [65533, 92]],
            'Triple escape' => ["1B2849 1B2842 1B284A 5C", [65533, 65533, 165]],
            'Trailing escape' => ["20 1B284A 30 33 1B2849", [32, 48, 51]],
            'Truncated escape 1' => ["1B", [65533]],
            'Truncated escape 2' => ["1B28", [65533, 40]],
            'Truncated escape 3' => ["1B2820", [65533, 40, 32]],
            'Truncated escape 4' => ["1B2020", [65533, 32, 32]],
            'Invalid escape 1' => ["1B2840", [65533, 40, 64]],
            'Invalid escape 2' => ["1B244A", [65533, 36, 74]],
            'Invalid bytes' => ["80 FF 1B2849 00 20 7F 1B2442 00 2100 FF FF", [65533, 65533, 65533, 65533, 65533, 65533, 65533, 65533, 65533]],
        ];
    }

    /**
     * @dataProvider provideCodePoints
     * @covers MensBeam\Intl\Encoding\Encoder
     */
    public function testEncodeCodePoints(bool $fatal, $input, $exp) {
        return parent::testEncodeCodePoints($fatal, $input, $exp);
    }

    /**
     * @dataProvider provideCodePoints
     * @coversNothing
     */
    public function testEncodeCodePointsStatically(bool $fatal, $input, $exp) {
        return parent::testEncodeCodePointsStatically($fatal, $input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\ISO2022JP::__construct
     * @covers MensBeam\Intl\Encoding\ISO2022JP::nextCode
     * @covers MensBeam\Intl\Encoding\ISO2022JP::modeSet
     */
    public function testDecodeMultipleCharactersAsCodePoints(string $input, array $exp) {
        return parent::testDecodeMultipleCharactersAsCodePoints($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\ISO2022JP::__construct
     * @covers MensBeam\Intl\Encoding\ISO2022JP::nextChar
     * @covers MensBeam\Intl\Encoding\ISO2022JP::modeSet
     */
    public function testDecodeMultipleCharactersAsStrings(string $input, array $exp) {
        return parent::testDecodeMultipleCharactersAsStrings($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\ISO2022JP::seekBack
     */
    public function testSTepBackThroughAString(string $input, array $exp) {
        return parent::testSTepBackThroughAString($input, $exp);
    }

    /**
     * @covers MensBeam\Intl\Encoding\ISO2022JP::seek
     * @covers MensBeam\Intl\Encoding\ISO2022JP::posChar
     * @covers MensBeam\Intl\Encoding\ISO2022JP::posByte
     * @covers MensBeam\Intl\Encoding\ISO2022JP::rewind
     */
    public function testSeekThroughAString() {
        return parent::testSeekThroughAString();
    }

    /**
     * @covers MensBeam\Intl\Encoding\ISO2022JP::posChar
     * @covers MensBeam\Intl\Encoding\ISO2022JP::posByte
     * @covers MensBeam\Intl\Encoding\ISO2022JP::eof
     */
    public function testTraversePastTheEndOfAString() {
        return parent::testTraversePastTheEndOfAString();
    }

    /**
     * @covers MensBeam\Intl\Encoding\ISO2022JP::peekChar
     * @covers MensBeam\Intl\Encoding\ISO2022JP::stateSave
     * @covers MensBeam\Intl\Encoding\ISO2022JP::stateApply
     */
    public function testPeekAtCharacters() {
        return parent::testPeekAtCharacters();
    }

    /**
     * @covers MensBeam\Intl\Encoding\ISO2022JP::peekCode
     * @covers MensBeam\Intl\Encoding\ISO2022JP::stateSave
     * @covers MensBeam\Intl\Encoding\ISO2022JP::stateApply
     */
    public function testPeekAtCodePoints() {
        return parent::testPeekAtCodePoints();
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\ISO2022JP::lenChar
     * @covers MensBeam\Intl\Encoding\ISO2022JP::lenByte
     * @covers MensBeam\Intl\Encoding\ISO2022JP::stateSave
     * @covers MensBeam\Intl\Encoding\ISO2022JP::stateApply
     */
    public function testGetStringLength(string $input, array $points) {
        return parent::testGetStringLength($input, $points);
    }

    /**
     * @covers MensBeam\Intl\Encoding\ISO2022JP::errDec
     */
    public function testReplacementModes() {
        return parent::testReplacementModes();
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\ISO2022JP::rewind
     * @covers MensBeam\Intl\Encoding\ISO2022JP::chars
     * @covers MensBeam\Intl\Encoding\ISO2022JP::codes
     */
    public function testIterateThroughAString(string $input, array $exp) {
        return parent::testIterateThroughAString($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @coversNothing
     */
    public function testIterateThroughAStringAllowingSurrogates(string $input, array $strictExp, array $relaxedExp = null) {
        return parent::testIterateThroughAStringAllowingSurrogates($input, $strictExp, $relaxedExp);
    }

    /**
     * @covers MensBeam\Intl\Encoding\ISO2022JP::seekBack
     */
    public function testSeekBackOverRandomData() {
        return parent::testSeekBackOverRandomData();
    }

    /**
     * @group optional
     */
    public function testPedanticallyDecodeSingleCharactersAsCodePoint() {
        $series = [
        ];
        foreach ($series as $test) {
            foreach ($test[0] as $a => $input) {
                $class = $this->testedClass;
                $char = hex2bin($input);
                $exp = $test[1][$a];
                $s = new $class($char);
                $this->assertSame($exp, $s->nextCode(), "Sequence $input did not decode to $exp.");
                $this->assertFalse($s->nextCode(), "Sequence $input did not end after one character");
            }
        }
    }
}
