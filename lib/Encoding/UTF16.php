<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

abstract class UTF16 implements Encoding {
    use GenericEncoding;
    
    protected $dirtyEOF = 0;

    public function nextCode() {
        $lead_b = null;
        $lead_s = null;
        $this->posChar++;
        while (($b = @$this->string[$this->posByte++]) !== "") {
            $b = ord($b);
            if (is_null($lead_b)) {
                $lead_b = $b;
                continue;
            } else {
                if (static::BE) {
                    $code = ($lead_b << 8) + $b;
                } else {
                    $code = ($b << 8) + $lead_b;
                }
                $lead_b = null;
                if (!is_null($lead_s)) {
                    if ($code >= 0xDC00 && $code <= 0xDFFF) {
                        return 0x10000 + (($lead_s - 0xD800) << 10) + ($code - 0xDC00);
                    } elseif ($this->allowSurrogates) {
                        $this->posByte -= 2;
                        return $lead_s;
                    } else {
                        $this->posByte -= 2;
                        return self::err($this->errMode, [$this->posChar - 1, $this->posByte - 2]);
                    }
                } else {
                    if ($code >= 0xD800 && $code <= 0xDBFF) {
                        $lead_s = $code;
                        continue;
                    } elseif ($code >= 0xDC00 && $code <= 0xDFFF) {
                        if ($this->allowSurrogates) {
                            return $code;
                        } else {
                            return self::err($this->errMode, [$this->posChar - 1, $this->posByte - 2]);
                        }
                    } else {
                        return $code;
                    }
                }
            }
        }
        $this->posByte--;
        if (($lead_b + $lead_s) == 0) {
            // clean EOF
            $this->posChar--;
            return false;
        } else {
            // dirty EOF; note how many bytes the last character had
            // properly synchronizing UTF-16 is possible without retaining this information, but retaining it makes the task easier
            $this->dirtyEOF = ($lead_s && $lead_b ? 3 : ($lead_s ? 2 : 1));
            return self::err($this->errMode, [$this->posChar - 1, $this->posByte - $this->dirtyEOF]);
        }
    }

    public function nextChar(): string {
        // get the byte at the current position
        $b = @$this->string[$this->posByte];
        if ($b === "") {
            // if the byte is end of input, simply return it
            return "";
        } else {
            // otherwise return the serialization of the code point at the current position
            return UTF8::encode($this->nextCode());
        }
    }

    /** Implements backward seeking $distance characters */
    protected function seekBack(int $distance): int {
        if ($this->posByte >= $this->lenByte && $this->dirtyEOF > 0) {
            // if we are at the end of the string and it did not terminate cleanly, go back the correct number of dirty bytes to seek through the last character
            $this->posByte -= $this->dirtyEOF;
            $distance--;
            $this->posChar--;
        }
        while ($distance > 0 && $this->posByte > 0) {
            $distance--;
            $this->posChar--;
            if ($this->posByte < 4) {
                // if we're less than four bytes into the string, the previous character is necessarily double-byte
                $this->posByte -= 2;
            } else {
                // otherwise go back four bytes and consume a character
                $start = $this->posByte;
                $this->posByte -= 4;
                $this->posChar--;
                $this->nextCode();
                if ($this->posByte == $start) {
                    // if we're back at our starting position the character was four bytes
                    $this->posByte -= 4;
                } else {
                    // otherwise we're already where we need to be
                }
            }
        }
        return $distance;
    }
}
