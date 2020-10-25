<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class Windows1257 extends SingleByteEncoding {
    public const NAME = "windows-1257";
    public const LABELS = [
        "cp1257",
        "windows-1257",
        "x-cp1257",
    ];

    protected const TABLE_DEC_CHAR = ["\u{20ac}","\u{81}","\u{201a}","\u{83}","\u{201e}","\u{2026}","\u{2020}","\u{2021}","\u{88}","\u{2030}","\u{8a}","\u{2039}","\u{8c}","\u{a8}","\u{2c7}","\u{b8}","\u{90}","\u{2018}","\u{2019}","\u{201c}","\u{201d}","\u{2022}","\u{2013}","\u{2014}","\u{98}","\u{2122}","\u{9a}","\u{203a}","\u{9c}","\u{af}","\u{2db}","\u{9f}","\u{a0}",34=>"\u{a2}","\u{a3}","\u{a4}",38=>"\u{a6}","\u{a7}","\u{d8}","\u{a9}","\u{156}","\u{ab}","\u{ac}","\u{ad}","\u{ae}","\u{c6}","\u{b0}","\u{b1}","\u{b2}","\u{b3}","\u{b4}","\u{b5}","\u{b6}","\u{b7}","\u{f8}","\u{b9}","\u{157}","\u{bb}","\u{bc}","\u{bd}","\u{be}","\u{e6}","\u{104}","\u{12e}","\u{100}","\u{106}","\u{c4}","\u{c5}","\u{118}","\u{112}","\u{10c}","\u{c9}","\u{179}","\u{116}","\u{122}","\u{136}","\u{12a}","\u{13b}","\u{160}","\u{143}","\u{145}","\u{d3}","\u{14c}","\u{d5}","\u{d6}","\u{d7}","\u{172}","\u{141}","\u{15a}","\u{16a}","\u{dc}","\u{17b}","\u{17d}","\u{df}","\u{105}","\u{12f}","\u{101}","\u{107}","\u{e4}","\u{e5}","\u{119}","\u{113}","\u{10d}","\u{e9}","\u{17a}","\u{117}","\u{123}","\u{137}","\u{12b}","\u{13c}","\u{161}","\u{144}","\u{146}","\u{f3}","\u{14d}","\u{f5}","\u{f6}","\u{f7}","\u{173}","\u{142}","\u{15b}","\u{16b}","\u{fc}","\u{17c}","\u{17e}","\u{2d9}"];
    protected const TABLE_DEC_CODE = [8364,129,8218,131,8222,8230,8224,8225,136,8240,138,8249,140,168,711,184,144,8216,8217,8220,8221,8226,8211,8212,152,8482,154,8250,156,175,731,159,160,34=>162,163,164,38=>166,167,216,169,342,171,172,173,174,198,176,177,178,179,180,181,182,183,248,185,343,187,188,189,190,230,260,302,256,262,196,197,280,274,268,201,377,278,290,310,298,315,352,323,325,211,332,213,214,215,370,321,346,362,220,379,381,223,261,303,257,263,228,229,281,275,269,233,378,279,291,311,299,316,353,324,326,243,333,245,246,247,371,322,347,363,252,380,382,729];
    protected const TABLE_ENC      = [129=>"\x81",131=>"\x83",136=>"\x88",138=>"\x8A",140=>"\x8C",144=>"\x90",152=>"\x98",154=>"\x9A",156=>"\x9C",159=>"\x9F","\xA0",162=>"\xA2","\xA3","\xA4",166=>"\xA6","\xA7","\x8D","\xA9",171=>"\xAB","\xAC","\xAD","\xAE","\x9D","\xB0","\xB1","\xB2","\xB3","\xB4","\xB5","\xB6","\xB7","\x8F","\xB9",187=>"\xBB","\xBC","\xBD","\xBE",196=>"\xC4","\xC5","\xAF",201=>"\xC9",211=>"\xD3",213=>"\xD5","\xD6","\xD7","\xA8",220=>"\xDC",223=>"\xDF",228=>"\xE4","\xE5","\xBF",233=>"\xE9",243=>"\xF3",245=>"\xF5","\xF6","\xF7","\xB8",252=>"\xFC",256=>"\xC2","\xE2",260=>"\xC0","\xE0","\xC3","\xE3",268=>"\xC8","\xE8",274=>"\xC7","\xE7",278=>"\xCB","\xEB","\xC6","\xE6",290=>"\xCC","\xEC",298=>"\xCE","\xEE",302=>"\xC1","\xE1",310=>"\xCD","\xED",315=>"\xCF","\xEF",321=>"\xD9","\xF9","\xD1","\xF1","\xD2","\xF2",332=>"\xD4","\xF4",342=>"\xAA","\xBA",346=>"\xDA","\xFA",352=>"\xD0","\xF0",362=>"\xDB","\xFB",370=>"\xD8","\xF8",377=>"\xCA","\xEA","\xDD","\xFD","\xDE","\xFE",711=>"\x8E",729=>"\xFF",731=>"\x9E",8211=>"\x96","\x97",8216=>"\x91","\x92","\x82",8220=>"\x93","\x94","\x84",8224=>"\x86","\x87","\x95",8230=>"\x85",8240=>"\x89",8249=>"\x8B","\x9B",8364=>"\x80",8482=>"\x99"];
}
