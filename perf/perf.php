<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\UTF8;

require __DIR__."/../tests/bootstrap.php";

$files = [
    'ASCII text'      => ["ascii.txt", false, 0, 0x7F],
    'Multi-byte text' => ["multi.txt", false, 0x80, 0x10FFFF],
];

$tests = [
    'Native characters' => ["", function(string $text): int {
        $t = 0;
        $pos = 0;
        $eof = strlen($text);
        while ($pos <= $eof) {
            $p = UTF8::get($text, $pos, $pos);
            $t++;
        }
        return $t;
    }],
    'Intl characters' => ["intl", function(string $text): int {
        $t = 0;
        $i = \IntlBreakIterator::createCodePointInstance();
        $i->setText($text);
        foreach ($i as $b) {
            $p = \IntlChar::chr($i->getLastCodePoint());
            $t++;
        }
        return $t;
    }],
    'Native code points' => ["", function(string $text): int {
        $t = 0;
        $pos = 0;
        $eof = strlen($text);
        while ($pos <= $eof) {
            $p = UTF8::ord($text, $pos, $pos);
            $t++;
        }
        return $t;
    }],
];

if (!file_exists(__DIR__."/docs/")) {
    mkdir(__DIR__."/docs/");
}

foreach($files as $fName => $file) {
    list($file, $binary, $min, $max) = $file;
    $file = __DIR__."/docs/$file";
    if (!file_exists($file)) {
        $text = gen_string(1000000, $binary, $min, $max);
        file_put_contents($file, $text);
    } else {
        $text = file_get_contents($file);
    }
    echo "$fName:\n";
    foreach($tests as $tName => $test) {
        list($req, $test) = $test;
        if ($req && !extension_loaded($req)) {
            continue;
        } else {
            echo "    $tName: ";
            $t = [];
            for ($a = 0; $a < 5; $a++) {
                $s = microtime(true);
                $n = $test($text);
                $t[$a] = microtime(true) - $s;
            }
            $t = array_sum($t) / sizeof($t);
            echo number_format($t, 3)."\n";
        }
    }
}

function gen_string(int $size, bool $binary, int $lowest, int $highest): string {
    $a = 0;
    $out = "";
    if ($binary) {
        while ($a++ < $size) {
            $c = chr(mt_rand(0, 255));
            $out = "$out$c";
        }
        return $out;
    } else {
        while ($a++ < $size) {
            $p = mt_rand($lowest, $highest);
            if ($p >= 55296 && $p <= 57343) {
                $p = 0xFFFD;
            }
            $c = \IntlChar::chr($p);
            $out = "$out$c";
        }
        return $out;
    }
}
