<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

require __DIR__."/../tests/bootstrap.php";

$files = [
    'Best case'      => ["ascii.txt",    [100,0,0,0]],
    'Worst case'     => ["multi.txt",    [0,0,0,100]],
    'Japanese'       => ["japanese.txt", [85,0,15,0]],
    'Greek'          => ["greek.txt",    [83,17,0,0]],
];

$tests = [
    'Intl characters' => ["intl", function(string $text) {
        $i = (function($text) {
            $i = \IntlBreakIterator::createCodePointInstance();
            $i->setText($text);
            foreach ($i as $b) {
                yield \IntlChar::chr($i->getLastCodePoint());
            }
        })($text);
        foreach ($i as $c) {
            $b = $c;
        }
    }],
    'Native characters' => ["", function(string $text) {
        $c = null;
        $i = new UTF8($text);
        while ($c !== "") {
            $c = $i->nextChar();
        }
    }],
    'Native iterator' => ["", function(string $text) {
        $c = null;
        $i = new UTF8($text);
        while ($c !== "") {
            $c = $i->nextChar();
        }
    }],
    'Intl code points' => ["intl", function(string $text) {
        $i = (function($text) {
            $i = \IntlBreakIterator::createCodePointInstance();
            $i->setText($text);
            foreach ($i as $b) {
                yield $i->getLastCodePoint();
            }
        })($text);
        foreach ($i as $c) {
            $b = $c;
        }
    }],
    'Native code points' => ["", function(string $text) {
        $p = null;
        $i = new UTF8($text);
        while ($p !== false) {
            $p = $i->nextCode();
        }
    }],
];

if (!file_exists(__DIR__."/docs/")) {
    mkdir(__DIR__."/docs/");
}

foreach($files as $fName => $file) {
    list($file, $make) = $file;
    $file = __DIR__."/docs/$file";
    if (!file_exists($file)) {
        if (is_string($make)) {
            $text = file_get_contents($make);
        } else {
            $text = make_file(...$make);
        }
        file_put_contents($file, $text);
    } else {
        $text = file_get_contents($file);
    }
    echo str_pad("$fName:", 30, " ").compile_statistics($text)."\n";
    foreach($tests as $tName => $test) {
        list($req, $test) = $test;
        if ($req && !extension_loaded($req)) {
            continue;
        } else {
            echo "    $tName: ";
            $t = [];
            for ($a = 0; $a < 5; $a++) {
                $s = microtime(true);
                $test($text);
                $t[$a] = microtime(true) - $s;
            }
            sort($t);
            array_pop($t);
            array_pop($t);
            $t = array_sum($t) / sizeof($t);
            echo number_format($t, 3)."\n";
        }
    }
}

function compile_statistics(string $text): string {
    $s = get_statistics($text);
    for ($a = 1; $a < 5; $a++) {
        $s[$a] = (int) ($s[$a] / $s[0] * 100);
        $s[$a] = str_pad((string) $s[$a], 3, " ", \STR_PAD_LEFT)."%";
    }
    array_shift($s);
    return "( ".implode(" ", $s)." )";
}

function get_statistics(string $text): array {
    $i = \IntlBreakIterator::createCodePointInstance();
    $i->setText($text);
    $s = [0,0,0,0,0];
    foreach ($i as $b) {
        $p = $i->getLastCodePoint();
        $s[0]++;
        if ($p < 0x80) {
            $s[1]++;
        } elseif ($p < 0x800) {
            $s[2]++;
        } elseif ($p < 0x10000) {
            $s[3]++;
        } else {
            $s[4]++;
        }
    }
    return $s;
}

function make_file(int $single, int $double, int $triple, int $quadruple): string {
    $a = 0;
    $s = $d = $t = $q = 0;
    $out = "";
    while ($a < 1000000) {
        if ($s < $single) {
            $min = 0;
            $max = 127;
            $s++;
        } elseif ($d < $double) {
            $min = 0x80;
            $max = 0x7FF;
            $d++;
        } elseif ($t < $triple) {
            $min = 0x800;
            $max = 0xFFFF;
            $t++;
        } elseif ($q < $quadruple) {
            $min = 0x10000;
            $max = 0x10FFFF;
            $q++;
        } else {
            $s = $d = $t = $q = 0;
            continue;
        }
        $p = random_int($min, $max);
        if ($p >= 55296 && $p <= 57343) {
            $p = 0xFFFD;
        }
        $c = \IntlChar::chr($p);
        $out .= $c;
        $a++;
    }
    return $out;
}
