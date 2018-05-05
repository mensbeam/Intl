<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\UTF8;

abstract class UTF8 {
    public static $replacementChar = "\u{FFFD}";
    public static $errMode = self::M_REPLACE;

    const M_REPLACE = 0;
    const M_SKIP = 1;
    const M_HALT = 2;

    /** Starting from byte offset $pos, advance $num characters through $string and return the byte offset of the found character
     *
     * If $num is negative, the operation will be performed in reverse
     *
     * If $pos is omitted, the start of the string will be used for a forward seek, and the end for a reverse seek
     */
    public static function seek(string $string, int $num, int $pos = null, int $errMode = null): int {
        $errMode = $errMode ?? self::$errMode;
        if ($num > 0) {
            $pos = $pos ?? 0;
            do {
                $c = self::get($string, $pos, $pos, $errMode); // the current position is getting overwritten with the next position, by reference
            } while (--$num && $c != ""); // stop after we have skipped the desired number of characters, or reached EOF
            return $pos;
        } elseif ($num < 0) {
            $pos = $pos ?? strlen($string);
            if (!$pos) {
                // if we're already at the start of the string, we can't go further back
                return $pos;
            }
            $num = abs($num);
            do {
                $pos = self::sync($string, $pos -1, $errMode);
                $num--;
            } while ($num && $pos);
            return $pos;
        } else {
            // seeking zero characters is equivalent to a sync
            return self::sync($string, $pos, $errMode);
        }
    }

    /** Synchronize to the byte offset of the start of the nearest character at or before byte offset $pos */
    public static function sync(string $string, int $pos, int $errMode = null): int {
        $errMode = $errMode ?? self::$errMode;
        start:
        if (!$pos || $pos >= strlen($string)) {
            // if we're at the start of the string or past its end, then this is the character start
            return $pos;
        }
        // save the start position for later, and increment before the coming decrement loop
        $s = $pos++;
        // examine the current byte and skip up to three continuation bytes, going backward and counting the number of examined bytes (between 1 and 4)
        $t = 0;
        do {
            $pos--;
            $t++;
            $b = @$string[$pos];
        } while (
            $b >= "\x80" && $b <= "\xBF" && // continuation bytes
            ($t < 4 || $errMode==self::M_SKIP) && // stop after four bytes, unless we're skipping invalid sequences
            $pos > 0 // stop once the start of the string has been reached
        );
        // attempt to extract a code point at the current position
        $p = self::ord($string, $pos, $n, self::M_REPLACE);
        // if the position of the character after the one we just consumed is earlier than our start position,
        // then there was at least one invalid sequence between the consumed character and the start position
        if ($n < $s) {
            if ($errMode==self::M_SKIP) {
                // if we're supposed to skip invalid sequences, there is no need to do anything
            } elseif ($errMode==self::M_REPLACE) {
                // if we're supposed to replace invalid sequences, return the starting offset: it is itself a character
                return $s;
            } else {
                // otherwise if the character is invalid and we're expected to halt, halt
                throw new \Exception;
            }
        }
        // if the consumed character is valid, return the current position
        if (is_int($p)) {
            return $pos;
        } elseif ($errMode==self::M_SKIP) {
            // if we're supposed to skip invalid sequences:
            if ($pos < 1) {
                // if we're already at the start of the string, give up
                return $pos;
            } else {
                // otherwise skip over the last examined byte and start over
                $pos--;
                goto start;
            }
        } elseif ($errMode==self::M_REPLACE) {
            // if we're supposed to replace invalid sequences, return the current offset: we've synchronized
            return $pos;
        } else {
            // otherwise if the character is invalid and we're expected to halt, halt
            throw new \Exception;
        }
    }

    public static function len(string $string, int $start = 0, int $end = null, int $errMode = null): int {
        $errMode = $errMode ?? self::$errMode;
        $end = $end ?? strlen($string);
        if (substr($string, $start, ($end - $start)) =="") {
            return 0;
        }
        $count = 0;
        $pos = $start;
        do {
            $c = self::get($string, $pos, $pos, $errMode);
        } while ($c != "" && ++$count && $pos < $end);
        return $count;
    }

    public static function substr(string $str, int $start = 0, int $length = null, &$next = null, int $errMode = null): string {
        $errMode = $errMode ?? self::$errMode;
        if ($length > 0) {
            $pos = $start;
            $buffer = "";
            do {
                $c = self::get($string, $pos, $pos, $errMode); // the current position is getting overwritten with the next position, by reference
                $buffer .= $c;
            } while (--$length && $c != ""); // stop after we have skipped the desired number of characters, or reached EOF
            $next = $pos;
            return $buffer;
        } else {
            $next = self::sync($string, $start, $errMode);
            return "";
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
