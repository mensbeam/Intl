<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\TestCase\Encoding;

use MensBeam\Intl\Encoding\Replacement;
use MensBeam\Intl\Encoding\DecoderException;

class TestReplacement extends \MensBeam\Intl\Test\DecoderTest {
    protected $testedClass = Replacement::class;

    public function provideStrings() {
        return [
            // control samples
            'empty string' => ["", []],
            'Arbitrary string 1' => ["20", [0xFFFD]],
            'Arbitrary string 2' => ["64 8B 20 00 FF A5", [0xFFFD]],
        ];
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\Replacement::__construct
     * @covers MensBeam\Intl\Encoding\Replacement::nextCode
     */
    public function testDecodeMultipleCharactersAsCodePoints(string $input, array $exp) {
        return parent::testDecodeMultipleCharactersAsCodePoints($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\Replacement::__construct
     * @covers MensBeam\Intl\Encoding\Replacement::nextChar
     */
    public function testDecodeMultipleCharactersAsStrings(string $input, array $exp) {
        return parent::testDecodeMultipleCharactersAsStrings($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\Replacement::seek
     */
    public function testSTepBackThroughAString(string $input, array $exp) {
        return parent::testSTepBackThroughAString($input, $exp);
    }

    /**
     * @coversNothing
     */
    public function testSeekThroughAString() {
        $this->assertTrue(true);
    }

    /**
     * @covers MensBeam\Intl\Encoding\Replacement::posChar
     * @covers MensBeam\Intl\Encoding\Replacement::posByte
     * @covers MensBeam\Intl\Encoding\Replacement::seek
     * @covers MensBeam\Intl\Encoding\Replacement::eof
     */
    public function testTraversePastTheEndOfAString() {
        $d = new Replacement("a");
        $this->assertFalse($d->eof());
        $this->assertSame(0, $d->posChar());
        $this->assertSame(0, $d->posByte());
        $d->seek(1);
        $this->assertTrue($d->eof());
        $this->assertSame(1, $d->posChar());
        $this->assertSame(1, $d->posByte());
        $d->seek(1);
        $this->assertTrue($d->eof());
        $this->assertSame(1, $d->posChar());
        $this->assertSame(1, $d->posByte());
    }

    /**
     * @covers MensBeam\Intl\Encoding\Replacement::peekChar
     * @covers MensBeam\Intl\Encoding\Replacement::posChar
     * @covers MensBeam\Intl\Encoding\Replacement::posByte
     */
    public function testPeekAtCharacters() {
        $d = new Replacement("A");
        $this->assertSame(0, $d->posChar());
        $this->assertSame(0, $d->posByte());
        $this->assertSame("\u{FFFD}", $d->peekChar(2112));
        $this->assertSame(0, $d->posChar());
        $this->assertSame(0, $d->posByte());
        $this->assertSame("", $d->peekChar(0));
        $this->assertSame("", $d->peekChar(-2112));
    }

    /**
     * @covers MensBeam\Intl\Encoding\Replacement::peekCode
     * @covers MensBeam\Intl\Encoding\Replacement::posChar
     * @covers MensBeam\Intl\Encoding\Replacement::posByte
     */
    public function testPeekAtCodePoints() {
        $d = new Replacement("A");
        $this->assertSame(0, $d->posChar());
        $this->assertSame(0, $d->posByte());
        $this->assertSame([0xFFFD], $d->peekCode(2112));
        $this->assertSame(0, $d->posChar());
        $this->assertSame(0, $d->posByte());
        $this->assertSame([], $d->peekCode(0));
        $this->assertSame([], $d->peekCode(-2112));
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\Replacement::lenChar
     * @covers MensBeam\Intl\Encoding\Replacement::lenByte
     */
    public function testGetStringLength(string $input, array $points) {
        return parent::testGetStringLength($input, $points);
    }

    /**
     * @covers MensBeam\Intl\Encoding\Replacement::nextChar
     * @covers MensBeam\Intl\Encoding\Replacement::nextCode
     * @covers MensBeam\Intl\Encoding\Replacement::peekChar
     * @covers MensBeam\Intl\Encoding\Replacement::peekCode
     * @covers MensBeam\Intl\Encoding\Replacement::rewind
     * @covers MensBeam\Intl\Encoding\Replacement::posChar
     * @covers MensBeam\Intl\Encoding\Replacement::posByte
     */
    public function testReplacementModes() {
        $d = new Replacement("VVVVVV", true);
        $this->assertSame(0, $d->posChar());
        $this->assertSame(0, $d->posByte());
        try {
            $p = $d->peekCode();
        } catch (\Exception $e) {
            $p = $e;
        } finally {
            $this->assertInstanceOf(DecoderException::class, $p);
        }
        $this->assertSame(0, $d->posErr);
        $this->assertSame(0, $d->posChar());
        $this->assertSame(0, $d->posByte());
        try {
            $p = $d->nextCode();
        } catch (\Exception $e) {
            $p = $e;
        } finally {
            $this->assertInstanceOf(DecoderException::class, $p);
        }
        $this->assertSame(1, $d->posErr);
        $this->assertSame(1, $d->posChar());
        $this->assertSame(6, $d->posByte());
        $d->rewind();
        $this->assertSame(0, $d->posChar());
        $this->assertSame(0, $d->posByte());
        try {
            $p = $d->peekChar();
        } catch (\Exception $e) {
            $p = $e;
        } finally {
            $this->assertInstanceOf(DecoderException::class, $p);
        }
        $this->assertSame(1, $d->posErr);
        $this->assertSame(0, $d->posChar());
        $this->assertSame(0, $d->posByte());
        try {
            $p = $d->nextChar();
        } catch (\Exception $e) {
            $p = $e;
        } finally {
            $this->assertInstanceOf(DecoderException::class, $p);
        }
        $this->assertSame(1, $d->posErr);
        $this->assertSame(1, $d->posChar());
        $this->assertSame(6, $d->posByte());
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\Replacement::rewind
     * @covers MensBeam\Intl\Encoding\Replacement::chars
     * @covers MensBeam\Intl\Encoding\Replacement::codes
     */
    public function testIterateThroughAString(string $input, array $exp) {
        return parent::testIterateThroughAString($input, $exp);
    }

    /**
     * @dataProvider provideStrings
     * @covers MensBeam\Intl\Encoding\Replacement::nextCode
     */
    public function testIterateThroughAStringAllowingSurrogates(string $input, array $strictExp, ?array $relaxedExp = null) {
        return parent::testIterateThroughAStringAllowingSurrogates($input, $strictExp, $relaxedExp);
    }

    /**
     * @coversNothing
     */
    public function testSeekBackOverRandomData() {
        return parent::testSeekBackOverRandomData();
    }

    /**
     * @covers MensBeam\Intl\Encoding\Replacement::asciiSpan
     */
    public function testExtractAsciiSpans() {
        $d = new Replacement("VVVVVV");
        $this->assertSame("", $d->asciiSpan($this->allBytes()));
        $d->nextChar();
        $this->assertTrue($d->eof());
    }

    /**
     * @covers MensBeam\Intl\Encoding\Replacement::asciiSpanNot
     */
    public function testExtractNegativeAsciiSpans() {
        $d = new Replacement("VVVVVV");
        $this->assertSame("", $d->asciiSpanNot(""));
        $d->nextChar();
        $this->assertTrue($d->eof());
    }
}
