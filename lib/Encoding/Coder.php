<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

interface Coder {
    const E_INVALID_CODE_POINT = 1;
    const E_UNAVAILABLE_CODE_POINT = 3;
    const E_UNAVAILABLE_ENCODER = 4;

    /** Returns the encoding of $codePoint as a byte string
     *
     * If $codePoint is less than 0 or greater than 1114111, an exception is thrown
     *
     * If $fatal is true, an exception will be thrown if the code point cannot be encoded into a character; otherwise HTML character references will be substituted
     */
    public static function encode(int $codePoint, bool $fatal = true): string;
}
