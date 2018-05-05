<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\UTF8;

class UTF8String {
    protected $string;
    protected $posByte = 0;
    protected $posChar = 0;


    public function __construct(string $string) {
        $this->string = $string;
    }

    public function nextChr(): string {
        // get the byte at the current position
        $b = @$this->string[$this->posByte];
        if (ord($b) < 0x80) {
            // if the byte is an ASCII character or end of input, simply return it
            $this->posChar++;
            $this->posByte++;
            return $b;
        } else {
            // otherwise return the serialization of the code point at the current position
            return UTF8::chr($this->nextOrd() ?? 0xFFFD);
        }
    }

    public function nextOrd() {
        // this function effectively implements https://encoding.spec.whatwg.org/#utf-8-decoder
        // though it differs from a slavish implementation because it operates on only a single
        // character rather than a whole stream
        $this->posChar++;
        // optimization for ASCII characters
        $b = @$this->string[$this->posByte];
        if ($b=="") {
            $this->posByte++;
            return false;
        } elseif (($b = ord($b)) < 0x80) {
            $this->posByte++;
            return $b;
        }
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
                        $upper = 0x9F;
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
                    return null;
                }
            } elseif ($b < $lower || $b > $upper) {
                $this->posByte--;
                return null;
            } else {
                $lower = 0x80;
                $upper = 0xBF;
                $point = ($point << 6) | ($b & 0x3F);
            }
            $seen++;
        }
        return $point;
    }
}
