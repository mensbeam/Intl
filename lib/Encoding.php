<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl;

use MensBeam\Intl\Encoding\Decoder;
use MensBeam\Intl\Encoding\Coder;
use MensBeam\Intl\Encoding\Encoder;
use MensBeam\Intl\Encoding\EncoderException;

abstract class Encoding {
    protected const LABEL_MAP = ['big5'=>"Big5",'big5-hkscs'=>"Big5",'cn-big5'=>"Big5",'csbig5'=>"Big5",'x-x-big5'=>"Big5",'cseucpkdfmtjapanese'=>"EUC-JP",'euc-jp'=>"EUC-JP",'x-euc-jp'=>"EUC-JP",'cseuckr'=>"EUC-KR",'csksc56011987'=>"EUC-KR",'euc-kr'=>"EUC-KR",'iso-ir-149'=>"EUC-KR",'korean'=>"EUC-KR",'ks_c_5601-1987'=>"EUC-KR",'ks_c_5601-1989'=>"EUC-KR",'ksc5601'=>"EUC-KR",'ksc_5601'=>"EUC-KR",'windows-949'=>"EUC-KR",'gb18030'=>"gb18030",'chinese'=>"GBK",'csgb2312'=>"GBK",'csiso58gb231280'=>"GBK",'gb2312'=>"GBK",'gb_2312'=>"GBK",'gb_2312-80'=>"GBK",'gbk'=>"GBK",'iso-ir-58'=>"GBK",'x-gbk'=>"GBK",'866'=>"IBM866",'cp866'=>"IBM866",'csibm866'=>"IBM866",'ibm866'=>"IBM866",'csiso2022jp'=>"ISO-2022-JP",'iso-2022-jp'=>"ISO-2022-JP",'csisolatin6'=>"ISO-8859-10",'iso-8859-10'=>"ISO-8859-10",'iso-ir-157'=>"ISO-8859-10",'iso8859-10'=>"ISO-8859-10",'iso885910'=>"ISO-8859-10",'l6'=>"ISO-8859-10",'latin6'=>"ISO-8859-10",'iso-8859-13'=>"ISO-8859-13",'iso8859-13'=>"ISO-8859-13",'iso885913'=>"ISO-8859-13",'iso-8859-14'=>"ISO-8859-14",'iso8859-14'=>"ISO-8859-14",'iso885914'=>"ISO-8859-14",'csisolatin9'=>"ISO-8859-15",'iso-8859-15'=>"ISO-8859-15",'iso8859-15'=>"ISO-8859-15",'iso885915'=>"ISO-8859-15",'iso_8859-15'=>"ISO-8859-15",'l9'=>"ISO-8859-15",'iso-8859-16'=>"ISO-8859-16",'csisolatin2'=>"ISO-8859-2",'iso-8859-2'=>"ISO-8859-2",'iso-ir-101'=>"ISO-8859-2",'iso8859-2'=>"ISO-8859-2",'iso88592'=>"ISO-8859-2",'iso_8859-2'=>"ISO-8859-2",'iso_8859-2:1987'=>"ISO-8859-2",'l2'=>"ISO-8859-2",'latin2'=>"ISO-8859-2",'csisolatin3'=>"ISO-8859-3",'iso-8859-3'=>"ISO-8859-3",'iso-ir-109'=>"ISO-8859-3",'iso8859-3'=>"ISO-8859-3",'iso88593'=>"ISO-8859-3",'iso_8859-3'=>"ISO-8859-3",'iso_8859-3:1988'=>"ISO-8859-3",'l3'=>"ISO-8859-3",'latin3'=>"ISO-8859-3",'csisolatin4'=>"ISO-8859-4",'iso-8859-4'=>"ISO-8859-4",'iso-ir-110'=>"ISO-8859-4",'iso8859-4'=>"ISO-8859-4",'iso88594'=>"ISO-8859-4",'iso_8859-4'=>"ISO-8859-4",'iso_8859-4:1988'=>"ISO-8859-4",'l4'=>"ISO-8859-4",'latin4'=>"ISO-8859-4",'csisolatincyrillic'=>"ISO-8859-5",'cyrillic'=>"ISO-8859-5",'iso-8859-5'=>"ISO-8859-5",'iso-ir-144'=>"ISO-8859-5",'iso8859-5'=>"ISO-8859-5",'iso88595'=>"ISO-8859-5",'iso_8859-5'=>"ISO-8859-5",'iso_8859-5:1988'=>"ISO-8859-5",'arabic'=>"ISO-8859-6",'asmo-708'=>"ISO-8859-6",'csiso88596e'=>"ISO-8859-6",'csiso88596i'=>"ISO-8859-6",'csisolatinarabic'=>"ISO-8859-6",'ecma-114'=>"ISO-8859-6",'iso-8859-6'=>"ISO-8859-6",'iso-8859-6-e'=>"ISO-8859-6",'iso-8859-6-i'=>"ISO-8859-6",'iso-ir-127'=>"ISO-8859-6",'iso8859-6'=>"ISO-8859-6",'iso88596'=>"ISO-8859-6",'iso_8859-6'=>"ISO-8859-6",'iso_8859-6:1987'=>"ISO-8859-6",'csisolatingreek'=>"ISO-8859-7",'ecma-118'=>"ISO-8859-7",'elot_928'=>"ISO-8859-7",'greek'=>"ISO-8859-7",'greek8'=>"ISO-8859-7",'iso-8859-7'=>"ISO-8859-7",'iso-ir-126'=>"ISO-8859-7",'iso8859-7'=>"ISO-8859-7",'iso88597'=>"ISO-8859-7",'iso_8859-7'=>"ISO-8859-7",'iso_8859-7:1987'=>"ISO-8859-7",'sun_eu_greek'=>"ISO-8859-7",'csiso88598e'=>"ISO-8859-8",'csisolatinhebrew'=>"ISO-8859-8",'hebrew'=>"ISO-8859-8",'iso-8859-8'=>"ISO-8859-8",'iso-8859-8-e'=>"ISO-8859-8",'iso-ir-138'=>"ISO-8859-8",'iso8859-8'=>"ISO-8859-8",'iso88598'=>"ISO-8859-8",'iso_8859-8'=>"ISO-8859-8",'iso_8859-8:1988'=>"ISO-8859-8",'visual'=>"ISO-8859-8",'csiso88598i'=>"ISO-8859-8-I",'iso-8859-8-i'=>"ISO-8859-8-I",'logical'=>"ISO-8859-8-I",'cskoi8r'=>"KOI8-R",'koi'=>"KOI8-R",'koi8'=>"KOI8-R",'koi8-r'=>"KOI8-R",'koi8_r'=>"KOI8-R",'koi8-ru'=>"KOI8-U",'koi8-u'=>"KOI8-U",'csmacintosh'=>"macintosh",'mac'=>"macintosh",'macintosh'=>"macintosh",'x-mac-roman'=>"macintosh",'csiso2022kr'=>"replacement",'hz-gb-2312'=>"replacement",'iso-2022-cn'=>"replacement",'iso-2022-cn-ext'=>"replacement",'iso-2022-kr'=>"replacement",'replacement'=>"replacement",'csshiftjis'=>"Shift_JIS",'ms932'=>"Shift_JIS",'ms_kanji'=>"Shift_JIS",'shift-jis'=>"Shift_JIS",'shift_jis'=>"Shift_JIS",'sjis'=>"Shift_JIS",'windows-31j'=>"Shift_JIS",'x-sjis'=>"Shift_JIS",'unicodefffe'=>"UTF-16BE",'utf-16be'=>"UTF-16BE",'csunicode'=>"UTF-16LE",'iso-10646-ucs-2'=>"UTF-16LE",'ucs-2'=>"UTF-16LE",'unicode'=>"UTF-16LE",'unicodefeff'=>"UTF-16LE",'utf-16'=>"UTF-16LE",'utf-16le'=>"UTF-16LE",'unicode-1-1-utf-8'=>"UTF-8",'unicode11utf8'=>"UTF-8",'unicode20utf8'=>"UTF-8",'utf-8'=>"UTF-8",'utf8'=>"UTF-8",'x-unicode20utf8'=>"UTF-8",'cp1250'=>"windows-1250",'windows-1250'=>"windows-1250",'x-cp1250'=>"windows-1250",'cp1251'=>"windows-1251",'windows-1251'=>"windows-1251",'x-cp1251'=>"windows-1251",'ansi_x3.4-1968'=>"windows-1252",'ascii'=>"windows-1252",'cp1252'=>"windows-1252",'cp819'=>"windows-1252",'csisolatin1'=>"windows-1252",'ibm819'=>"windows-1252",'iso-8859-1'=>"windows-1252",'iso-ir-100'=>"windows-1252",'iso8859-1'=>"windows-1252",'iso88591'=>"windows-1252",'iso_8859-1'=>"windows-1252",'iso_8859-1:1987'=>"windows-1252",'l1'=>"windows-1252",'latin1'=>"windows-1252",'us-ascii'=>"windows-1252",'windows-1252'=>"windows-1252",'x-cp1252'=>"windows-1252",'cp1253'=>"windows-1253",'windows-1253'=>"windows-1253",'x-cp1253'=>"windows-1253",'cp1254'=>"windows-1254",'csisolatin5'=>"windows-1254",'iso-8859-9'=>"windows-1254",'iso-ir-148'=>"windows-1254",'iso8859-9'=>"windows-1254",'iso88599'=>"windows-1254",'iso_8859-9'=>"windows-1254",'iso_8859-9:1989'=>"windows-1254",'l5'=>"windows-1254",'latin5'=>"windows-1254",'windows-1254'=>"windows-1254",'x-cp1254'=>"windows-1254",'cp1255'=>"windows-1255",'windows-1255'=>"windows-1255",'x-cp1255'=>"windows-1255",'cp1256'=>"windows-1256",'windows-1256'=>"windows-1256",'x-cp1256'=>"windows-1256",'cp1257'=>"windows-1257",'windows-1257'=>"windows-1257",'x-cp1257'=>"windows-1257",'cp1258'=>"windows-1258",'windows-1258'=>"windows-1258",'x-cp1258'=>"windows-1258",'dos-874'=>"windows-874",'iso-8859-11'=>"windows-874",'iso8859-11'=>"windows-874",'iso885911'=>"windows-874",'tis-620'=>"windows-874",'windows-874'=>"windows-874",'x-mac-cyrillic'=>"x-mac-cyrillic",'x-mac-ukrainian'=>"x-mac-cyrillic",'x-user-defined'=>"x-user-defined"];
    protected const NAME_MAP = ['Big5'=>\MensBeam\Intl\Encoding\Big5::class,'EUC-JP'=>\MensBeam\Intl\Encoding\EUCJP::class,'EUC-KR'=>\MensBeam\Intl\Encoding\EUCKR::class,'gb18030'=>\MensBeam\Intl\Encoding\GB18030::class,'GBK'=>\MensBeam\Intl\Encoding\GBK::class,'IBM866'=>\MensBeam\Intl\Encoding\IBM866::class,'ISO-2022-JP'=>\MensBeam\Intl\Encoding\ISO2022JP::class,'ISO-8859-10'=>\MensBeam\Intl\Encoding\ISO885910::class,'ISO-8859-13'=>\MensBeam\Intl\Encoding\ISO885913::class,'ISO-8859-14'=>\MensBeam\Intl\Encoding\ISO885914::class,'ISO-8859-15'=>\MensBeam\Intl\Encoding\ISO885915::class,'ISO-8859-16'=>\MensBeam\Intl\Encoding\ISO885916::class,'ISO-8859-2'=>\MensBeam\Intl\Encoding\ISO88592::class,'ISO-8859-3'=>\MensBeam\Intl\Encoding\ISO88593::class,'ISO-8859-4'=>\MensBeam\Intl\Encoding\ISO88594::class,'ISO-8859-5'=>\MensBeam\Intl\Encoding\ISO88595::class,'ISO-8859-6'=>\MensBeam\Intl\Encoding\ISO88596::class,'ISO-8859-7'=>\MensBeam\Intl\Encoding\ISO88597::class,'ISO-8859-8'=>\MensBeam\Intl\Encoding\ISO88598::class,'ISO-8859-8-I'=>\MensBeam\Intl\Encoding\ISO88598I::class,'KOI8-R'=>\MensBeam\Intl\Encoding\KOI8R::class,'KOI8-U'=>\MensBeam\Intl\Encoding\KOI8U::class,'macintosh'=>\MensBeam\Intl\Encoding\Macintosh::class,'replacement'=>\MensBeam\Intl\Encoding\Replacement::class,'Shift_JIS'=>\MensBeam\Intl\Encoding\ShiftJIS::class,'UTF-16BE'=>\MensBeam\Intl\Encoding\UTF16BE::class,'UTF-16LE'=>\MensBeam\Intl\Encoding\UTF16LE::class,'UTF-8'=>\MensBeam\Intl\Encoding\UTF8::class,'windows-1250'=>\MensBeam\Intl\Encoding\Windows1250::class,'windows-1251'=>\MensBeam\Intl\Encoding\Windows1251::class,'windows-1252'=>\MensBeam\Intl\Encoding\Windows1252::class,'windows-1253'=>\MensBeam\Intl\Encoding\Windows1253::class,'windows-1254'=>\MensBeam\Intl\Encoding\Windows1254::class,'windows-1255'=>\MensBeam\Intl\Encoding\Windows1255::class,'windows-1256'=>\MensBeam\Intl\Encoding\Windows1256::class,'windows-1257'=>\MensBeam\Intl\Encoding\Windows1257::class,'windows-1258'=>\MensBeam\Intl\Encoding\Windows1258::class,'windows-874'=>\MensBeam\Intl\Encoding\Windows874::class,'x-mac-cyrillic'=>\MensBeam\Intl\Encoding\XMacCyrillic::class,'x-user-defined'=>\MensBeam\Intl\Encoding\XUserDefined::class];

