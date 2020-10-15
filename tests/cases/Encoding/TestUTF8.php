<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\TestCase\Encoding;

use MensBeam\Intl\Encoding\UTF8;
use MensBeam\Intl\Encoding\Encoding;
use MensBeam\Intl\Encoding\EncoderException;

class TestUTF8 extends \MensBeam\Intl\Test\CoderDecoderTest {
    protected $testedClass = UTF8::class;
    /*
        Char 0  U+007A   (1 byte)  Offset 0
        Char 1  U+00A2   (2 bytes) Offset 1
        Char 2  U+6C34   (3 bytes) Offset 3
        Char 3  U+1D11E  (4 bytes) Offset 6
        Char 4  U+F8FF   (3 bytes) Offset 10
        Char 5  U+10FFFD (4 bytes) Offset 13
        Char 6  U+FFFE   (3 bytes) Offset 17
        End of string at char 7, offset 20
    */
    protected $seekString = "7A C2A2 E6B0B4 F09D849E EFA3BF F48FBFBD EFBFBE";
    protected $seekCodes = [0x007A, 0x00A2, 0x6C34, 0x1D11E, 0xF8FF, 0x10FFFD, 0xFFFE];
    protected $seekOffsets = [0, 1, 3, 6, 10, 13, 17, 20];
    /* This string contains an invalid character sequence sandwiched between two null characters */
    protected $brokenChar = "00 FF 00";

    public function provideCodePoints() {
        return [
            'U+007A (HTML)'    => [false, 0x7A, "7A"],
            'U+007A (fatal)'   => [true,  0x7A, "7A"],
            'U+00A2 (HTML)'    => [false, 0xA2, "C2 A2"],
            'U+00A2 (fatal)'   => [true,  0xA2, "C2 A2"],
            'U+6C34 (HTML)'    => [false, 0x6C34, "E6 B0 B4"],
            'U+6C34 (fatal)'   => [true,  0x6C34, "E6 B0 B4"],
            'U+1D11E (HTML)'   => [false, 0x1D11E, "F0 9D 84 9E"],
            'U+1D11E (fatal)'  => [true,  0x1D11E, "F0 9D 84 9E"],
            'U+F8FF (HTML)'    => [false, 0xF8FF, "EF A3 BF"],
            'U+F8FF (fatal)'   => [true,  0xF8FF, "EF A3 BF"],
            'U+10FFFD (HTML)'  => [false, 0x10FFFD, "F4 8F BF BD"],
            'U+10FFFD (fatal)' => [true,  0x10FFFD, "F4 8F BF BD"],
            'U+FFFE (HTML)'    => [false, 0xFFFE, "EF BF BE"],
            'U+FFFE (fatal)'   => [true,  0xFFFE, "EF BF BE"],
            '-1 (HTML)'        => [false, -1, new EncoderException("", Encoding::E_INVALID_CODE_POINT)],
            '-1 (fatal)'       => [true,  -1, new EncoderException("", Encoding::E_INVALID_CODE_POINT)],
            '0x110000 (HTML)'  => [false, 0x110000, new EncoderException("", Encoding::E_INVALID_CODE_POINT)],
            '0x110000 (fatal)' => [true,  0x110000, new EncoderException("", Encoding::E_INVALID_CODE_POINT)],
        ];
    }

