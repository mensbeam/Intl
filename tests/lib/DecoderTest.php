<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Test;

use MensBeam\Intl\Encoding\DecoderException;

abstract class DecoderTest extends \PHPUnit\Framework\TestCase {
    protected $lowerA = "a";

    public function testDecodeMultipleCharactersAsCodePoints(string $input, array $exp) {
        $class = $this->testedClass;
        $input = $this->prepString($input);
        $s = new $class($input);
        $out = [];
        $a = 0;
        $this->assertSame($a, $s->posChar());
        while (($p = $s->nextCode()) !== false) {
            $this->assertSame(++$a, $s->posChar());
            $out[] = $p;
        }
        $this->assertSame($exp, $out);
        $this->assertSame(strlen($input), $s->posByte());
    }

    public function testDecodeMultipleCharactersAsStrings(string $input, array $exp) {
        $class = $this->testedClass;
        $exp = array_map(function($v) {
            return \IntlChar::chr($v);
        }, $exp);
        $input = $this->prepString($input);
        $s = new $class($input);
        $out = [];
        while (($p = $s->nextChar()) !== "") {
            $out[] = $p;
        }
        $this->assertSame($exp, $out);
        $this->assertSame(strlen($input), $s->posByte());
    }

    public function testSTepBackThroughAString(string $input, array $exp) {
        $class = $this->testedClass;
        $input = $this->prepString($input);
        $s = new $class($input);
        $exp = array_reverse($exp);
        $act = [];
        $pos = 0;
        while ($s->nextCode() !== false) {
            $this->assertSame(++$pos, $s->posChar());
        }
        $this->assertSame(sizeof($exp), $pos);
        while ($s->posChar()) {
            $this->assertSame(0, $s->seek(-1));
            $this->assertSame(--$pos, $s->posChar());
            $act[] = $s->nextCode();
            $s->seek(-1);
        }
        $this->assertEquals($exp, $act);
    }

    public function testSeekThroughAString() {
        $class = $this->testedClass;
        if (!$this->seekString) {
            $this->markTestSkipped();
            return;
        }
        $input = $this->prepString($this->seekString);
        $off = $this->seekOffsets;
        $s = new $class($input);
        $this->assertSame(0, $s->posChar());
        $this->assertSame(0, $s->posByte());

        $this->assertSame(0, $s->seek(0));
        $this->assertSame(0, $s->posChar());
        $this->assertSame(0, $s->posByte());

        $this->assertSame(1, $s->seek(-1));
        $this->assertSame(0, $s->posChar());
        $this->assertSame(0, $s->posByte());

        $this->assertSame(0, $s->seek(1));
        $this->assertSame(1, $s->posChar());
        $this->assertSame($off[1], $s->posByte());

        $this->assertSame(0, $s->seek(2));
        $this->assertSame(3, $s->posChar());
        $this->assertSame($off[3], $s->posByte());

        $this->assertSame(0, $s->seek(4));
        $this->assertSame(7, $s->posChar());
        $this->assertSame($off[7], $s->posByte());

        $this->assertSame(1, $s->seek(1));
        $this->assertSame(7, $s->posChar());
        $this->assertSame($off[7], $s->posByte());

        $this->assertSame(0, $s->seek(-3));
        $this->assertSame(4, $s->posChar());
        $this->assertSame($off[4], $s->posByte());

        $this->assertSame(6, $s->seek(-10));
        $this->assertSame(0, $s->posChar());
        $this->assertSame(0, $s->posByte());

        $this->assertSame(0, $s->seek(5));
        $this->assertSame(5, $s->posChar());
        $this->assertSame($off[5], $s->posByte());

        $s->rewind(0);
        $this->assertSame(0, $s->posChar());
        $this->assertSame(0, $s->posByte());
    }

    public function testTraversePastTheEndOfAString() {
        $class = $this->testedClass;
        $s = new $class($this->lowerA);
        $l = strlen($this->lowerA);
        $this->assertSame(0, $s->posChar());
        $this->assertSame(0, $s->posByte());

        $this->assertSame("a", $s->nextChar());
        $this->assertSame(1, $s->posChar());
        $this->assertSame($l, $s->posByte());

        $this->assertSame("", $s->nextChar());
        $this->assertSame(1, $s->posChar());
        $this->assertSame($l, $s->posByte());

        $s = new $class($this->lowerA);
        $this->assertSame(0, $s->posChar());
        $this->assertSame(0, $s->posByte());

        $this->assertSame(ord("a"), $s->nextCode());
        $this->assertSame(1, $s->posChar());
        $this->assertSame($l, $s->posByte());

        $this->assertSame(false, $s->nextCode());
        $this->assertSame(1, $s->posChar());
        $this->assertSame($l, $s->posByte());
    }

