<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace JKingWeb\URI;

abstract class UTF8 {
    public static $replacementChar = "\u{FFFD}";
    public static $errMode = self::M_REPLACE;

    const M_REPLACE = 0;
    const M_SKIP = 1;
    const M_HALT = 2;

    /** Retrieve a character from $string starting at byte offset $pos
     * 
     * $next is a variable in which to store the next byte offset at which a character starts
     * 
     * The returned character may be a replacement character, or the empty string if $pos is beyond the end of $string
     */
    public static function get(string $string, int $pos, &$next = null, int $errMode = null): string {
        start:
        // get the byte at the specified position
        $b = ($pos < strlen($string)) ? $string[$pos] : "";
        if ($b < "\x80" || $b=="") {
            // if the byte is an ASCII character or end of input, simply return it
            $next = $pos + 1;
            return $b;
        } else {
            $errMode = $errMode ?? self::$errMode;
            // otherwise determine the numeric code point of the character, as well as the position of the next character
            $p = self::ord($string, $pos, $next, self::M_REPLACE);
            if (is_int($p)) {
                // if the character is valid, return its serialization
                // we do a round trip (bytes > code point > bytes) to normalize overlong sequences
                return self::chr($p);
            } elseif ($errMode==self::M_REPLACE) {
                // if the byte is invalid and we're supposed to replace, return a replacement character
                return self::$replacementChar;
            } elseif ($errMode==self::M_SKIP) {
                // if the character is invalid and we're supposed to skip invalid characters, advance the position and start over
                $pos = $next;
                goto start;
            } else {
                // if the byte is invalid and we're supposed to halt, halt
                throw new \Exception;
            }
        }
    }

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
            $b = ($pos < strlen($string)) ? $string[$pos] : "";
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

    /** Decodes the first UTF-8 character from a byte sequence into a numeric code point, starting at byte offset $pos
     * 
     * Upon success, returns the numeric code point of the character, an integer between 0 and 1114111
     * 
     * Upon error, returns false; if $char is the empty string or $pos is beyond the end of the string, null is returned
     * 
     * $next is a variable in which to store the next byte offset at which a character starts
     */
    public static function ord(string $string, int $pos = 0, &$next = null, int $errMode = null) {
        // this function effectively implements https://encoding.spec.whatwg.org/#utf-8-decoder
        // though it differs from a slavish implementation because it operates on only a single 
        // character rather than a whole stream
        $eof = strlen($string);
        start:
        $point = null;
        $seen = 0;
        $needed = 0;
        $next = $pos + 1;
        $lower = "\x80";
        $upper = "\xBF";
        while ($pos < $eof && !($needed && $seen >= $needed)) {
            $b = $string[$pos++];
            $next = $pos;
            $seen++;
            if(!$needed) {
                $needed = self::l($b);
                switch($needed) {
                    case 1:
                        $point = ord($b);
                        break;
                    case 2:
                        $point = ord($b) & 0x1F;
                        break;
                    case 3:
                        if ($b=="\xE0") {
                            $lower = "\xA0";
                        } elseif ($b=="\xED") {
                            $upper = "\x9F";
                        }
                        $point = ord($b) & 0xF;
                        break;
                    case 4:
                        if ($b=="\xF0") {
                            $lower = "\x90";
                        } elseif ($b=="\xF4") {
                            $upper = "\x8F";
                        }
                        $point = ord($b) & 0x7;
                        break;
                    case 0:
                        switch ($errMode ?? self::$errMode) {
                            case self::M_SKIP:
                                goto start;
                            case self::M_REPLACE:
                                return false;
                            default:
                                throw new \Exception;
                        }
                        break;
                }
            } elseif ($b < $lower || $b > $upper) {
                switch ($errMode ?? self::$errMode) {
                    case self::M_SKIP:
                        goto start;
                    case self::M_REPLACE:
                        return false;
                    default:
                        throw new \Exception;
                }
            } else {
                $lower = "\x80";
                $upper = "\xBF";
                $point = ($point << 6) | (ord($b) & 0x3F);
            }
        }
        if ($seen < $needed) {
            switch ($errMode ?? self::$errMode) {
                case self::M_SKIP:
                    goto start;
                case self::M_REPLACE:
                    return false;
                default:
                    throw new \Exception;
            }
        } else {
            return $point;
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
            $temp = $codePoint >> (6 * ($count - 1));
            $bytes .= chr(0x80 | ($temp & 0x3F));
            $count--;
        }
        return $bytes;
    }

    /** 
     * Returns the expected byte length of a UTF-8 character starting with byte $b 
     * 
     * If the byte is not the start of a UTF-8 sequence, 0 is returned
     */
    protected static function l(string $b): int {
        if ($b >= "\xC2" && $b <= "\xDF") { // two-byte character
            return 2;
        } elseif ($b >= "\xE0" && $b <= "\xEF") { // three-byte character
            return 3;
        } elseif ($b >= "\xF0" && $b <= "\xF4") { // four-byte character
            return 4;
        } elseif ($b < "\x80") { // ASCII byte: one-byte character
            return 1;
        } elseif ($b == "") { // end of input: pretend it's a valid single-byte character
            return 1;
        } else { // invalid byte
            return 0;
        }
    }
}
