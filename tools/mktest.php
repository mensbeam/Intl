<?php
declare(strict_types=1);
// this script generates a test series from the Web Platform test suite which exercises the index tables of multi-byte encodings with single characters
// they are pedantic sets of tests, and so the test suite itself only uses this series in optional tests

$tests = [
    'gb18030' => [
        // the Web Platform test suite does not have tests for gb18030, but a pull request was made in 2016 with a set of tests
        'two-byte GBK'            => "https://raw.githubusercontent.com/web-platform-tests/wpt/5847108cb16dc0047331da3f746652f35b3e9c90/encoding/legacy-mb-schinese/gb18030/gb18030_chars.html",
        'four-byte Han'           => "https://raw.githubusercontent.com/web-platform-tests/wpt/5847108cb16dc0047331da3f746652f35b3e9c90/encoding/legacy-mb-schinese/gb18030/gb18030_extra_han_chars.html",
        'four-byte Hangul'        => "https://raw.githubusercontent.com/web-platform-tests/wpt/5847108cb16dc0047331da3f746652f35b3e9c90/encoding/legacy-mb-schinese/gb18030/gb18030_extra_hangul_chars.html",
        'four-byte miscellaneous' => "https://raw.githubusercontent.com/web-platform-tests/wpt/5847108cb16dc0047331da3f746652f35b3e9c90/encoding/legacy-mb-schinese/gb18030/gb18030_extra_misc_chars.html",
        'four-byte private use'   => "https://raw.githubusercontent.com/web-platform-tests/wpt/5847108cb16dc0047331da3f746652f35b3e9c90/encoding/legacy-mb-schinese/gb18030/gb18030_extra_pua_chars.html",
    ],
    'big5' => [
        'standard characters' => "https://raw.githubusercontent.com/web-platform-tests/wpt/master/encoding/legacy-mb-tchinese/big5/big5_chars.html",
        'extended characters' => "https://raw.githubusercontent.com/web-platform-tests/wpt/master/encoding/legacy-mb-tchinese/big5/big5_chars_extra.html",
    ],
    'euc-kr' => [
        'characters' => "https://raw.githubusercontent.com/web-platform-tests/wpt/master/encoding/legacy-mb-korean/euc-kr/euckr_chars.html",
    ],
    'euc-jp' => [
        'characters' => "https://raw.githubusercontent.com/web-platform-tests/wpt/master/encoding/legacy-mb-japanese/euc-jp/eucjp_chars.html",
    ],
    'iso-2022-jp' => [
        'characters' => "https://raw.githubusercontent.com/web-platform-tests/wpt/master/encoding/legacy-mb-japanese/iso-2022-jp/iso2022jp_chars.html",
    ],
    'shiftjis' => [
        'characters' => "https://raw.githubusercontent.com/web-platform-tests/wpt/master/encoding/legacy-mb-japanese/shift_jis/sjis_chars.html",
    ],
];

$label = $argv[1] ?? "";
$label = trim(strtolower($label));
if (!isset($tests[$label])) {
    die("Invalid label specified. Must be one of: ".json_encode(array_keys($tests)));
}

foreach ($tests[$label] as $name => $url) {
    $data = make_test($label, $url);
    $in = $data[0];
    $out = $data[1];
    echo "'$name' => [[$in], [$out]],\n";
}

function make_test(string $label, string $url): array {
    // retrieve the test data
    $data = file_get_contents($url) or die("Could not retrieve $label test $url");
    // find the data
    preg_match_all('/<span data-cp="([^"]+)" data-bytes="([^"]+)">/s', $data, $matches, \PREG_SET_ORDER);
    // set up
    $in = $out = [];
    // loop through each match
    foreach ($matches as $match) {
        $bytes = str_replace(" ", "", $match[2]);
        $code = hexdec($match[1]);
        if ($label=="gb18030" && $bytes=="A8BC") { // this test is incorrect or out of date; both Vivaldi and Firefox yield code point 7743
            $code = 7743;
        } elseif ($label=="euc-jp") { // three tests are out of date
            $code = ["5C" => 92, "7E" => 126, "A1DD" => 65293][$bytes] ?? $code;
        } elseif ($label=="shiftjis") { // three tests are incorrect
            $code = ["5C" => 92, "7E" => 126, "817C" => 0xFF0D][$bytes] ?? $code;
        }
        // convert the code point to decimal
        $out[] = $code;
        // convert the hex bytes to PHP notation
        $in[] = '"'.$bytes.'"';
    }
    $in = implode(",", $in);
    $out = implode(",", $out);
    return [$in, $out];
}
