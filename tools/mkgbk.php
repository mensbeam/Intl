<?php
// retrieve the GB18030 index file for two-byte sequences
$label = "gb18030";
$data = file_get_contents("https://encoding.spec.whatwg.org/index-$label.txt") or die("index file for '$label' could not be retrieved from network.");
// find lines that contain data
preg_match_all("/^\s*(\d+)\s+0x([0-9A-Z]+)/m", $data, $matches, \PREG_SET_ORDER);
// set up
$dec_gbk = [];
// loop through each line
foreach ($matches as $match) {
    // only the code point is relevant
    $dec_gbk[] = hexdec($match[2]);
}

// retrieve the GB18030 range index file for four-byte sequences
$label = "gb18030";
$data = file_get_contents("https://encoding.spec.whatwg.org/index-$label-ranges.txt") or die("range index file for '$label' could not be retrieved from network.");
// find lines that contain data
preg_match_all("/^\s*(\d+)\s+0x([0-9A-Z]+)/m", $data, $matches, \PREG_SET_ORDER);
// set up
$dec_max = [];
$dec_off = [];
// loop through each line
foreach ($matches as $match) {
    // gather the range starts in one array; they will actually be used as range ends
    $dec_max[] = (int) $match[1];
    // gather the starting code points in another array
    $dec_off[] = hexdec($match[2]);
}
// fudge the top of the ranges
// see https://encoding.spec.whatwg.org/#index-gb18030-ranges-code-point Step 1
$penult = array_pop($dec_max);
$dec_max = array_merge($dec_max, [39420, $penult, 1237576]);
array_splice($dec_off, -1, 0, "null");

// output
$dec_gbk = implode(",", $dec_gbk);
$dec_max = implode(",", $dec_max);
$dec_off = implode(",", $dec_off);

echo "    const TABLE_GBK = [$dec_gbk];\n";
echo "    const TABLE_RANGES = [$dec_max];\n";
echo "    const TABLE_OFFSETS = [$dec_off];\n";
