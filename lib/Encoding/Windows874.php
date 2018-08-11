<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class Windows874 extends SingleByteEncoding {
    const NAME = "windows-874";
    const LABELS = [
        "dos-874",
        "iso-8859-11",
        "iso8859-11",
        "iso885911",
        "tis-620",
        "windows-874",
    ];

    const TABLE_DEC_CHAR = ["\u{20ac}","\u{81}","\u{82}","\u{83}","\u{84}","\u{2026}","\u{86}","\u{87}","\u{88}","\u{89}","\u{8a}","\u{8b}","\u{8c}","\u{8d}","\u{8e}","\u{8f}","\u{90}","\u{2018}","\u{2019}","\u{201c}","\u{201d}","\u{2022}","\u{2013}","\u{2014}","\u{98}","\u{99}","\u{9a}","\u{9b}","\u{9c}","\u{9d}","\u{9e}","\u{9f}","\u{a0}","\u{e01}","\u{e02}","\u{e03}","\u{e04}","\u{e05}","\u{e06}","\u{e07}","\u{e08}","\u{e09}","\u{e0a}","\u{e0b}","\u{e0c}","\u{e0d}","\u{e0e}","\u{e0f}","\u{e10}","\u{e11}","\u{e12}","\u{e13}","\u{e14}","\u{e15}","\u{e16}","\u{e17}","\u{e18}","\u{e19}","\u{e1a}","\u{e1b}","\u{e1c}","\u{e1d}","\u{e1e}","\u{e1f}","\u{e20}","\u{e21}","\u{e22}","\u{e23}","\u{e24}","\u{e25}","\u{e26}","\u{e27}","\u{e28}","\u{e29}","\u{e2a}","\u{e2b}","\u{e2c}","\u{e2d}","\u{e2e}","\u{e2f}","\u{e30}","\u{e31}","\u{e32}","\u{e33}","\u{e34}","\u{e35}","\u{e36}","\u{e37}","\u{e38}","\u{e39}","\u{e3a}",95=>"\u{e3f}","\u{e40}","\u{e41}","\u{e42}","\u{e43}","\u{e44}","\u{e45}","\u{e46}","\u{e47}","\u{e48}","\u{e49}","\u{e4a}","\u{e4b}","\u{e4c}","\u{e4d}","\u{e4e}","\u{e4f}","\u{e50}","\u{e51}","\u{e52}","\u{e53}","\u{e54}","\u{e55}","\u{e56}","\u{e57}","\u{e58}","\u{e59}","\u{e5a}","\u{e5b}"];
    const TABLE_DEC_CODE = [8364,129,130,131,132,8230,134,135,136,137,138,139,140,141,142,143,144,8216,8217,8220,8221,8226,8211,8212,152,153,154,155,156,157,158,159,160,3585,3586,3587,3588,3589,3590,3591,3592,3593,3594,3595,3596,3597,3598,3599,3600,3601,3602,3603,3604,3605,3606,3607,3608,3609,3610,3611,3612,3613,3614,3615,3616,3617,3618,3619,3620,3621,3622,3623,3624,3625,3626,3627,3628,3629,3630,3631,3632,3633,3634,3635,3636,3637,3638,3639,3640,3641,3642,95=>3647,3648,3649,3650,3651,3652,3653,3654,3655,3656,3657,3658,3659,3660,3661,3662,3663,3664,3665,3666,3667,3668,3669,3670,3671,3672,3673,3674,3675];
    const TABLE_ENC      = [129=>"\x81","\x82","\x83","\x84",134=>"\x86","\x87","\x88","\x89","\x8A","\x8B","\x8C","\x8D","\x8E","\x8F","\x90",152=>"\x98","\x99","\x9A","\x9B","\x9C","\x9D","\x9E","\x9F","\xA0",3585=>"\xA1","\xA2","\xA3","\xA4","\xA5","\xA6","\xA7","\xA8","\xA9","\xAA","\xAB","\xAC","\xAD","\xAE","\xAF","\xB0","\xB1","\xB2","\xB3","\xB4","\xB5","\xB6","\xB7","\xB8","\xB9","\xBA","\xBB","\xBC","\xBD","\xBE","\xBF","\xC0","\xC1","\xC2","\xC3","\xC4","\xC5","\xC6","\xC7","\xC8","\xC9","\xCA","\xCB","\xCC","\xCD","\xCE","\xCF","\xD0","\xD1","\xD2","\xD3","\xD4","\xD5","\xD6","\xD7","\xD8","\xD9","\xDA",3647=>"\xDF","\xE0","\xE1","\xE2","\xE3","\xE4","\xE5","\xE6","\xE7","\xE8","\xE9","\xEA","\xEB","\xEC","\xED","\xEE","\xEF","\xF0","\xF1","\xF2","\xF3","\xF4","\xF5","\xF6","\xF7","\xF8","\xF9","\xFA","\xFB",8211=>"\x96","\x97",8216=>"\x91","\x92",8220=>"\x93","\x94",8226=>"\x95",8230=>"\x85",8364=>"\x80"];
}
