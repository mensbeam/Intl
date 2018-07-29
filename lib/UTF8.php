<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\UTF8;

abstract class UTF8 {

    /** Retrieve a character from $string starting at byte offset $pos
     *
     * $next is a variable in which to store the next byte offset at which a character starts
     *
     * The returned character may be a replacement character, or the empty string if $pos is beyond the end of $string
     */
    public static function get(string $string, int $pos, &$next = null): string {
        start:
        // get the byte at the specified position
        $b = @$string[$pos];
        if (ord($b) < 0x80) {
            // if the byte is an ASCII character or end of input, simply return it
            if ($b !== "") {
                $next = $pos + 1;
            } else {
                $next = $pos;
            }
            return $b;
        } else {
            // otherwise determine the numeric code point of the character, as well as the position of the next character
            $p = self::ord($string, $pos, $next);
            return is_int($p) ? self::chr($p) : "\u{FFFD}";
        }
    }

    /** Starting from byte offset $pos, advance $num characters through $string and return the byte offset of the found character
     *
     * If $num is negative, the operation will be performed in reverse
     *
     * If $pos is omitted, the start of the string will be used for a forward seek, and the end for a reverse seek
     */
    public static function seek(string $string, int $num, int $pos = null): int {
        if ($num > 0) {
            $pos = $pos ?? 0;
            do {
                $c = self::get($string, $pos, $pos); // the current position is getting overwritten with the next position, by reference
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
                $pos = self::sync($string, $pos -1);
                $num--;
            } while ($num && $pos);
            return $pos;
        } else {
            // seeking zero characters is equivalent to a sync
            return self::sync($string, $pos);
        }
    }

    /** Synchronize to the byte offset of the start of the nearest character at or before byte offset $pos */
    public static function sync(string $string, int $pos): int {
        $b = ord(@$string[$pos]);
        if ($b < 0x80) {
            // if the byte is an ASCII byte or the end of input, then this is already a synchronized position
            return min(max($pos,0), strlen($string));
        } else {
            $s = $pos;
            while ($b >= 0x80 && $b <= 0xBF && $pos > 0 && ($s - $pos) < 3) { // go back at most three bytes, no further than the start of the string, and only as long as the byte remains a continuation byte
                $b = ord(@$string[--$pos]);
            }
            if (is_null(self::ord($string, $pos, $next))) {
                return $s;
            } else {
                return ($next > $s) ? $pos : $s;
            }
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

    /** Decodes the first UTF-8 character from a byte sequence into a numeric code point, starting at byte offset $pos
     *
     * Upon success, returns the numeric code point of the character, an integer between 0 and 1114111
     *
     * Upon error, returns null; if $char is the empty string or $pos is beyond the end of the string, false is returned
     *
     * $next is a variable in which to store the next byte offset at which a character starts
     */
    public static function ord(string $string, int $pos = 0, &$next = null) {
        // this function effectively implements https://encoding.spec.whatwg.org/#utf-8-decoder
        // though it differs from a slavish implementation because it operates on only a single
        // character rather than a whole stream
        // optimization for ASCII characters
        $b = @$string[$pos];
        if ($b=="") {
            $next = $pos;
            return false;
        } elseif (($b = ord($b)) < 0x80) {
            $next = $pos + 1;
            return $b;
        }
        $point = 0;
        $seen = 0;
        $needed = 1;
        $lower = 0x80;
        $upper = 0xBF;
        while ($seen < $needed) {
            $b = ord(@$string[$pos++]);
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
                    $next = $pos;
                    return null;
                }
            } elseif ($b < $lower || $b > $upper) {
                $next = $pos - 1;
                return null;
            } else {
                $lower = 0x80;
                $upper = 0xBF;
                $point = ($point << 6) | ($b & 0x3F);
            }
            $seen++;
        }
        $next = $pos;
        return $point;
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