    public function provideStrings() {
        return [
            // control samples
            'empty string' => ["", []],
            'sanity check' => ["61 62 63 31 32 33", [97, 98, 99, 49, 50, 51]],
            'multibyte control' => ["E5 8F A4 E6 B1 A0 E3 82 84 E8 9B 99 E9 A3 9B E3 81 B3 E8 BE BC E3 82 80 E6 B0 B4 E3 81 AE E9 9F B3", [21476, 27744, 12420, 34521, 39131, 12403, 36796, 12416, 27700, 12398, 38899]],
            'mixed sample' => ["7A C2 A2 E6 B0 B4 F0 9D 84 9E EF A3 BF F4 8F BF BD EF BF BE", [122, 162, 27700, 119070, 63743, 1114109, 65534]],
            // various invalid sequences
            'invalid code' => ["FF", [65533]],
            'ends early' => ["C0", [65533]],
            'ends early 2' => ["E0", [65533]],
            'invalid trail' => ["C0 00", [65533, 0]],
            'invalid trail 2' => ["C0 C0", [65533, 65533]],
            'invalid trail 3' => ["E0 00", [65533, 0]],
            'invalid trail 4' => ["E0 C0", [65533, 65533]],
            'invalid trail 5' => ["E0 80 00", [65533, 65533, 0]],
            'invalid trail 6' => ["E0 80 C0", [65533, 65533, 65533]],
            '> 0x10FFFF' => ["FC 80 80 80 80 80", [65533, 65533, 65533, 65533, 65533, 65533]],
            'obsolete lead byte' => ["FE 80 80 80 80 80", [65533, 65533, 65533, 65533, 65533, 65533]],
            'overlong U+0000 - 2 bytes' => ["C0 80", [65533, 65533]],
            'overlong U+0000 - 3 bytes' => ["E0 80 80", [65533, 65533, 65533]],
            'overlong U+0000 - 4 bytes' => ["F0 80 80 80", [65533, 65533, 65533, 65533]],
            'overlong U+0000 - 5 bytes' => ["F8 80 80 80 80", [65533, 65533, 65533, 65533, 65533]],
            'overlong U+0000 - 6 bytes' => ["FC 80 80 80 80 80", [65533, 65533, 65533, 65533, 65533, 65533]],
            'overlong U+007F - 2 bytes' => ["C1 BF", [65533, 65533]],
            'overlong U+007F - 3 bytes' => ["E0 81 BF", [65533, 65533, 65533]],
            'overlong U+007F - 4 bytes' => ["F0 80 81 BF", [65533, 65533, 65533, 65533]],
            'overlong U+007F - 5 bytes' => ["F8 80 80 81 BF", [65533, 65533, 65533, 65533, 65533]],
            'overlong U+007F - 6 bytes' => ["FC 80 80 80 81 BF", [65533, 65533, 65533, 65533, 65533, 65533]],
            'overlong U+07FF - 3 bytes' => ["E0 9F BF", [65533, 65533, 65533]],
            'overlong U+07FF - 4 bytes' => ["F0 80 9F BF", [65533, 65533, 65533, 65533]],
            'overlong U+07FF - 5 bytes' => ["F8 80 80 9F BF", [65533, 65533, 65533, 65533, 65533]],
            'overlong U+07FF - 6 bytes' => ["FC 80 80 80 9F BF", [65533, 65533, 65533, 65533, 65533, 65533]],
            'overlong U+FFFF - 4 bytes' => ["F0 8F BF BF", [65533, 65533, 65533, 65533]],
            'overlong U+FFFF - 5 bytes' => ["F8 80 8F BF BF", [65533, 65533, 65533, 65533, 65533]],
            'overlong U+FFFF - 6 bytes' => ["FC 80 80 8F BF BF", [65533, 65533, 65533, 65533, 65533, 65533]],
            'overlong U+10FFFF - 5 bytes' => ["F8 84 8F BF BF", [65533, 65533, 65533, 65533, 65533]],
            'overlong U+10FFFF - 6 bytes' => ["FC 80 84 8F BF BF", [65533, 65533, 65533, 65533, 65533, 65533]],
            // UTF-16 surrogates
            // surrogates have alternate outputs for when surrogates are being allowed
            'lead surrogate' => ["ED A0 80", [65533, 65533, 65533], [0xD800]],
            'trail surrogate' => ["ED B0 80", [65533, 65533, 65533], [0xDC00]],
            'surrogate pair' => ["ED A0 80 ED B0 80", [65533, 65533, 65533, 65533, 65533, 65533], [0xD800, 0xDC00]],
            // self-sync edge cases
            'trailing continuation' => ["0A 80 80", [10, 65533, 65533]],
            'trailing continuation 2' => ["E5 8F A4 80", [21476, 65533]],
        ];
    }

