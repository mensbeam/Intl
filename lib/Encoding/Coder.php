<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

interface Coder {
    public const E_INVALID_CODE_POINT = 1;
    public const E_UNAVAILABLE_CODE_POINT = 3;
    public const E_UNAVAILABLE_ENCODER = 4;

    /** Returns the encoding of $codePoint as a byte string
     *
     * @param int $codePoint The Unicode code point to encode. If less than 0 or greater than 1114111, an exception is thrown
     * @param bool $fatal Whether an exception will be thrown if the code point cannot be encoded into a character; if false HTML character references will be substituted
     */
    public static function encode(int $codePoint, bool $fatal = true): string;
}
