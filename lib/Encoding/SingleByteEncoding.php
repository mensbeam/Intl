<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

abstract class SingleByteEncoding implements StatelessEncoding {
    use GenericEncoding;

    /** Retrieve the next character in the string, in UTF-8 encoding
     *
     * The returned character may be a replacement character, or the empty string if the end of the string has been reached
     */
    public function nextChar(): string {
        // get the byte at the current position
        $b = @$this->string[$this->posChar];
        if ($b === "") {
            return "";
        }
        $this->posChar++;
        $p = ord($b);
        if ($p < 0x80) {
            // if the byte is an ASCII character or end of input, simply return it
            return $b;
        } else {
            return static::TABLE_DEC_CHAR[$p - 128] ?? UTF8::encode(static::err($this->errMode, [$this->posChar, $this->posChar]));
        }
    }

    /** Decodes the next character from the string and returns its code point number
     *
     * If the end of the string has been reached, false is returned
     *
     * @return int|bool
     */
    public function nextCode() {
        // get the byte at the current position
        $b = @$this->string[$this->posChar];
        if ($b === "") {
            return false;
        }
        $this->posChar++;
        $p = ord($b);
        if ($p < 0x80) {
            // if the byte is an ASCII character or end of input, simply return it
            return $p;
        } else {
            return static::TABLE_DEC_CODE[$p - 128] ?? static::err($this->errMode, [$this->posChar, $this->posChar]);
        }
    }

    /** Returns the encoding of $codePoint as a byte string
     *
     * If $codePoint is less than 0 or greater than 1114111, an exception is thrown
     *
     * If $fatal is true, an exception will be thrown if the code point cannot be encoded into a character; otherwise HTML character references will be substituted
     */
    public static function encode(int $codePoint, bool $fatal = true): string {
        if ($codePoint < 0 || $codePoint > 0x10FFFF) {
            throw new EncoderException("Encountered code point outside Unicode range ($codePoint)", self::E_INVALID_CODE_POINT);
        } elseif ($codePoint < 128) {
            return chr($codePoint);
        } else {
            return static::TABLE_ENC[$codePoint] ?? static::err($fatal ? self::MODE_FATAL_ENC : self::MODE_HTML, $codePoint);
        }
    }

    /** Advance $distance characters through the string
     *
     * If $distance is negative, the operation will be performed in reverse
     *
     * If the end (or beginning) of the string was reached before the end of the operation, the remaining number of requested characters is returned
     */
    public function seek(int $distance): int {
        if ($distance > 0) {
            while ($this->posChar < $this->lenByte && $distance > 0) {
                $this->nextCode();
                $distance--;
            }
            return $distance;
        } elseif ($distance < 0) {
            $distance = abs($distance);
            while ($this->posChar > 0 && $distance > 0) {
                $this->posChar--;
                $distance--;
            }
            return $distance;
        } else {
            return 0;
        }
    }

    /** Returns the current byte position of the decoder */
    public function posByte(): int {
        return $this->posChar;
    }

    /** Calculates the length of the string in code points
     *
     * Note that this may involve processing to the end of the string
    */
    public function len(): int {
        return $this->lenByte;
    }
}