    /**
     * @dataProvider provideCodePoints
     * @covers MensBeam\Intl\Encoding\Encoder
     * @covers MensBeam\Intl\Encoding\UTF8::encode
     * @covers MensBeam\Intl\Encoding\UTF8::errEnc
     */
    public function testEncodeCodePoints(bool $fatal, $input, $exp) {
        return parent::testEncodeCodePoints($fatal, $input, $exp);
    }

    /**
     * @dataProvider provideCodePoints
     * @covers MensBeam\Intl\Encoding\UTF8::encode
     * @covers MensBeam\Intl\Encoding\UTF8::errEnc
     */
    public function testEncodeCodePointsStatically(bool $fatal, $input, $exp) {
        return parent::testEncodeCodePointsStatically($fatal, $input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF8::__construct
     * @covers MensBeam\Intl\Encoding\UTF8::nextCode
     */
    public function testDecodeMultipleCharactersAsCodePoints(string $input, array $exp) {
        return parent::testDecodeMultipleCharactersAsCodePoints($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF8::__construct
     * @covers MensBeam\Intl\Encoding\UTF8::nextChar
     */
    public function testDecodeMultipleCharactersAsStrings(string $input, array $exp) {
        return parent::testDecodeMultipleCharactersAsStrings($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF8::seekBack
     */
    public function testSTepBackThroughAString(string $input, array $exp) {
        return parent::testSTepBackThroughAString($input, $exp);
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF8::seek
     * @covers MensBeam\Intl\Encoding\UTF8::posChar
     * @covers MensBeam\Intl\Encoding\UTF8::posByte
     * @covers MensBeam\Intl\Encoding\UTF8::rewind
     */
    public function testSeekThroughAString() {
        return parent::testSeekThroughAString();
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF8::posChar
     * @covers MensBeam\Intl\Encoding\UTF8::posByte
     * @covers MensBeam\Intl\Encoding\UTF8::eof
     */
    public function testTraversePastTheEndOfAString() {
        return parent::testTraversePastTheEndOfAString();
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF8::peekChar
     * @covers MensBeam\Intl\Encoding\UTF8::stateSave
     * @covers MensBeam\Intl\Encoding\UTF8::stateApply
     */
    public function testPeekAtCharacters() {
        return parent::testPeekAtCharacters();
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF8::peekCode
     * @covers MensBeam\Intl\Encoding\UTF8::stateSave
     * @covers MensBeam\Intl\Encoding\UTF8::stateApply
     */
    public function testPeekAtCodePoints() {
        return parent::testPeekAtCodePoints();
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF8::lenChar
     * @covers MensBeam\Intl\Encoding\UTF8::lenByte
     * @covers MensBeam\Intl\Encoding\UTF8::stateSave
     * @covers MensBeam\Intl\Encoding\UTF8::stateApply
     */
    public function testGetStringLength(string $input, array $points) {
        return parent::testGetStringLength($input, $points);
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF8::errDec
     */
    public function testReplacementModes() {
        return parent::testReplacementModes();
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF8::rewind
     * @covers MensBeam\Intl\Encoding\UTF8::chars
     * @covers MensBeam\Intl\Encoding\UTF8::codes
     */
    public function testIterateThroughAString(string $input, array $exp) {
        return parent::testIterateThroughAString($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF8::nextCode
     */
    public function testIterateThroughAStringAllowingSurrogates(string $input, array $strictExp, array $relaxedExp = null) {
        return parent::testIterateThroughAStringAllowingSurrogates($input, $strictExp, $relaxedExp);
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF8::seekBack
     */
    public function testSeekBackOverRandomData() {
        return parent::testSeekBackOverRandomData();
    }
}
