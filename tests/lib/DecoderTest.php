<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Test;

use MensBeam\Intl\Encoding\DecoderException;

abstract class DecoderTest extends \PHPUnit\Framework\TestCase {
    protected $random = "L51yGwEFuatjbZi7wgNC80qYncvauVm1Lh8vCSK/KJs6QxoynMU8TCamx5TNhbjeh5VpWqQ0Q1j/W6u4O/InxBDxk8g83azJFQHzU+L7Npk0bkdofFv2AHDI2SUlXotYeEOnkKa/c6eQiDk8NapS0LGnb64ypKASacAMp6s2wSUU03l6iVVapHsNBgYs0cD++vnG8ckgbGsV3KkE3Lh601u6jviDyeRwbTxLZcUfSS2uIzrvvGWFfw6D4/FOa3uTR1k2Ya6jT+T/F+OdMgWlUPouuAVgLuvFxj9v9ZBnI+FAFc0kX4aT/JoTuBGMm8YS4xPVvczdrPXCUijML5TZrU201uFqeB9LDDWULp1Ai9d41fcD/8GBFrzlpXPIV+hsSJ4HvWswXdDeVKWgSMrQ78pf+zwvD66TA4FjMiEsLLpf9bb+mPiS2Aa3BP0JpjPwi0gdBu8QipLXNGFUUGW/15jGlj3eNynELRAtvyYZnoYIYShsN1TIU+buw8hHOp9iKsKT+fqPaEuuLLtlJ/cqhcxaZhbaWRB6vCQW9mO7f8whl7cpbBOO+NwDDCJZCsULh7rINF2omkexfOZzQSt/LC3yw+Pzqrf5Pmp5YgpMvoNgHcY1FkpsHc48IHMsJ+gex2zltIG51TQBAhy/fWF0KIqd+IPT+qngVGYIw/WuXj0LaK7XIVp33tc6fzuXNv+GUzYwpv4k9ry8R/DW8EX572FXFA49HHxbytSIJLD/+KpE2CE1WOr3ONwOXm6WduUBmFi4bwlRrCKnHqnFtLztVdLwMOauFa8N822XoAnWvHs+8R1DLHtgUyZas3ktp/qjMp5oVsb2PO+VpPFHIighHySgljrPl+sKaPULh7P/rAHXOuS9p9zTZKHrQ4nccl8SnYZlHKdioWo1NK5LRZB0PXYH8Ytu8aWVBmb4lAlpAFbSTqtOhydUJ/lyM29STG5mTV3rbG6tWMsUXBpaX4PrGCnhj40RVdz0BzsgvzLu4PNI+s3TJ6ZKV4hGS5on040xMDC2423DpKHPNa7mbl7J036dFt0JcYeGu07maGxssJnwLbebg5cm36Ecea7cTBWEGFMqiFjLoBEu0Y2CfF/GEbwqOf55/p1ewaZMrunFKd/Mj89qyYU5bp6mwmXSwj10psAA+qtXYm3XzRrLHKfCuiukyPEtvI+RdjbQDtMP1vF5qkmjlQLHXvEDpviJMaqvIPkjGrZkvAej1JX5yka50z0od9LLz8TIernjLLoVZ+cWtpd3kchO6w+zTpIOups4HdD66zaiPJrXIrJwi5bIgwTOWLhVs3ufZ0loFjlWWUh5FlTW+oWl1AD4h/yPBHWglqfMaTTqH75B4XEriy+Bw9k=";
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
        $this->assertFalse($s->eof());

        $this->assertSame("a", $s->nextChar());
        $this->assertSame(1, $s->posChar());
        $this->assertSame($l, $s->posByte());
        $this->assertTrue($s->eof());

        $this->assertSame("", $s->nextChar());
        $this->assertSame(1, $s->posChar());
        $this->assertSame($l, $s->posByte());
        $this->assertTrue($s->eof());

        $s = new $class($this->lowerA);
        $this->assertSame(0, $s->posChar());
        $this->assertSame(0, $s->posByte());
        $this->assertFalse($s->eof());

        $this->assertSame(ord("a"), $s->nextCode());
        $this->assertSame(1, $s->posChar());
        $this->assertSame($l, $s->posByte());
        $this->assertTrue($s->eof());

        $this->assertSame(false, $s->nextCode());
        $this->assertSame(1, $s->posChar());
        $this->assertSame($l, $s->posByte());
        $this->assertTrue($s->eof());
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

        $this->assertSame(sizeof($points), $s->lenChar());
        $this->assertSame($posChar, $s->posChar());
        $this->assertSame($posByte, $s->posByte());
        $this->assertSame(strlen($input), $s->lenByte());
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
        $this->iterateThroughAString($input, $exp, false);
    }

    public function testIterateThroughAStringAllowingSurrogates(string $input, array $strictExp, array $relaxedExp = null) {
        $exp = $relaxedExp ?? $strictExp;
        $this->iterateThroughAString($input, $exp, true);
    }

    public function testSeekBackOverRandomData() {
        $class = $this->testedClass;
        $bytes = base64_decode($this->random);
        $i = new $class($bytes);
        $fwd = [];
        do {
            $fwd[] = [$i->posByte(), $i->nextCode()];
        } while ($i->posByte() < strlen($bytes));
        while (sizeof($fwd)) {
            list($expPos, $expCode) = array_pop($fwd);
            $this->assertSame(0, $i->seek(-1), "Start of string reached prematureley");
            $this->assertSame($expPos, $i->posByte(), "Position desynchronized");
            $this->assertSame($expCode, $i->peekCode(1)[0], "Incorrect character decoded at byte position $expPos");
        }
    }

    protected function iterateThroughAString(string $input, array $exp, bool $allowSurrogates) {
        $class = $this->testedClass;
        $input = $this->prepString($input);
        $s = new $class($input, false, $allowSurrogates);
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
