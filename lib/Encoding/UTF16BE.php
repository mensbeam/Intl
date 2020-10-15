<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class UTF16BE extends UTF16 {
    const BE = true;
    const NAME = "UTF-16BE";
    const LABELS = [
        "unicodefffe",
        "utf-16be",
    ];
}
