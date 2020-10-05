<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\TestCase\Encoding;

use MensBeam\Intl\Encoding\UTF16LE;

class TestUTF16LE extends \MensBeam\Intl\Test\DecoderTest {
    protected $testedClass = UTF16LE::class;
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
    protected $seekString = "7A00 A200 346C 34D81EDD FFF8 FFDBFDDF FEFF";
    protected $seekCodes = [0x007A, 0x00A2, 0x6C34, 0x1D11E, 0xF8FF, 0x10FFFD, 0xFFFE];
    protected $seekOffsets = [0, 2, 4, 6, 10, 12, 16, 18];
    /* This string contains an invalid character sequence sandwiched between two null characters */
    protected $brokenChar = "0000 00DC 0000";
    protected $lowerA = "a\x00";

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF16::__construct
     * @covers MensBeam\Intl\Encoding\UTF16::nextCode
     */
    public function testDecodeMultipleCharactersAsCodePoints(string $input, array $exp) {
        return parent::testDecodeMultipleCharactersAsCodePoints($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF16::__construct
     * @covers MensBeam\Intl\Encoding\UTF16::nextChar
     */
    public function testDecodeMultipleCharactersAsStrings(string $input, array $exp) {
        return parent::testDecodeMultipleCharactersAsStrings($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF16::seekBack
     */
    public function testSTepBackThroughAString(string $input, array $exp) {
        return parent::testSTepBackThroughAString($input, $exp);
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF16::seek
     * @covers MensBeam\Intl\Encoding\UTF16::posChar
     * @covers MensBeam\Intl\Encoding\UTF16::posByte
     * @covers MensBeam\Intl\Encoding\UTF16::rewind
     */
    public function testSeekThroughAString() {
        return parent::testSeekThroughAString();
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF16::posChar
     * @covers MensBeam\Intl\Encoding\UTF16::posByte
     * @covers MensBeam\Intl\Encoding\UTF16::eof
     */
    public function testTraversePastTheEndOfAString() {
        return parent::testTraversePastTheEndOfAString();
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF16::peekChar
     * @covers MensBeam\Intl\Encoding\UTF16::stateSave
     * @covers MensBeam\Intl\Encoding\UTF16::stateApply
     */
    public function testPeekAtCharacters() {
        return parent::testPeekAtCharacters();
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF16::peekCode
     * @covers MensBeam\Intl\Encoding\UTF16::stateSave
     * @covers MensBeam\Intl\Encoding\UTF16::stateApply
     */
    public function testPeekAtCodePoints() {
        return parent::testPeekAtCodePoints();
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF16::lenChar
     * @covers MensBeam\Intl\Encoding\UTF16::lenByte
     * @covers MensBeam\Intl\Encoding\UTF16::stateSave
     * @covers MensBeam\Intl\Encoding\UTF16::stateApply
     */
    public function testGetStringLength(string $input, array $points) {
        return parent::testGetStringLength($input, $points);
    }

    /**
     * @covers MensBeam\Intl\Encoding\UTF16::errDec
     */
    public function testReplacementModes() {
        return parent::testReplacementModes();
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF16::rewind
     * @covers MensBeam\Intl\Encoding\UTF16::chars
     * @covers MensBeam\Intl\Encoding\UTF16::codes
     */
    public function testIterateThroughAString(string $input, array $exp) {
        return parent::testIterateThroughAString($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\UTF16::nextCode
     */
    public function testIterateThroughAStringAllowingSurrogates(string $input, array $strictExp, array $relaxedExp = null) {
        return parent::testIterateThroughAStringAllowingSurrogates($input, $strictExp, $relaxedExp);
    }


    /**
     * @covers MensBeam\Intl\Encoding\UTF16::seekBack
     */
    public function testSeekBackOverRandomData() {
        return parent::testSeekBackOverRandomData();
    }

    public function provideStrings() {
        return [
            // control samples
            'empty string' => ["", []],
            'sanity check' => ["6100 6200 6300 3100 3200 3300", [97, 98, 99, 49, 50, 51]],
            'mixed sample' => ["7A00 A200 346C 34D8 1EDD FFF8 FFDB FDDF FEFF", [122, 162, 27700, 119070, 63743, 1114109, 65534]],
            // unexpected EOF
            'EOF in BMP character' => ["0000 FF", [0, 65533]],
            'EOF after lead surrogate' => ["0000 34D8", [0, 65533]],
            'EOF in trail surrogate' => ["0000 34D8 1E", [0, 65533]],
            // invalid UTF-16 surrogates
            'lead surrogate without trail' => ["34D8 0000", [65533, 0], [0xD834, 0]],
            'trail surrogate without lead' => ["1EDD 0000", [65533, 0], [0xDD1E, 0]],
            'double lead surrogate' => ["34D8 34D8 1EDD", [65533, 119070], [0xD834, 119070]],
            'double trail surrogate' => ["34D8 1EDD 1EDD", [119070, 65533], [119070, 0xDD1E]],
        ];
    }
}
