<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

interface Decoder {
    public const E_INVALID_BYTE = 2;

    /** Constructs a new decoder
     * 
     * @param string $string The string to decode
     * @param bool $fatal If true, throw enceptions when encountering invalid input. If false, substitute U+FFFD REPLACEMENT CHARACTER instead
     * @param bool $allowSurrogates If true, treats surrogate characters as valid input; this only affects UTF-8 and UTF-16 encodings
     */
    public function __construct(string $string, bool $fatal = false, bool $allowSurrogates = false);

    /** Returns the current byte position of the decoder */
    public function posByte(): int;

    /** Returns the current character position of the decoder */
    public function posChar(): int;

    /** Retrieve the next character in the string, in UTF-8 encoding
     *
     * The returned character may be a replacement character, or the empty string if the end of the string has been reached
     */
    public function nextChar(): string;

    /** Decodes the next character from the string and returns its code point number
     *
     * If the end of the string has been reached, false is returned
     *
     * @return int|false
     */
    public function nextCode();

    /** Advance $distance characters through the string
     *
     * If the end (or beginning) of the string was reached before the end of the operation, the remaining number of requested characters is returned
     *
     * @param int $distance The number of characters to advance. If negative, the operation will seek back toward the beginning of the string
     */
    public function seek(int $distance): int;

    /** Seeks to the start of the string
     *
     * This is usually faster than using the seek method for the same purpose
     */
    public function rewind(): void;

    /** Retrieves the next $num characters (in UTF-8 encoding) from the string without advancing the character pointer
     *
     * @param int $num The number of characters to retrieve
     */
    public function peekChar(int $num = 1): string;

    /** Retrieves the next $num code points from the string, without advancing the character pointer
     *
     * @param int $num The number of code points to retrieve
     */
    public function peekCode(int $num = 1): array;

    /** Calculates the length of the string in bytes */
    public function lenByte(): int;

    /** Calculates the length of the string in code points
     *
     * Note that this may involve processing to the end of the string
     */
    public function lenChar(): int;

    /** Returns whether the character pointer is at the end of the string */
    public function eof(): bool;

    /** Generates an iterator which steps through each character in the string */
    public function chars(): \Generator;

    /** Generates an iterator which steps through each code point in the string  */
    public function codes(): \Generator;

    /** Fast-forwards through a span of ASCII characters matching the supplied mask, returning any consumed characters
     * 
     * The mask must consist only of ASCII characters. 
     * 
     * Note that if the empty string is returned, this does not necessarily signal the end of the string
     * 
     * @param string $mask The set of ASCII characters to match
     * @param int $length The maximum number oof characters to advance by
     */
    public function asciiSpan(string $mask, ?int $length = null): string;

    /** Fast-forwards through a span of ASCII characters not matching the supplied mask, returning any consumed characters
     * 
     * The mask must consist only of ASCII characters. 
     * 
     * Note that if the empty string is returned, this does not necessarily signal the end of the string
     * 
     * @param string $mask The set of ASCII characters to not match
     * @param int $length The maximum number oof characters to advance by
     */
    public function asciiSpanNot(string $mask, ?int $length = null): string;
}
