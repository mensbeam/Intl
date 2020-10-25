<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class ISO88594 extends SingleByteEncoding {
    public const NAME = "ISO-8859-4";
    public const LABELS = [
        "csisolatin4",
        "iso-8859-4",
        "iso-ir-110",
        "iso8859-4",
        "iso88594",
        "iso_8859-4",
        "iso_8859-4:1988",
        "l4",
        "latin4",
    ];

    protected const TABLE_DEC_CHAR = ["\u{80}","\u{81}","\u{82}","\u{83}","\u{84}","\u{85}","\u{86}","\u{87}","\u{88}","\u{89}","\u{8a}","\u{8b}","\u{8c}","\u{8d}","\u{8e}","\u{8f}","\u{90}","\u{91}","\u{92}","\u{93}","\u{94}","\u{95}","\u{96}","\u{97}","\u{98}","\u{99}","\u{9a}","\u{9b}","\u{9c}","\u{9d}","\u{9e}","\u{9f}","\u{a0}","\u{104}","\u{138}","\u{156}","\u{a4}","\u{128}","\u{13b}","\u{a7}","\u{a8}","\u{160}","\u{112}","\u{122}","\u{166}","\u{ad}","\u{17d}","\u{af}","\u{b0}","\u{105}","\u{2db}","\u{157}","\u{b4}","\u{129}","\u{13c}","\u{2c7}","\u{b8}","\u{161}","\u{113}","\u{123}","\u{167}","\u{14a}","\u{17e}","\u{14b}","\u{100}","\u{c1}","\u{c2}","\u{c3}","\u{c4}","\u{c5}","\u{c6}","\u{12e}","\u{10c}","\u{c9}","\u{118}","\u{cb}","\u{116}","\u{cd}","\u{ce}","\u{12a}","\u{110}","\u{145}","\u{14c}","\u{136}","\u{d4}","\u{d5}","\u{d6}","\u{d7}","\u{d8}","\u{172}","\u{da}","\u{db}","\u{dc}","\u{168}","\u{16a}","\u{df}","\u{101}","\u{e1}","\u{e2}","\u{e3}","\u{e4}","\u{e5}","\u{e6}","\u{12f}","\u{10d}","\u{e9}","\u{119}","\u{eb}","\u{117}","\u{ed}","\u{ee}","\u{12b}","\u{111}","\u{146}","\u{14d}","\u{137}","\u{f4}","\u{f5}","\u{f6}","\u{f7}","\u{f8}","\u{173}","\u{fa}","\u{fb}","\u{fc}","\u{169}","\u{16b}","\u{2d9}"];
    protected const TABLE_DEC_CODE = [128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,260,312,342,164,296,315,167,168,352,274,290,358,173,381,175,176,261,731,343,180,297,316,711,184,353,275,291,359,330,382,331,256,193,194,195,196,197,198,302,268,201,280,203,278,205,206,298,272,325,332,310,212,213,214,215,216,370,218,219,220,360,362,223,257,225,226,227,228,229,230,303,269,233,281,235,279,237,238,299,273,326,333,311,244,245,246,247,248,371,250,251,252,361,363,729];
    protected const TABLE_ENC      = [128=>"\x80","\x81","\x82","\x83","\x84","\x85","\x86","\x87","\x88","\x89","\x8A","\x8B","\x8C","\x8D","\x8E","\x8F","\x90","\x91","\x92","\x93","\x94","\x95","\x96","\x97","\x98","\x99","\x9A","\x9B","\x9C","\x9D","\x9E","\x9F","\xA0",164=>"\xA4",167=>"\xA7","\xA8",173=>"\xAD",175=>"\xAF","\xB0",180=>"\xB4",184=>"\xB8",193=>"\xC1","\xC2","\xC3","\xC4","\xC5","\xC6",201=>"\xC9",203=>"\xCB",205=>"\xCD","\xCE",212=>"\xD4","\xD5","\xD6","\xD7","\xD8",218=>"\xDA","\xDB","\xDC",223=>"\xDF",225=>"\xE1","\xE2","\xE3","\xE4","\xE5","\xE6",233=>"\xE9",235=>"\xEB",237=>"\xED","\xEE",244=>"\xF4","\xF5","\xF6","\xF7","\xF8",250=>"\xFA","\xFB","\xFC",256=>"\xC0","\xE0",260=>"\xA1","\xB1",268=>"\xC8","\xE8",272=>"\xD0","\xF0","\xAA","\xBA",278=>"\xCC","\xEC","\xCA","\xEA",290=>"\xAB","\xBB",296=>"\xA5","\xB5","\xCF","\xEF",302=>"\xC7","\xE7",310=>"\xD3","\xF3","\xA2",315=>"\xA6","\xB6",325=>"\xD1","\xF1",330=>"\xBD","\xBF","\xD2","\xF2",342=>"\xA3","\xB3",352=>"\xA9","\xB9",358=>"\xAC","\xBC","\xDD","\xFD","\xDE","\xFE",370=>"\xD9","\xF9",381=>"\xAE","\xBE",711=>"\xB7",729=>"\xFF",731=>"\xB2"];
}
