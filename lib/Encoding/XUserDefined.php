<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class XUserDefined extends AbstractEncoding implements Coder, Decoder {
    public const NAME = "x-user-defined";
    public const LABELS = ["x-user-defined"];

    /** Retrieve the next character in the string, in UTF-8 encoding
     *
     * The returned character may be a replacement character, or the empty string if the end of the string has been reached
     */
    public function nextChar(): string {
        // get the byte at the current position
        $b = $this->string[$this->posChar] ?? "";
        if ($b === "") {
            return "";
        }
        $this->posChar++;
        $this->posByte++;
        $p = ord($b);
        if ($p < 0x80) {
            // if the byte is an ASCII character or end of input, simply return it
            return $b;
        } else {
            return UTF8::encode(0xF700 + $p);
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
        $b = $this->string[$this->posChar] ?? "";
        if ($b === "") {
            return false;
        }
        $this->posChar++;
        $this->posByte++;
        $p = ord($b);
        if ($p < 0x80) {
            // if the byte is an ASCII character or end of input, simply return it
            return $p;
        } else {
            return 0xF700 + $p;
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
                $this->posByte--;
                $distance--;
            }
            return $distance;
        } else {
            return 0;
        }
    }
    
    public static function encode(int $codePoint, bool $fatal = true): string {
        if ($codePoint < 0 || $codePoint > 0x10FFFF) {
            throw new EncoderException("Encountered code point outside Unicode range ($codePoint)", self::E_INVALID_CODE_POINT);
        } elseif ($codePoint < 0x80) {
            return chr($codePoint);
        } elseif ($codePoint >= 0xF780 && $codePoint <= 0xF7FF) {
            return chr($codePoint - 0xF780 + 0x80);
        } else {
            return self::errEnc(!$fatal, $codePoint);
        }
    }

    /** @codeCoverageIgnore */
    protected function seekBack(int $distance): int {
        // stub: not used
        return 0;
    }

    /** Calculates the length of the string in code points
     *
     * Note that this may involve processing to the end of the string
     */
    public function lenChar(): int {
        return $this->lenByte;
    }

    /** Returns whether the character pointer is at the end of the string */
    public function eof(): bool {
        return $this->posChar >= $this->lenByte;
    }
}
