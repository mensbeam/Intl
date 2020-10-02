<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class UTF8 extends AbstractEncoding implements StatelessEncoding {
    const NAME = "UTF-8";
    const LABELS = ["unicode-1-1-utf-8", "utf-8", "utf8"];

    protected $selfSynchronizing = true;

    public function nextCode() {
        // this function effectively implements https://encoding.spec.whatwg.org/#utf-8-decoder
        // optimization for ASCII characters
        $b = @$this->string[$this->posByte];
        if ($b === "") {
            return false;
        } elseif (($b = ord($b)) < 0x80) {
            $this->posChar++;
            $this->posByte++;
            return $b;
        }
        $this->posChar++;
        $point = 0;
        $seen = 0;
        $needed = 1;
        $lower = 0x80;
        $upper = 0xBF;
        while ($seen < $needed) {
            $b = ord(@$this->string[$this->posByte++]);
            if (!$seen) {
                if ($b >= 0xC2 && $b <= 0xDF) { // two-byte character
                    $needed = 2;
                    $point = $b & 0x1F;
                } elseif ($b >= 0xE0 && $b <= 0xEF) { // three-byte character
                    $needed = 3;
                    if ($b==0xE0) {
                        $lower = 0xA0;
                    } elseif ($b==0xED) {
                        $upper = ($this->allowSurrogates) ? 0xBF : 0x9F;
                    }
                    $point = $b & 0xF;
                } elseif ($b >= 0xF0 && $b <= 0xF4) { // four-byte character
                    $needed = 4;
                    if ($b==0xF0) {
                        $lower = 0x90;
                    } elseif ($b==0xF4) {
                        $upper = 0x8F;
                    }
                    $point = $b & 0x7;
                } else { // invalid byte
                    return $this->errDec($this->errMode, $this->posChar, $this->posByte);
                }
            } elseif ($b < $lower || $b > $upper) {
                return $this->errDec($this->errMode, $this->posChar, $this->posByte--);
            } else {
                $lower = 0x80;
                $upper = 0xBF;
                $point = ($point << 6) | ($b & 0x3F);
            }
            $seen++;
        }
        return $point;
    }

    public static function encode(int $codePoint, bool $fatal = true): string {
        // this function implements https://encoding.spec.whatwg.org/#utf-8-encoder
        if ($codePoint < 0 || $codePoint > 0x10FFFF) {
            throw new EncoderException("Encountered code point outside Unicode range ($codePoint)", self::E_INVALID_CODE_POINT);
        } elseif ($codePoint < 128) {
            return chr($codePoint);
        } elseif ($codePoint < 0x800) {
            $count = 1;
            $offset = 0xC0;
        } elseif ($codePoint < 0x10000) {
            $count = 2;
            $offset = 0xE0;
        } else {
            $count = 3;
            $offset = 0xF0;
        }
        $bytes = chr(($codePoint >> (6 * $count)) + $offset);
        while ($count > 0) {
            $bytes .= chr(0x80 | (($codePoint >> (6 * ($count - 1))) & 0x3F));
            $count--;
        }
        return $bytes;
    }

    /** Implements backward seeking $distance characters */
    protected function seekBack(int $distance): int {
        while ($distance > 0 && $this->posByte > 0) {
            $distance--;
            $this->posChar--;
            $b = ord(@$this->string[$this->posByte - 1]);
            if ($b < 0x80) {
                // if the byte is an ASCII byte or the end of input, then this is already a synchronized position
                $this->posByte--;
            } else {
                $s = $this->posByte;
                $pos = $s - 1;
                while ($b >= 0x80 && $b <= 0xBF && $pos > 0 && ($s - $pos) < 4) { // go back at most four bytes, no further than the start of the string, and only as long as the byte remains a continuation byte
                    $b = ord(@$this->string[--$pos]);
                }
                $this->posByte = $pos;
                // decrement the character position because nextCode() increments it
                $this->posChar--;
                // check for overlong sequences: if the sequence is overlong consuming the character will yield an earlier position than where we started
                $this->nextCode();
                $this->posByte = ($this->posByte < $s) ? $s - 1 : $pos;
            }
        }
        return $distance;
    }
}