    public function testPeekAtCharacters() {
        $class = $this->testedClass;
        if (!$this->seekString) {
            $this->markTestSkipped();
            return;
        }
        $input = $this->prepString($this->seekString);
        $off = $this->seekOffsets;
        $codes = $this->seekCodes;
        $s = new $class($input);
        $s->seek(2);
        $this->assertSame(2, $s->posChar());
        $this->assertSame($off[2], $s->posByte());

        $this->assertSame(bin2hex(\IntlChar::chr($codes[2])), bin2hex($s->peekChar()));
        $this->assertSame(2, $s->posChar());
        $this->assertSame($off[2], $s->posByte());

        $this->assertSame(bin2hex(\IntlChar::chr($codes[2]).\IntlChar::chr($codes[3])), bin2hex($s->peekChar(2)));
        $this->assertSame(2, $s->posChar());
        $this->assertSame($off[2], $s->posByte());

        $s->seek(3);
        $this->assertSame(5, $s->posChar());
        $this->assertSame($off[5], $s->posByte());

        $this->assertSame(bin2hex(\IntlChar::chr($codes[5]).\IntlChar::chr($codes[6])), bin2hex($s->peekChar(3)));
        $this->assertSame(5, $s->posChar());
        $this->assertSame($off[5], $s->posByte());

        $this->assertSame("", $s->peekChar(-5));
        $this->assertSame(5, $s->posChar());
        $this->assertSame($off[5], $s->posByte());
    }

    public function testPeekAtCodePoints() {
        $class = $this->testedClass;
        if (!$this->seekString) {
            $this->markTestSkipped();
            return;
        }
        $input = $this->prepString($this->seekString);
        $off = $this->seekOffsets;
        $codes = $this->seekCodes;
        $s = new $class($input);
        $s->seek(2);
        $this->assertSame(2, $s->posChar());
        $this->assertSame($off[2], $s->posByte());

        $this->assertSame([$codes[2]], $s->peekCode());
        $this->assertSame(2, $s->posChar());
        $this->assertSame($off[2], $s->posByte());

        $this->assertSame([$codes[2], $codes[3]], $s->peekCode(2));
        $this->assertSame(2, $s->posChar());
        $this->assertSame($off[2], $s->posByte());

        $s->seek(3);
        $this->assertSame(5, $s->posChar());
        $this->assertSame($off[5], $s->posByte());

        $this->assertSame([$codes[5], $codes[6]], $s->peekCode(3));
        $this->assertSame(5, $s->posChar());
        $this->assertSame($off[5], $s->posByte());

        $this->assertSame([], $s->peekCode(-5));
        $this->assertSame(5, $s->posChar());
        $this->assertSame($off[5], $s->posByte());
    }

    public function testGetStringLength(string $input, array $points) {
        $class = $this->testedClass;
        $input = $this->prepString($input);
        $s = new $class($input);
        $s->seek(1);
        $posChar = $s->posChar();
        $posByte = $s->posByte();

        $this->assertSame(sizeof($points), $s->len());
        $this->assertSame($posChar, $s->posChar());
        $this->assertSame($posByte, $s->posByte());
    }

    public function testReplacementModes() {
        if (!$this->brokenChar) {
            // decoder for this encoding never produces errors
            $this->assertTrue(true);
            return;
        }
        $class = $this->testedClass;
        $input = $this->prepString($this->brokenChar);
        // officially test replacement characters (already effectively tested by other tests)
        $s = new $class($input, false);
        $s->seek(1);
        $this->assertSame(0xFFFD, $s->nextCode());
        $s->seek(-2);
        // test fatal mode
        $s = new $class($input, true);
        $s->seek(1);
        try {
            $p = $s->nextCode();
        } catch (DecoderException $e) {
            $p = $e;
        } finally {
            $this->assertInstanceOf(DecoderException::class, $p);
        }
        $this->assertSame(2, $s->posChar());
        $this->assertSame(0x00, $s->nextCode());
        $this->assertSame(3, $s->posChar());
        $this->assertSame(0, $s->seek(-2));
        $this->assertSame(1, $s->posChar());
        try {
            $p = $s->peekCode();
        } catch (DecoderException $e) {
            $p = $e;
        } finally {
            $this->assertInstanceOf(DecoderException::class, $p);
        }
        $this->assertSame(1, $s->posChar());
        try {
            $p = $s->peekChar();
        } catch (DecoderException $e) {
            $p = $e;
        } finally {
            $this->assertInstanceOf(DecoderException::class, $p);
        }
        $this->assertSame(1, $s->posChar());
    }

    public function testIterateThroughAString(string $input, array $exp) {
        $class = $this->testedClass;
        $input = $this->prepString($input);
        $s = new $class($input);
        $out = [];
        $a = 0;
        $this->assertTrue(true); // prevent risky test of empty string
        foreach ($s->codes() as $index => $p) {
            $this->assertSame($a, $index, "Character key at index $a reported incorrectly");
            $this->assertSame($exp[$a], $p, "Character at index $a decoded incorrectly");
            $a++;
        }
        $a = 0;
        foreach ($s->codes() as $p) {
            $a++;
        }
        $this->assertSame(0, $a);
        $s->rewind();
        foreach ($s->codes() as $p) {
            $a++;
        }
        $this->assertSame(sizeof($exp), $a);

        $exp = array_map(function($v) {
            return \IntlChar::chr($v);
        }, $exp);

        foreach ($s->chars() as $index => $p) {
            $this->assertSame($a, $index, "Character key at index $a reported incorrectly");
            $this->assertSame(bin2hex($exp[$a]), bin2hex($p), "Character at index $a decoded incorrectly");
            $a++;
        }
        $a = 0;
        foreach ($s->chars() as $p) {
            $a++;
        }
        $this->assertSame(0, $a);
        $s->rewind();
        foreach ($s->chars() as $p) {
            $a++;
        }
        $this->assertSame(sizeof($exp), $a);
    }

    protected function prepString(string $str): string {
        return hex2bin(str_replace(" ", "", $str));
    }
}
