<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\TestCase\Encoding;

use MensBeam\Intl\Encoding\XUserDefined;

class TestXUserDefined extends \MensBeam\Intl\Test\DecoderTest {
    protected $testedClass = XUserDefined::class;
    /* X-user-defined doesn't have complex seeking, so this string is generic */
    protected $seekString = "30 31 32 33 34 35 36";
    protected $seekCodes = [0x30, 0x31, 0x32, 0x33, 0x34, 0x35, 0x36];
    protected $seekOffsets = [0, 1, 2, 3, 4, 5, 6, 7];
    /* This string is supposed to contain an invalid character sequence sandwiched between two null characters, but x-user-defined has no invalid characters */
    protected $brokenChar = "";

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\XUserDefined::__construct
     * @covers MensBeam\Intl\Encoding\XUserDefined::nextCode
     */
    public function testDecodeMultipleCharactersAsCodePoints(string $input, array $exp) {
        return parent::testDecodeMultipleCharactersAsCodePoints($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\XUserDefined::__construct
     * @covers MensBeam\Intl\Encoding\XUserDefined::nextChar
     */
    public function testDecodeMultipleCharactersAsStrings(string $input, array $exp) {
        return parent::testDecodeMultipleCharactersAsStrings($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @coversNothing
     */
    public function testSTepBackThroughAString(string $input, array $exp) {
        // this test has no meaning for x-user-defined
        return parent::testSTepBackThroughAString($input, $exp);
    }

    /**
     * @covers MensBeam\Intl\Encoding\XUserDefined::seek
     * @covers MensBeam\Intl\Encoding\XUserDefined::posChar
     * @covers MensBeam\Intl\Encoding\XUserDefined::posByte
     * @covers MensBeam\Intl\Encoding\XUserDefined::rewind
     */
    public function testSeekThroughAString() {
        return parent::testSeekThroughAString();
    }

    /**
     * @covers MensBeam\Intl\Encoding\XUserDefined::posChar
     * @covers MensBeam\Intl\Encoding\XUserDefined::posByte
     * @covers MensBeam\Intl\Encoding\XUserDefined::eof
     */
    public function testTraversePastTheEndOfAString() {
        return parent::testTraversePastTheEndOfAString();
    }

    /**
     * @covers MensBeam\Intl\Encoding\XUserDefined::peekChar
     * @covers MensBeam\Intl\Encoding\XUserDefined::stateSave
     * @covers MensBeam\Intl\Encoding\XUserDefined::stateApply
     */
    public function testPeekAtCharacters() {
        return parent::testPeekAtCharacters();
    }

    /**
     * @covers MensBeam\Intl\Encoding\XUserDefined::peekCode
     * @covers MensBeam\Intl\Encoding\XUserDefined::stateSave
     * @covers MensBeam\Intl\Encoding\XUserDefined::stateApply
     */
    public function testPeekAtCodePoints() {
        return parent::testPeekAtCodePoints();
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\XUserDefined::lenChar
     * @covers MensBeam\Intl\Encoding\XUserDefined::lenByte
     * @covers MensBeam\Intl\Encoding\XUserDefined::stateSave
     * @covers MensBeam\Intl\Encoding\XUserDefined::stateApply
     */
    public function testGetStringLength(string $input, array $points) {
        return parent::testGetStringLength($input, $points);
    }

    /**
     * @covers MensBeam\Intl\Encoding\XUserDefined::errDec
     */
    public function testReplacementModes() {
        return parent::testReplacementModes();
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\XUserDefined::rewind
     * @covers MensBeam\Intl\Encoding\XUserDefined::chars
     * @covers MensBeam\Intl\Encoding\XUserDefined::codes
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
     * @coversNothing
     */
    public function testSeekBackOverRandomData() {
        return parent::testSeekBackOverRandomData();
    }

    public function provideStrings() {
        $a_bytes = [];
        $a_codes = [];
        for ($a = 0; $a < 0x80; $a++) {
            $a_bytes[] = strtoupper(bin2hex(chr($a)));
            $a_codes[]  = $a;
        }
        $p_bytes = [];
        $p_codes = [];
        for ($a = 0; $a < 0x80; $a++) {
            $p_bytes[] = strtoupper(bin2hex(chr(0x80 + $a)));
            $p_codes[]  = 0xF780 + $a;
        }
        $a_bytes = implode(" ", $a_bytes);
        $p_bytes = implode(" ", $p_bytes);
        return [
            'empty string' => ["", []],
            'ASCI bytes' => [$a_bytes, $a_codes],
            'private-use bytes' => [$p_bytes, $p_codes],
        ];
    }
}
