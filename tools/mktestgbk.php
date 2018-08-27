<?php
// the Web Platform test suite does not have tests for gb18030, but a pull request was made in 2016 with a partial set of tests
// this script generates a test series from those tests which exercises the index and range tables with single characters
// it is a pedantic set of tests, and so the test suite itself only uses this series in an optional test
$standard_tests = [
    'two-byte GBK'            => "https://raw.githubusercontent.com/web-platform-tests/wpt/5847108cb16dc0047331da3f746652f35b3e9c90/encoding/legacy-mb-schinese/gb18030/gb18030_chars.html",
    'four-byte Han'           => "https://raw.githubusercontent.com/web-platform-tests/wpt/5847108cb16dc0047331da3f746652f35b3e9c90/encoding/legacy-mb-schinese/gb18030/gb18030_extra_han_chars.html",
    'four-byte Hangul'        => "https://raw.githubusercontent.com/web-platform-tests/wpt/5847108cb16dc0047331da3f746652f35b3e9c90/encoding/legacy-mb-schinese/gb18030/gb18030_extra_hangul_chars.html",
    'four-byte miscellaneous' => "https://raw.githubusercontent.com/web-platform-tests/wpt/5847108cb16dc0047331da3f746652f35b3e9c90/encoding/legacy-mb-schinese/gb18030/gb18030_extra_misc_chars.html",
    'four-byte private use'   => "https://raw.githubusercontent.com/web-platform-tests/wpt/5847108cb16dc0047331da3f746652f35b3e9c90/encoding/legacy-mb-schinese/gb18030/gb18030_extra_pua_chars.html",
];
foreach($standard_tests as $name=> $url) {
    $data = make_standard_test($url);
    $in = $data[0];
    $out = $data[1];
    echo "'$name' => [[$in], [$out]],\n";
}

function make_standard_test(string $url): array {
    // retrieve the test data
    $data = file_get_contents($url) or die("Could not retrieve test $url");
    // find the data
    preg_match_all('/<span data-cp="([^"]+)" data-bytes="([^"]+)">/s', $data, $matches, \PREG_SET_ORDER);
    // set up
    $in = $out = [];
    // loop through each match
    foreach ($matches as $match) {
        $bytes = str_replace(" ", "", $match[2]);
        $code = hexdec($match[1]);
        if ($bytes=="A8BC") { // this test is incorrect or out of date; both Vivaldi and Firefox yield code point 7743
            $code = 7743;
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
