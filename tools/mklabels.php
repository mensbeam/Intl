<?php
$data = json_decode(file_get_contents("https://encoding.spec.whatwg.org/encodings.json"), true) or die("encoding list could not be retrieved from network.");
$labels = [];
$classes = [];
$longest = 0;
foreach ($data as $block) {
    foreach ($block['encodings'] as $encoding) {
        foreach($encoding['labels'] as $label) {
            $labels[$label] = $encoding['name'];
            $longest = max(strlen($label), $longest);
        }
        $name = $encoding['name'];
        if ($name == "gb18030") {
            $class = strtoupper($name);
        } else {
            $class = $name;
            $class = strtoupper($class[0]).substr($class, 1);
            $class = str_replace("_", "-", $class);
            $found = 0;
            while(($found = strpos($class, "-", $found + 1)) !== false) {
                $class = substr($class,0, $found).strtoupper($class[$found + 1]).substr($class, $found + 2);
            }
            $class = str_replace("-", "", $class);
        }
        $classes[$name] = $class;
    }
}
ksort($labels);
$out = [];
foreach($labels as $label => $name) {
    $pad = str_repeat(" ", $longest - strlen($label));
    $class = $classes[$name];
    $out[] = "        '$label'$pad => \"$class\",";
}
array_unshift($out, '    const LABELS = [');
$out[] = "    ];";
echo implode("\n", $out);
