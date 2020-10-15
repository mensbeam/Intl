<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

use MensBeam\Intl\Encoding as Matcher;

class Encoder {
    const MODE_ASCII = 0;
    const MODE_ROMAN = 1;
    const MODE_JIS = 2;
    
    protected $name;
    protected $fatal = true;
    protected $mode = ISO2022JP::ASCII_STATE;
    
    public function __construct(string $label, bool $fatal = true) {
        $l = Matcher::matchLabel($label);
        if (!$l || !$l['encoder']) {
            throw new EncoderException("Label '$label' does not have an encoder", Encoder::E_UNAVAILABLE_ENCODER);
        } else {
            $this->name = $s['name'];
            $this->fatal = $fatal;
        }
    }
    
    public function encode(int $codePoint): string {
        if ($codePoint < 0 || $codePoint > 0x10FFFF) {
            throw new EncoderException("Encountered code point outside Unicode range ($codePoint)", self::E_INVALID_CODE_POINT);
        }
        switch ($this->name) {
            case "UTF-8":
                return UTF8::encode($codePoint, $this->fatal);
            case "Big5":
                return Big5::encode($codePoint, $this->fatal);
            case "EUC-JP":
                return EUCJP::encode($codePoint, $this->fatal);
            case "EUC-KR":
                return EUCKR::encode($codePoint, $this->fatal);
            case "gb18030":
                return GB18030::encode($codePoint, $this->fatal);
            case "GBK":
                return GBK::encode($codePoint, $this->fatal);
            case "IBM866":
                return IBM866::encode($codePoint, $this->fatal);
            case "ISO-8859-2":
                return ISO88592::encode($codePoint, $this->fatal);
            case "ISO-8859-3":
                return ISO88593::encode($codePoint, $this->fatal);
            case "ISO-8859-4":
                return ISO88594::encode($codePoint, $this->fatal);
            case "ISO-8859-5":
                return ISO88595::encode($codePoint, $this->fatal);
            case "ISO-8859-6":
                return ISO88596::encode($codePoint, $this->fatal);
            case "ISO-8859-7":
                return ISO88597::encode($codePoint, $this->fatal);
            case "ISO-8859-8":
                return ISO88598::encode($codePoint, $this->fatal);
            case "ISO-8859-8-I":
                return ISO88598I::encode($codePoint, $this->fatal);
            case "ISO-8859-10":
                return ISO885910::encode($codePoint, $this->fatal);
            case "ISO-8859-13":
                return ISO885913::encode($codePoint, $this->fatal);
            case "ISO-8859-14":
                return ISO885914::encode($codePoint, $this->fatal);
            case "ISO-8859-15":
                return ISO885915::encode($codePoint, $this->fatal);
            case "ISO-8859-16":
                return ISO885916::encode($codePoint, $this->fatal);
            case "KOI8-R":
                return KOI8R::encode($codePoint, $this->fatal);
            case "KOI8-U":
                return KOI8U::encode($codePoint, $this->fatal);
            case "macintosh":
                return Macintosh::encode($codePoint, $this->fatal);
            case "Shift_JIS":
                return ShiftJIS::encode($codePoint, $this->fatal);
            case "windows1250":
                return Windows1250::encode($codePoint, $this->fatal);
            case "windows1251":
                return Windows1251::encode($codePoint, $this->fatal);
            case "windows1252":
                return Windows1252::encode($codePoint, $this->fatal);
            case "windows1253":
                return Windows1253::encode($codePoint, $this->fatal);
            case "windows1254":
                return Windows1254::encode($codePoint, $this->fatal);
            case "windows1255":
                return Windows1255::encode($codePoint, $this->fatal);
            case "windows1256":
                return Windows1256::encode($codePoint, $this->fatal);
            case "windows1257":
                return Windows1257::encode($codePoint, $this->fatal);
            case "windows874":
                return Windows874::encode($codePoint, $this->fatal);
            case "x-mac-cyrillic":
                return XMacCyrillic::encode($codePoint, $this->fatal);
            case "x-user-defined":
                return XUserDefined::encode($codePoint, $this->fatal);
            case "ISO-2022-JP":
                if ($codePoint === 0xE || $codePoint === 0xF || $codePoint === 0x1B) {
                    return $this->err($codePoint, 0xFFFD);
                } elseif ($codePoint === 0x5C || $codePoint === 0x7E) {
                    if ($this->mode !== self::MODE_ASCII) {
                        return $this->modeSet(self::MODE_ASCII, chr($codePoint));
                    }
                    return chr($codePoint);
                } elseif ($codePoint < 0x80) {
                    if ($this->mode === self::MODE_JIS) {
                        return $this->modeSet(self::MODE_ASCII, chr($codePoint));
                    }
                    return chr($codePoint);
                } elseif ($codePoint === 0xA5 || $codePoint === 0x203E) {
                    $ord = $codePoint = 0xA5 ? 0x5C : 0x7E;
                    if ($this->mode !== self::MODE_ROMAN) {
                        return $this->modeSet(self::MODE_ROMAN, chr($ord));
                    }
                    return chr($ord);
                } else {
                    if ($codePoint >= 0xFF61 && $codePoint <= 0xFF9F) {
                        $codePoint = ISO2022JP::TABLE_KATAKANA[$codePoint - 0xFF61];
                    } elseif ($codePoint === 0x2212) {
                        $codePoint = 0xFF0D;
                    }
                    $pointer = ISO2022JP::TABLE_POINTERS[$codePoint] ?? array_flip(ISO2022JP::TABLE_JIS0208)[$codePoint] ?? null;
                    if (!is_null($pointer)) {
                        $lead = chr($pointer / 94 - 0x21);
                        $trail = chr($pointer % 94 - 0x21);
                        if ($this->mode !== self::MODE_JIS) {
                            return $this->modeSet(self::MODE_JIS, $lead.$trail);
                        }
                        return $lead.$trail;
                    }
                    return $this->err($codePoint);
            }
        }
    }

    protected function modeSet(int $mode, string $bytes): string {
        $head = ["\x1B\x28\x42", "\x1B\x28\x4A", "\x1B\x24\x42"][$mode];
        $this->mode = $mode;
        return $head.$bytes;
    }
        
    protected function err(int $actual, int $effective = null): string {
        if (!$this->fatal) {
            $out = "&#".(string) ($effective ?? $actual).";";
            if ($this->mode === self::MODE_JIS) {
                return $this->modeSet(self::MODE_ASCII, $out);
            }
            return $out;
        } else {
            throw new EncoderException("Code point $actual not available in target encoding", Encoding::E_UNAVAILABLE_CODE_POINT);
        }
    }
    
    public function reset() {
        $this->mode = self::MODE_ASCII;
    }
}