<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace JKingWeb\URI;

class URL extends URI {
    public function __construct(string $url, string $base = null) {
        $parsedBase = null;
        if (!is_null($base)) {
            $parsedBase = $this->basicUrlParser($base);
            if (is_null($parsedBase)) {
                throw new \TypeError;
            }
        }
        $parsedUrl = $this->basicUrlParser($url, $parsedBase);
        var_export($parsedUrl);
    }
}
