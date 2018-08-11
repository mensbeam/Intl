<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

interface StatelessEncoding extends Encoding {

    /** Returns the encoding of $codePoint as a byte string
     *
     * If $codePoint is less than 0 or greater than 1114111, an exception is thrown
     *
     * If $fatal is true, an exception will be thrown if the code point cannot be encoded into a character; otherwise HTML character references will be substituted
     */
    public static function encode(int $codePoint, bool $fatal = true): string;
}
