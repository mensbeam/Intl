<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class Windows1252 extends SingleByteEncoding {
    const NAME = "windows-1252";
    const LABELS = [
        "ansi_x3.4-1968",
        "ascii",
        "cp1252",
        "cp819",
        "csisolatin1",
        "ibm819",
        "iso-8859-1",
        "iso-ir-100",
        "iso8859-1",
        "iso88591",
        "iso_8859-1",
        "iso_8859-1:1987",
        "l1",
        "latin1",
        "us-ascii",
        "windows-1252",
        "x-cp1252",
    ];

    const TABLE_DEC_CHAR = ["\u{20ac}","\u{81}","\u{201a}","\u{192}","\u{201e}","\u{2026}","\u{2020}","\u{2021}","\u{2c6}","\u{2030}","\u{160}","\u{2039}","\u{152}","\u{8d}","\u{17d}","\u{8f}","\u{90}","\u{2018}","\u{2019}","\u{201c}","\u{201d}","\u{2022}","\u{2013}","\u{2014}","\u{2dc}","\u{2122}","\u{161}","\u{203a}","\u{153}","\u{9d}","\u{17e}","\u{178}","\u{a0}","\u{a1}","\u{a2}","\u{a3}","\u{a4}","\u{a5}","\u{a6}","\u{a7}","\u{a8}","\u{a9}","\u{aa}","\u{ab}","\u{ac}","\u{ad}","\u{ae}","\u{af}","\u{b0}","\u{b1}","\u{b2}","\u{b3}","\u{b4}","\u{b5}","\u{b6}","\u{b7}","\u{b8}","\u{b9}","\u{ba}","\u{bb}","\u{bc}","\u{bd}","\u{be}","\u{bf}","\u{c0}","\u{c1}","\u{c2}","\u{c3}","\u{c4}","\u{c5}","\u{c6}","\u{c7}","\u{c8}","\u{c9}","\u{ca}","\u{cb}","\u{cc}","\u{cd}","\u{ce}","\u{cf}","\u{d0}","\u{d1}","\u{d2}","\u{d3}","\u{d4}","\u{d5}","\u{d6}","\u{d7}","\u{d8}","\u{d9}","\u{da}","\u{db}","\u{dc}","\u{dd}","\u{de}","\u{df}","\u{e0}","\u{e1}","\u{e2}","\u{e3}","\u{e4}","\u{e5}","\u{e6}","\u{e7}","\u{e8}","\u{e9}","\u{ea}","\u{eb}","\u{ec}","\u{ed}","\u{ee}","\u{ef}","\u{f0}","\u{f1}","\u{f2}","\u{f3}","\u{f4}","\u{f5}","\u{f6}","\u{f7}","\u{f8}","\u{f9}","\u{fa}","\u{fb}","\u{fc}","\u{fd}","\u{fe}","\u{ff}"];
    const TABLE_DEC_CODE = [8364,129,8218,402,8222,8230,8224,8225,710,8240,352,8249,338,141,381,143,144,8216,8217,8220,8221,8226,8211,8212,732,8482,353,8250,339,157,382,376,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230,231,232,233,234,235,236,237,238,239,240,241,242,243,244,245,246,247,248,249,250,251,252,253,254,255];
    const TABLE_ENC      = [129=>"\x81",141=>"\x8D",143=>"\x8F","\x90",157=>"\x9D",160=>"\xA0","\xA1","\xA2","\xA3","\xA4","\xA5","\xA6","\xA7","\xA8","\xA9","\xAA","\xAB","\xAC","\xAD","\xAE","\xAF","\xB0","\xB1","\xB2","\xB3","\xB4","\xB5","\xB6","\xB7","\xB8","\xB9","\xBA","\xBB","\xBC","\xBD","\xBE","\xBF","\xC0","\xC1","\xC2","\xC3","\xC4","\xC5","\xC6","\xC7","\xC8","\xC9","\xCA","\xCB","\xCC","\xCD","\xCE","\xCF","\xD0","\xD1","\xD2","\xD3","\xD4","\xD5","\xD6","\xD7","\xD8","\xD9","\xDA","\xDB","\xDC","\xDD","\xDE","\xDF","\xE0","\xE1","\xE2","\xE3","\xE4","\xE5","\xE6","\xE7","\xE8","\xE9","\xEA","\xEB","\xEC","\xED","\xEE","\xEF","\xF0","\xF1","\xF2","\xF3","\xF4","\xF5","\xF6","\xF7","\xF8","\xF9","\xFA","\xFB","\xFC","\xFD","\xFE","\xFF",338=>"\x8C","\x9C",352=>"\x8A","\x9A",376=>"\x9F",381=>"\x8E","\x9E",402=>"\x83",710=>"\x88",732=>"\x98",8211=>"\x96","\x97",8216=>"\x91","\x92","\x82",8220=>"\x93","\x94","\x84",8224=>"\x86","\x87","\x95",8230=>"\x85",8240=>"\x89",8249=>"\x8B","\x9B",8364=>"\x80",8482=>"\x99"];
}
