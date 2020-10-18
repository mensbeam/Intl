<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

abstract class SingleByteEncoding extends AbstractEncoding implements Coder, Decoder {
    protected $selfSynchronizing = true;

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
            return static::TABLE_DEC_CHAR[$p - 128] ?? UTF8::encode($this->errDec($this->errMode, $this->posChar, $this->posChar));
        }
    }

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
            return static::TABLE_DEC_CODE[$p - 128] ?? $this->errDec($this->errMode, $this->posChar, $this->posChar);
        }
    }

    public static function encode(int $codePoint, bool $fatal = true): string {
        if ($codePoint < 0 || $codePoint > 0x10FFFF) {
            throw new EncoderException("Encountered code point outside Unicode range ($codePoint)", self::E_INVALID_CODE_POINT);
        } elseif ($codePoint < 128) {
            return chr($codePoint);
        } else {
            return static::TABLE_ENC[$codePoint] ?? static::errEnc(!$fatal, $codePoint);
        }
    }

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

    /** @codeCoverageIgnore */
    protected function seekBack(int $distance): int {
        // stub: not used
        return 0;
    }

    public function posByte(): int {
        return $this->posChar;
    }

    public function lenChar(): int {
        return $this->lenByte;
    }

    public function eof(): bool {
        return $this->posChar >= $this->lenByte;
    }
}
