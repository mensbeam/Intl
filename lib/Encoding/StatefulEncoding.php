<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

interface StatefulEncoding extends Encoding {

    /** Returns the encoding of $codePoints as a byte string
     *
     * If any element of $codePoints is less than 0 or greater than 1114111, an exception is thrown
     *
     * If $fatal is true, an exception will be thrown if any code point cannot be encoded into a character; otherwise HTML character references will be substituted
     */
    public static function encode(array $codePoints, bool $fatal = true): string;
}