    /** Returns a new decoder for the specified $encodingLabel operating on $data, or null if the label is not valid
     * 
     * If $data includes a UTF-8 or UTF-16 byte order mark, this will take precedence over the specified encoding
     * 
     * @param string $encodingLabel One of the encoding labels listed in the specification e.g. "utf-8", "Latin1", "shift_JIS"
     * @param string $data The string to decode
     * @param bool $fatal If true, throw enceptions when encountering invalid input. If false, substitute U+FFFD REPLACEMENT CHARACTER instead
     * @param bool $allowSurrogates If true, treats surrogate characters as valid input; this only affects UTF-8 and UTF-16 encodings
     * 
     * @see https://encoding.spec.whatwg.org#names-and-labels
     */
    public static function createDecoder(string $encodingLabel, string $data, bool $fatal = false, bool $allowSurrogates = false): ?Decoder {
        $encoding = self::matchLabel(self::sniffBOM($data) ?? $encodingLabel);
        if ($encoding) {
            $class = $encoding['class'];
            return new $class($data, $fatal, $allowSurrogates);
        } else {
            return null;
        }
    }

    /** Returns a new encoder for the specified $encodingLabel, or null if the label is not valid
     * 
     * @param string $encodingLabel One of the encoding labels listed in the specification e.g. "utf-8", "Latin1", "shift_JIS"
     * @param bool $fatal If true (the default) exceptions will be thrown when a character cannot be represented in the target encoding; if false HTML character references will be substituted instead
     * 
     * @see https://encoding.spec.whatwg.org#names-and-labels
     */
    public static function createEncoder(string $encodingLabel, bool $fatal = true): ?Encoder {
        try {
            return new Encoder($encodingLabel, $fatal);
        } catch (EncoderException $e) {
            return null;
        }
    }

