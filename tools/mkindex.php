<?php
// retrieve the relevant index file
$label = $argv[1] ?? "";
$label = trim(strtolower($label));
$data = file_get_contents("https://encoding.spec.whatwg.org/index-$label.txt") or die("index file for $label could not be retrieved from network.");
// find lines that contain data
preg_match_all("/^\s*(\d+)\s+0x([0-9A-Z]+)/m", $data, $matches, \PREG_SET_ORDER);
// set up
$dec_char = [];
$dec_code = [];
$enc = [];
$i = 0;
// loop through each line
foreach ($matches as $match) {
    // index is the byte value minus 128
    $index = (int) $match[1];
    // byte is a reconstruction of the hexdecimal value of the byte value, padded to two nybbles
    $byte = strtoupper(str_pad(dechex($index + 128), 2, "0", \STR_PAD_LEFT));
    // code is the Unocide code point
    $code = hexdec($match[2]);
    // hex is the code point in hexadecimal
    $hex = dechex($code);
    // missing indexes necessitate specifying keys explicitly
    if ($index == $i) {
        $key = "";
    } else {
        $key = "$index=>";
        $i = $index;
    }
    $dec_code[] = $key."$code";
    $dec_char[] = $key."\"\\u{".$hex."}\"";
    // the encoder table will be reprocessed later
    $enc[$code] = "\"\\x$byte\"";
    $i++;
}
// sort the encoder table by keys to order it correctly
ksort($enc);
$i = 0;
foreach ($enc as $index => $value) {
    if ($index == $i) {
        $key = "";
    } else {
        $key = "$index=>";
        $i = $index;
    }
    $enc[$index] = "$key$value";
    $i++;
}
$dec_char = implode(",", $dec_char);
$dec_code = implode(",", $dec_code);
$enc = implode(",", $enc);
echo "    const TABLE_DEC_CHAR = [$dec_char];\n";
echo "    const TABLE_DEC_CODE = [$dec_code];\n";
echo "    const TABLE_ENC      = [$enc];\n";
