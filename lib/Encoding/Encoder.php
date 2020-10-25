<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

use MensBeam\Intl\Encoding as Matcher;

class Encoder {
    protected $name;
    protected $fatal = true;
    
    /** Constructs a new encoder for the specified $label
     * 
     * @param string $label One of the encoding labels listed in the specification e.g. "utf-8", "Latin1", "shift_JIS"
     * @param bool $fatal If true (the default) exceptions will be thrown when a character cannot be represented in the target encoding; if false HTML character references will be substituted instead
     * 
     * @see https://encoding.spec.whatwg.org#names-and-labels
     */
    public function __construct(string $label, bool $fatal = true) {
        $l = Matcher::matchLabel($label);
        if (!$l || !$l['encoder']) {
            throw new EncoderException("Label '$label' does not have an encoder", Coder::E_UNAVAILABLE_ENCODER);
        } else {
            $this->name = $l['name'];
            $this->fatal = $fatal;
        }
    }
    
    /** Encodes a series of code point numbers into a string
     * 
     * @param iterable $codePoints An iterable set of integers representing code points in the Unicode range
     */
    public function encode(iterable $codePoints): string {
        $out = "";
        switch ($this->name) {
            case "UTF-8":
                foreach ($codePoints as $codePoint) {
                    $out .= UTF8::encode($codePoint, $this->fatal);
                }
                break;
            case "Big5":
                foreach ($codePoints as $codePoint) {
                    $out .= Big5::encode($codePoint, $this->fatal);
                }
                break;
            case "EUC-JP":
                foreach ($codePoints as $codePoint) {
                    $out .= EUCJP::encode($codePoint, $this->fatal);
                }
                break;
            case "EUC-KR":
                foreach ($codePoints as $codePoint) {
                    $out .= EUCKR::encode($codePoint, $this->fatal);
                }
                break;
            case "gb18030":
                foreach ($codePoints as $codePoint) {
                    $out .= GB18030::encode($codePoint, $this->fatal);
                }
                break;
            case "GBK":
                foreach ($codePoints as $codePoint) {
                    $out .= GBK::encode($codePoint, $this->fatal);
                }
                break;
            case "IBM866":
                foreach ($codePoints as $codePoint) {
                    $out .= IBM866::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-2022-JP":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO2022JP::encode($codePoint, $this->fatal, $mode);
                }
                $out .= ISO2022JP::encode(null, $this->fatal, $mode);
                break;
            case "ISO-8859-2":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO88592::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-3":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO88593::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-4":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO88594::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-5":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO88595::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-6":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO88596::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-7":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO88597::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-8":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO88598::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-8-I":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO88598I::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-10":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO885910::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-13":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO885913::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-14":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO885914::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-15":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO885915::encode($codePoint, $this->fatal);
                }
                break;
            case "ISO-8859-16":
                foreach ($codePoints as $codePoint) {
                    $out .= ISO885916::encode($codePoint, $this->fatal);
                }
                break;
            case "KOI8-R":
                foreach ($codePoints as $codePoint) {
                    $out .= KOI8R::encode($codePoint, $this->fatal);
                }
                break;
            case "KOI8-U":
                foreach ($codePoints as $codePoint) {
                    $out .= KOI8U::encode($codePoint, $this->fatal);
                }
                break;
            case "macintosh":
                foreach ($codePoints as $codePoint) {
                    $out .= Macintosh::encode($codePoint, $this->fatal);
                }
                break;
            case "Shift_JIS":
                foreach ($codePoints as $codePoint) {
                    $out .= ShiftJIS::encode($codePoint, $this->fatal);
                }
                break;
            case "windows-1250":
                foreach ($codePoints as $codePoint) {
                    $out .= Windows1250::encode($codePoint, $this->fatal);
                }
                break;
            case "windows-1251":
                foreach ($codePoints as $codePoint) {
                    $out .= Windows1251::encode($codePoint, $this->fatal);
                }
                break;
            case "windows-1252":
                foreach ($codePoints as $codePoint) {
                    $out .= Windows1252::encode($codePoint, $this->fatal);
                }
                break;
            case "windows-1253":
                foreach ($codePoints as $codePoint) {
                    $out .= Windows1253::encode($codePoint, $this->fatal);
                }
                break;
            case "windows-1254":
                foreach ($codePoints as $codePoint) {
                    $out .= Windows1254::encode($codePoint, $this->fatal);
                }
                break;
            case "windows-1255":
                foreach ($codePoints as $codePoint) {
                    $out .= Windows1255::encode($codePoint, $this->fatal);
                }
                break;
            case "windows-1256":
                foreach ($codePoints as $codePoint) {
                    $out .= Windows1256::encode($codePoint, $this->fatal);
                }
                break;
            case "windows-1257":
                foreach ($codePoints as $codePoint) {
                    $out .= Windows1257::encode($codePoint, $this->fatal);
                }
                break;
            case "windows-1258":
                foreach ($codePoints as $codePoint) {
                    $out .= Windows1258::encode($codePoint, $this->fatal);
                }
                break;
            case "windows-874":
                foreach ($codePoints as $codePoint) {
                    $out .= Windows874::encode($codePoint, $this->fatal);
                }
                break;
            case "x-mac-cyrillic":
                foreach ($codePoints as $codePoint) {
                    $out .= XMacCyrillic::encode($codePoint, $this->fatal);
                }
                break;
            case "x-user-defined":
                foreach ($codePoints as $codePoint) {
                    $out .= XUserDefined::encode($codePoint, $this->fatal);
                }
                break;
        }
        return $out;
    }

    /** Encodes a single character into a string
     * 
     * When using this method to encode a string, the finalize() method should be called to terminate the string
     * 
     * @param int $codePoint An integer representing the Unicode code point number to encode
     */
    public function encodeChar(int $codePoint): string {
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
            case "windows-1250":
                return Windows1250::encode($codePoint, $this->fatal);
            case "windows-1251":
                return Windows1251::encode($codePoint, $this->fatal);
            case "windows-1252":
                return Windows1252::encode($codePoint, $this->fatal);
            case "windows-1253":
                return Windows1253::encode($codePoint, $this->fatal);
            case "windows-1254":
                return Windows1254::encode($codePoint, $this->fatal);
            case "windows-1255":
                return Windows1255::encode($codePoint, $this->fatal);
            case "windows-1256":
                return Windows1256::encode($codePoint, $this->fatal);
            case "windows-1257":
                return Windows1257::encode($codePoint, $this->fatal);
            case "windows-1258":
                return Windows1258::encode($codePoint, $this->fatal);
            case "windows-874":
                return Windows874::encode($codePoint, $this->fatal);
            case "x-mac-cyrillic":
                return XMacCyrillic::encode($codePoint, $this->fatal);
            case "x-user-defined":
                return XUserDefined::encode($codePoint, $this->fatal);
            case "ISO-2022-JP":
                return ISO2022JP::encode($codePoint, $this->fatal, $this->mode);
        }
    } // @codeCoverageIgnore
    
    /** Finalizes a string, returning any terminal bytes to append to the output
     * 
     * For the ISO-2022-JP encoding, this method must be called fater the last character is encoded to correctly encode a string; for other encodings this is a no-op
     */
    public function finalize(): string {
        return ISO2022JP::encode(null, $this->fatal, $this->mode);
    }
}
