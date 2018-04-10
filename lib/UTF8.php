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
            // otherwise determine the byte-length of the UTF-8 character
            $l = self::l($b);
            if (!$l && $errMode==self::M_SKIP) {
                // if the byte is invalid and we're supposed to skip, advance the position and start over
                $pos++;
                goto start;
            } elseif (!$l && $errMode == self::M_REPLACE) {
                // if the byte is invalid and we're supposed to replace, return a replacement character
                $next = $pos + 1;
                return self::$replacementChar;
            } elseif (!$l) {
                // if the byte is invalid and we're supposed to halt, halt
                throw new \Exception;
            } else {
                // otherwise collect valid mid-sequence bytes into a buffer until the whole character is retrieved or an invalid byte is encountered
                $buffer = $b;
                do {
                    $b = (++$pos < strlen($string)) ? $string[$pos] : "";
                    if ($b >= "\x80" && $b <= "\xBF") {
                        // if the byte is valid, add it to the buffer
                        $buffer .= $b;
                    } elseif ($errMode==self::M_SKIP) {
                        // if the byte is invalid and we're supposed to skip, start over from the current position
                        goto start;
                    } elseif ($errMode==self::M_REPLACE) {
                        // if the byte is invalid and we're supposed to replace, return a replacement character
                        $next = $pos;
                        return self::$replacementChar;
                    } else {
                        // if the byte is invalid and we're supposed to halt, halt
                        throw new \Exception;
                    }
                } while (strlen($buffer) < $l);
                // return the filled buffer and the position of the next byte
                $next = $pos + 1;
                return $buffer;
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
            $pos // stop once the start of the string has been reached
        ); 
        // get the expected length of the character starting at the last examined byte
        $l = self::l($b);
        if ($l==$t) {
            // if the expected length matches the number of examined bytes, the character is valid
            return $pos;
        } elseif ($errMode==self::M_SKIP) {
            // if we're expected to ignore invalid sequences:
            if ($l && $t > $l) {
                // if the last examined byte is the start of a sequence and we have more than the right amount of continuation characters, return the current position
                return $pos;
            } elseif (!$pos) {
                // if we're already at the start of the string, give up
                return $pos;
            } else {
                // otherwise skip over the last examined byte and start over
                $pos--;
                goto start;
            }
        } elseif ($errMode==self::M_REPLACE) {
            // if we're expected to treat invalid sequences as replacement characters, return 
            // the offset of the most recently examined byte if it is the start of a multi-byte
            // sequence, or the starting offset otherwise: in the latter case the original byte
            // is itself a replacement character position
            return ($l > 1) ? $pos: $s;
        } else {
            // if the character is invalid and we're expected to halt, halt
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

    /** 
     * Returns the expected byte length of a UTF-8 character starting with byte $b 
     * 
     * If the byte is not the start of a UTF-8 sequence, 0 is returned
     */
    protected static function l(string $b): int {
        if ($b >= "\xC0" && $b <= "\xDF") { // two-byte character
            return 2;
        } elseif ($b >= "\xE0" && $b <= "\xEF") { // three-byte character
            return 3;
        } elseif ($b >= "\xF0" && $b <= "\xF7") { // four-byte character
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
