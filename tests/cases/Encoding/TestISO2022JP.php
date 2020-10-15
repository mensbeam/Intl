<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\TestCase\Encoding;

use MensBeam\Intl\Encoding\ISO2022JP;

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
     * @covers MensBeam\Intl\Encoding\ISO2022JP::encode
     * @covers MensBeam\Intl\Encoding\ISO2022JP::errEnc
     */
    public function testEncodeCodePoints(bool $fatal, $input, $exp) {
        return parent::testEncodeCodePoints($fatal, $input, $exp);
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
