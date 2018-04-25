<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\UTF8;

require __DIR__."/../tests/bootstrap.php";

$files = [
    'HTML single-page specification' => ["https://html.spec.whatwg.org/", "html.html"],
    'English article on Canada' => ["https://en.wikipedia.org/wiki/Canada", "canada.html"],
    'Greek article on Greece' => ["https://el.wikipedia.org/wiki/Ελλάδα", "greece.html"],
    'Japanese article on Japan' => ["https://ja.wikipedia.org/wiki/日本", "japan.html"],
];

$tests = [
    'Intl code points' => ["intl", function(string $text): int {
        $t = 0;
        $i = \IntlBreakIterator::createCodePointInstance();
        $i->setText($text);
        foreach ($i as $o) {
            $p = $i->getLastCodePoint();
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
    'PCRE split characters' => ["pcre", function(string $text): int {
        $t = 0;
        foreach (preg_split('//u',  $text) as $c) {
            $p = $c;
            $t++;
        }
        return $t;
    }],
];

if (!file_exists(__DIR__."/docs/")) {
    mkdir(__DIR__."/docs/");
}

foreach($files as $fName => $file) {
    list($url, $file) = $file;
    $file = __DIR__."/docs/$file";
    if (!file_exists($file)) {
        $text = file_get_contents($url);
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
            echo number_format($t, 3)." ($n characters)\n";
        }
    }
}