    /** Returns metadata about the encoding identified by $label, or null if the label is not valid
     * 
     * The returned array will contain the following keys:
     * 
     * - label: The normalized representation of the specified label
     * - name: The canonical name of the encoding
     * - class: the fully-qualified name of the class which implements the encoding
     * - encoder: A boolean denoting whether the encoding includes an encoder
     * 
     * @param string $label One of the encoding labels listed in the specification e.g. "utf-8", "Latin1", "shift_JIS"
     * @see https://encoding.spec.whatwg.org#names-and-labels
     */
    public static function matchLabel(string $label): ?array {
        $label = strtolower(trim($label));
        $name = self::LABEL_MAP[$label] ?? null;
        if ($name) {
            $class = self::NAME_MAP[$name];
            $encoder = method_exists($class, "encode");
            return [
                'label' => $label,
                'name' => $name,
                'class' => $class,
                'encoder' => $encoder,
            ];
        } else {
            return null;
        }
    }

    /** Finds a Unicode byte order mark in a byte stream and returns the detected encoding, if any
     * 
     * @param string $data The string to examine
     */
    public static function sniffBOM(string $data): ?string {
        if (substr($data, 0, 3) === "\xEF\xBB\xBF") {
            return "UTF-8";
        } elseif (substr($data, 0, 2) === "\xFE\xFF") {
            return "UTF-16BE";
        } elseif (substr($data, 0, 2) === "\xFF\xFE") {
            return "UTF-16LE";
        } else {
            return null;
        }
    }
}
