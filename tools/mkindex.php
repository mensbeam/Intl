<?php
$labels = [
    'big5'                => "big5",
    //'euc-jp'              => "eucjp",
    'euc-kr'              => "euckr",
    'gb18030'             => "gb18030",
    'ibm866'              => "single_byte",
    //'iso-2022-jp'         => "iso2022jp",
    'iso-8859-10'         => "single_byte",
    'iso-8859-13'         => "single_byte",
    'iso-8859-14'         => "single_byte",
    'iso-8859-15'         => "single_byte",
    'iso-8859-16'         => "single_byte",
    'iso-8859-2'          => "single_byte",
    'iso-8859-3'          => "single_byte",
    'iso-8859-4'          => "single_byte",
    'iso-8859-5'          => "single_byte",
    'iso-8859-6'          => "single_byte",
    'iso-8859-7'          => "single_byte",
    'iso-8859-8'          => "single_byte",
    'koi8-r'              => "single_byte",
    'koi8-u'              => "single_byte",
    'macintosh'           => "single_byte",
    //'shift-jis'           => "shiftjis",
    'windows-1250'        => "single_byte",
    'windows-1251'        => "single_byte",
    'windows-1252'        => "single_byte",
    'windows-1253'        => "single_byte",
    'windows-1254'        => "single_byte",
    'windows-1255'        => "single_byte",
    'windows-1256'        => "single_byte",
    'windows-1257'        => "single_byte",
    'windows-1258'        => "single_byte",
    'windows-874'         => "single_byte",
    'x-mac-cyrillic'      => "single_byte",
];
$label = $argv[1] ?? "";
$label = trim(strtolower($label));
if (!isset($labels[$label])) {
    die("Invalid label specified. Must be one of: ".json_encode(array_keys($labels)));
}
($labels[$label])($label);

// encoding-specific output generators

function single_byte(string $label) {
    $entries = read_index($label, "https://encoding.spec.whatwg.org/index-$label.txt");
    $dec_char = make_decoder_char_array($entries);
    $dec_code = make_decoder_point_array($entires);
    $enc = make_encoder_array($entries);
    echo "const TABLE_DEC_CHAR = $dec_char;\n";
    echo "const TABLE_DEC_CODE = $dec_code;\n";
    echo "const TABLE_ENC      = $enc;\n";
}

function gb18030(string $label) {
    $dec_gbk = make_decoder_point_array(read_index($label, "https://encoding.spec.whatwg.org/index-$label.txt"));
    $ranges = read_index($label, "https://encoding.spec.whatwg.org/index-$label-ranges.txt");
    $dec_max = [];
    $dec_off = [];
    foreach ($ranges as $match) {
        // gather the range starts in one array; they will actually be used as range ends
        $dec_max[] = (int) $match[1];
        // gather the starting code points in another array
        $dec_off[] = hexdec($match[2]);
    }
    // fudge the top of the ranges
    // see https://encoding.spec.whatwg.org/#index-gb18030-ranges-code-point Step 1
    // we also add 0x110000 (one beyond the top of the Unicode range) to the offsets for encoding
    $penult = array_pop($dec_max);
    $dec_max = array_merge($dec_max, [39420, $penult, 1237576]);
    array_splice($dec_off, -1, 0, "null");
    $dec_off[] = 0x110000;
    $dec_max = "[".implode(",", $dec_max)."]";
    $dec_off = "[".implode(",", $dec_off)."]";
    echo "const TABLE_GBK = $dec_gbk;\n";
    echo "const TABLE_RANGES = $dec_max;\n";
    echo "const TABLE_OFFSETS = $dec_off;\n";
}

function big5(string $label) {
    $codes = make_decoder_point_array(read_index($label, "https://encoding.spec.whatwg.org/index-$label.txt"));
    $specials = <<<ARRAY_LITERAL
[
    1133 => [0x00CA, 0x0304],
    1135 => [0x00CA, 0x030C],
    1164 => [0x00EA, 0x0304],
    1166 => [0x00EA, 0x030C],
]
ARRAY_LITERAL;
    // compile an encoder table
    // see https://encoding.spec.whatwg.org/#index-big5-pointer for particulars
    // first get the decoder table as an array
    $table = eval("return $codes;");
    // filter out the low end of the table containing Hong Kong Supplement characters, which are not used during encoding
    $table = array_filter($table, function($key) {
        return (!($key < ((0xA1 - 0x81) * 157)));
    }, \ARRAY_FILTER_USE_KEY);
    // search for each unique code point's pointer in the table, the first for some, the last for a specific set
    $enc = [];
    $a = 0;
    $points = array_unique($table);
    sort($points);
    foreach ($points as $point) {
        // find the correct pointer
        if (in_array($point, [0x2550, 0x255E, 0x256A, 0x5341, 0x5345])) {
            $pointer = array_search($point, array_reverse($table, true));
        } else {
            $pointer = array_search($point, $table);
        }
        // step the output array's key
        if ($a == $point) {
            $key = "";
        } else {
            $a = $point;
            $key = "$point=>";
        }
        $a++;
        $enc[] = "$key$pointer";
    }
    // compose the encoder table literal
    $enc = "[".implode(",", $enc)."]";
    echo "const TABLE_CODES = $codes;\n";
    echo "const TABLE_DOUBLES = $specials;\n";
    echo "const TABLE_ENC = $enc;\n";
}

function euckr(string $label) {
    $codes = make_decoder_point_array(read_index($label, "https://encoding.spec.whatwg.org/index-$label.txt"));
    echo "const TABLE_CODES = $codes;\n";
}

// generic helper functions

function read_index(string $label, string $url): array {
    $data = file_get_contents($url) or die("index file for '$label' could not be retrieved from network.");
    // find lines that contain data
    preg_match_all("/^\s*(\d+)\s+0x([0-9A-Z]+)/m", $data, $matches, \PREG_SET_ORDER);
    return $matches;
}

function make_decoder_point_array(array $entries): string {
    $out = [];
    $i = 0;
    foreach ($entries as $match) {
        $index = (int) $match[1];
        $code = hexdec($match[2]);
        // missing indexes necessitate specifying keys explicitly
        if ($index == $i) {
            $key = "";
        } else {
            $key = "$index=>";
            $i = $index;
        }
        $out[] = $key."$code";
        $i++;
    }
    return "[".implode(",", $out)."]";
}

function make_decoder_char_array(array $entries): string {
    $out = [];
    foreach ($entries as $match) {
        $index = (int) $match[1];
        $code = $match[2];
        // missing indexes necessitate specifying keys explicitly
        if ($index == $i) {
            $key = "";
        } else {
            $key = "$index=>";
            $i = $index;
        }
        $out[] = $key."\"\\u{".$code."}\"";
        $i++;
    }
    return "[".implode(",", $out)."]";
}

// this is only used for single-byte encoders; other encoders instead flip their decoder arrays or use custom tables
function make_encoder_array(array $entries): string {
    $out = [];
    foreach ($entries as $match) {
        $index = (int) $match[1];
        $code = $match[2];
        $byte = strtoupper(str_pad(dechex($index + 128), 2, "0", \STR_PAD_LEFT));
        $out[$code] = "\"\\x$byte\"";
    }
    ksort($out);
    $i = 0;
    foreach ($out as $index => $value) {
        if ($index == $i) {
            $key = "";
        } else {
            $key = "$index=>";
            $i = $index;
        }
        $out[$index] = "$key$value";
        $i++;
    }
    return "[".implode(",", $out)."]";
}
