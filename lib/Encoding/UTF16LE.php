<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class UTF16LE extends UTF16 {
    const BE = false;
    const NAME = "UTF-16LE";
    const LABELS = [
        "csunicode",
        "iso-10646-ucs-2",
        "ucs-2",
        "unicode",
        "unicodefeff",
        "utf-16",
        "utf-16le"
    ];
}
