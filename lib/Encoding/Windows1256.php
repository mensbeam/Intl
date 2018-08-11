<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class Windows1256 extends SingleByteEncoding {
    const NAME = "windows-1256";
    const LABELS = [
        "cp1256",
        "windows-1256",
        "x-cp1256",
    ];

    const TABLE_DEC_CHAR = ["\u{20ac}","\u{67e}","\u{201a}","\u{192}","\u{201e}","\u{2026}","\u{2020}","\u{2021}","\u{2c6}","\u{2030}","\u{679}","\u{2039}","\u{152}","\u{686}","\u{698}","\u{688}","\u{6af}","\u{2018}","\u{2019}","\u{201c}","\u{201d}","\u{2022}","\u{2013}","\u{2014}","\u{6a9}","\u{2122}","\u{691}","\u{203a}","\u{153}","\u{200c}","\u{200d}","\u{6ba}","\u{a0}","\u{60c}","\u{a2}","\u{a3}","\u{a4}","\u{a5}","\u{a6}","\u{a7}","\u{a8}","\u{a9}","\u{6be}","\u{ab}","\u{ac}","\u{ad}","\u{ae}","\u{af}","\u{b0}","\u{b1}","\u{b2}","\u{b3}","\u{b4}","\u{b5}","\u{b6}","\u{b7}","\u{b8}","\u{b9}","\u{61b}","\u{bb}","\u{bc}","\u{bd}","\u{be}","\u{61f}","\u{6c1}","\u{621}","\u{622}","\u{623}","\u{624}","\u{625}","\u{626}","\u{627}","\u{628}","\u{629}","\u{62a}","\u{62b}","\u{62c}","\u{62d}","\u{62e}","\u{62f}","\u{630}","\u{631}","\u{632}","\u{633}","\u{634}","\u{635}","\u{636}","\u{d7}","\u{637}","\u{638}","\u{639}","\u{63a}","\u{640}","\u{641}","\u{642}","\u{643}","\u{e0}","\u{644}","\u{e2}","\u{645}","\u{646}","\u{647}","\u{648}","\u{e7}","\u{e8}","\u{e9}","\u{ea}","\u{eb}","\u{649}","\u{64a}","\u{ee}","\u{ef}","\u{64b}","\u{64c}","\u{64d}","\u{64e}","\u{f4}","\u{64f}","\u{650}","\u{f7}","\u{651}","\u{f9}","\u{652}","\u{fb}","\u{fc}","\u{200e}","\u{200f}","\u{6d2}"];
    const TABLE_DEC_CODE = [8364,1662,8218,402,8222,8230,8224,8225,710,8240,1657,8249,338,1670,1688,1672,1711,8216,8217,8220,8221,8226,8211,8212,1705,8482,1681,8250,339,8204,8205,1722,160,1548,162,163,164,165,166,167,168,169,1726,171,172,173,174,175,176,177,178,179,180,181,182,183,184,185,1563,187,188,189,190,1567,1729,1569,1570,1571,1572,1573,1574,1575,1576,1577,1578,1579,1580,1581,1582,1583,1584,1585,1586,1587,1588,1589,1590,215,1591,1592,1593,1594,1600,1601,1602,1603,224,1604,226,1605,1606,1607,1608,231,232,233,234,235,1609,1610,238,239,1611,1612,1613,1614,244,1615,1616,247,1617,249,1618,251,252,8206,8207,1746];
    const TABLE_ENC      = [160=>"\xA0",162=>"\xA2","\xA3","\xA4","\xA5","\xA6","\xA7","\xA8","\xA9",171=>"\xAB","\xAC","\xAD","\xAE","\xAF","\xB0","\xB1","\xB2","\xB3","\xB4","\xB5","\xB6","\xB7","\xB8","\xB9",187=>"\xBB","\xBC","\xBD","\xBE",215=>"\xD7",224=>"\xE0",226=>"\xE2",231=>"\xE7","\xE8","\xE9","\xEA","\xEB",238=>"\xEE","\xEF",244=>"\xF4",247=>"\xF7",249=>"\xF9",251=>"\xFB","\xFC",338=>"\x8C","\x9C",402=>"\x83",710=>"\x88",1548=>"\xA1",1563=>"\xBA",1567=>"\xBF",1569=>"\xC1","\xC2","\xC3","\xC4","\xC5","\xC6","\xC7","\xC8","\xC9","\xCA","\xCB","\xCC","\xCD","\xCE","\xCF","\xD0","\xD1","\xD2","\xD3","\xD4","\xD5","\xD6","\xD8","\xD9","\xDA","\xDB",1600=>"\xDC","\xDD","\xDE","\xDF","\xE1","\xE3","\xE4","\xE5","\xE6","\xEC","\xED","\xF0","\xF1","\xF2","\xF3","\xF5","\xF6","\xF8","\xFA",1657=>"\x8A",1662=>"\x81",1670=>"\x8D",1672=>"\x8F",1681=>"\x9A",1688=>"\x8E",1705=>"\x98",1711=>"\x90",1722=>"\x9F",1726=>"\xAA",1729=>"\xC0",1746=>"\xFF",8204=>"\x9D","\x9E","\xFD","\xFE",8211=>"\x96","\x97",8216=>"\x91","\x92","\x82",8220=>"\x93","\x94","\x84",8224=>"\x86","\x87","\x95",8230=>"\x85",8240=>"\x89",8249=>"\x8B","\x9B",8364=>"\x80",8482=>"\x99"];
}
