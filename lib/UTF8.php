<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\UTF8;

class UTF8 {
    protected $string;
    protected $posByte = 0;
    protected $posChar = 0;


    public function __construct(string $string) {
        $this->string = $string;
    }

    public function posByte(): int {
        return $this->posByte;
    }

    public function posChr(): int {
        return $this->posChar;
    }

    /** Retrieve the next character in the string
     *
     * The returned character may be a replacement character, or the empty string if the end of the string has already been reached
     */
    public function nextChr(): string {
        // get the byte at the current position
        $b = @$this->string[$this->posByte];
        if ($b === "") {
            return "";
        } elseif (ord($b) < 0x80) {
            // if the byte is an ASCII character or end of input, simply return it
            $this->posChar++;
            $this->posByte++;
            return $b;
        } else {
            // otherwise return the serialization of the code point at the current position
            return UTF8::chr($this->nextOrd() ?? 0xFFFD);
        }
    }

    /** Decodes the next UTF-8 character from the string and returns its code point number
     *
     * If a character could not be decoded, null is returned; if the end of the string has already been reached, false is returned
     */
    public function nextOrd() {
        // this function effectively implements https://encoding.spec.whatwg.org/#utf-8-decoder
        // though it differs from a slavish implementation because it operates on only a single
        // character rather than a whole stream
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

    /** Advance $distance characters through the string
     *
     * If $distance is negative, the operation will be performed in reverse
     *
     * If the end (or beginning) of the string was reached before the end of the operation, the remaining number of requested characters is returned
     */
    public function seek(int $distance): int {
        if ($distance > 0) {
            if ($this->posByte == strlen($this->string)) {
                // if we're already at the end of the string, we can't go further
                return $distance;
            }
            do {
                // get the next code point; this automatically increments the character position
                $p = $this->nextOrd();
            } while (--$distance && $p !== false); // stop after we have skipped the desired number of characters, or reached EOF
            return $distance;
        } elseif ($distance < 0) {
            $distance = abs($distance);
            if (!$this->posByte) {
                // if we're already at the start of the string, we can't go further back
                return $distance;
            }
            do {
                $this->sync($this->posByte - 1);
                // manually decrement the character position
                $this->posChar--;
            } while (--$distance && $this->posByte);
            return $distance;
        } else {
            return 0;
        }
    }

    /** Synchronize to the byte offset of the start of the nearest character at or before byte offset $pos */
    protected function sync(int $pos) {
        $b = ord(@$this->string[$pos]);
        if ($b < 0x80) {
            // if the byte is an ASCII byte or the end of input, then this is already a synchronized position
            $this->posByte = $pos;
        } else {
            $s = $pos;
            while ($b >= 0x80 && $b <= 0xBF && $pos > 0 && ($s - $pos) < 3) { // go back at most three bytes, no further than the start of the string, and only as long as the byte remains a continuation byte
                $b = ord(@$this->string[--$pos]);
            }
            $this->posByte = $pos;
            // decrement the character position because nextOrd() increments it
            $this->posChar--;
            if (is_null($this->nextOrd())) {
                $this->posByte = $s;
            } else {
                $this->posByte = ($this->posByte > $s) ? $pos : $s;
            }
        }
    }

    /** Returns the UTF-8 encoding of $codePoint
     *
     * If $codePoint is less than 0 or greater than 1114111, an empty string is returned
     */
    public static function chr(int $codePoint): string {
        // this function implements https://encoding.spec.whatwg.org/#utf-8-encoder
        if ($codePoint < 0 || $codePoint > 0x10FFFF) {
            return "";
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
}
