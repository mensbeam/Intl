<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class Macintosh extends SingleByteEncoding {
    public const NAME = "macintosh";
    public const LABELS = [
        "csmacintosh",
        "mac",
        "macintosh",
        "x-mac-roman",
    ];

    protected const TABLE_DEC_CHAR = ["\u{c4}","\u{c5}","\u{c7}","\u{c9}","\u{d1}","\u{d6}","\u{dc}","\u{e1}","\u{e0}","\u{e2}","\u{e4}","\u{e3}","\u{e5}","\u{e7}","\u{e9}","\u{e8}","\u{ea}","\u{eb}","\u{ed}","\u{ec}","\u{ee}","\u{ef}","\u{f1}","\u{f3}","\u{f2}","\u{f4}","\u{f6}","\u{f5}","\u{fa}","\u{f9}","\u{fb}","\u{fc}","\u{2020}","\u{b0}","\u{a2}","\u{a3}","\u{a7}","\u{2022}","\u{b6}","\u{df}","\u{ae}","\u{a9}","\u{2122}","\u{b4}","\u{a8}","\u{2260}","\u{c6}","\u{d8}","\u{221e}","\u{b1}","\u{2264}","\u{2265}","\u{a5}","\u{b5}","\u{2202}","\u{2211}","\u{220f}","\u{3c0}","\u{222b}","\u{aa}","\u{ba}","\u{3a9}","\u{e6}","\u{f8}","\u{bf}","\u{a1}","\u{ac}","\u{221a}","\u{192}","\u{2248}","\u{2206}","\u{ab}","\u{bb}","\u{2026}","\u{a0}","\u{c0}","\u{c3}","\u{d5}","\u{152}","\u{153}","\u{2013}","\u{2014}","\u{201c}","\u{201d}","\u{2018}","\u{2019}","\u{f7}","\u{25ca}","\u{ff}","\u{178}","\u{2044}","\u{20ac}","\u{2039}","\u{203a}","\u{fb01}","\u{fb02}","\u{2021}","\u{b7}","\u{201a}","\u{201e}","\u{2030}","\u{c2}","\u{ca}","\u{c1}","\u{cb}","\u{c8}","\u{cd}","\u{ce}","\u{cf}","\u{cc}","\u{d3}","\u{d4}","\u{f8ff}","\u{d2}","\u{da}","\u{db}","\u{d9}","\u{131}","\u{2c6}","\u{2dc}","\u{af}","\u{2d8}","\u{2d9}","\u{2da}","\u{b8}","\u{2dd}","\u{2db}","\u{2c7}"];
    protected const TABLE_DEC_CODE = [196,197,199,201,209,214,220,225,224,226,228,227,229,231,233,232,234,235,237,236,238,239,241,243,242,244,246,245,250,249,251,252,8224,176,162,163,167,8226,182,223,174,169,8482,180,168,8800,198,216,8734,177,8804,8805,165,181,8706,8721,8719,960,8747,170,186,937,230,248,191,161,172,8730,402,8776,8710,171,187,8230,160,192,195,213,338,339,8211,8212,8220,8221,8216,8217,247,9674,255,376,8260,8364,8249,8250,64257,64258,8225,183,8218,8222,8240,194,202,193,203,200,205,206,207,204,211,212,63743,210,218,219,217,305,710,732,175,728,729,730,184,733,731,711];
    protected const TABLE_ENC      = [160=>"\xCA","\xC1","\xA2","\xA3",165=>"\xB4",167=>"\xA4","\xAC","\xA9","\xBB","\xC7","\xC2",174=>"\xA8","\xF8","\xA1","\xB1",180=>"\xAB","\xB5","\xA6","\xE1","\xFC",186=>"\xBC","\xC8",191=>"\xC0","\xCB","\xE7","\xE5","\xCC","\x80","\x81","\xAE","\x82","\xE9","\x83","\xE6","\xE8","\xED","\xEA","\xEB","\xEC",209=>"\x84","\xF1","\xEE","\xEF","\xCD","\x85",216=>"\xAF","\xF4","\xF2","\xF3","\x86",223=>"\xA7","\x88","\x87","\x89","\x8B","\x8A","\x8C","\xBE","\x8D","\x8F","\x8E","\x90","\x91","\x93","\x92","\x94","\x95",241=>"\x96","\x98","\x97","\x99","\x9B","\x9A","\xD6","\xBF","\x9D","\x9C","\x9E","\x9F",255=>"\xD8",305=>"\xF5",338=>"\xCE","\xCF",376=>"\xD9",402=>"\xC4",710=>"\xF6","\xFF",728=>"\xF9","\xFA","\xFB","\xFE","\xF7","\xFD",937=>"\xBD",960=>"\xB9",8211=>"\xD0","\xD1",8216=>"\xD4","\xD5","\xE2",8220=>"\xD2","\xD3","\xE3",8224=>"\xA0","\xE0","\xA5",8230=>"\xC9",8240=>"\xE4",8249=>"\xDC","\xDD",8260=>"\xDA",8364=>"\xDB",8482=>"\xAA",8706=>"\xB6",8710=>"\xC6",8719=>"\xB8",8721=>"\xB7",8730=>"\xC3",8734=>"\xB0",8747=>"\xBA",8776=>"\xC5",8800=>"\xAD",8804=>"\xB2","\xB3",9674=>"\xD7",63743=>"\xF0",64257=>"\xDE","\xDF"];
}
