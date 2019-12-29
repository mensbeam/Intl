<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\TestCase;

use MensBeam\Intl\Encoding;

class TestEncoding extends \PHPUnit\Framework\TestCase {
    /** @dataProvider provideLabelData */
    public function testMatchALabelToAnEncoding(string $label, array $exp) {
        $this->assertSame($exp, Encoding::matchLabel($label));
        $this->assertSame($exp, Encoding::matchLabel(strtoupper($label)));
        $this->assertSame($exp, Encoding::matchLabel("    $label\n\n\r\t"));
    }

    public function testFailToMatchALabelToAnEncoding() {
        $this->assertNull(Encoding::matchLabel("Not a label"));
    }

    /** @dataProvider provideLabelData */
    public function testCreateADecoderFromALabel(string $label, array $data) {
        $this->assertInstanceOf($data['class'], Encoding::createDecoder($label, ""));
        $this->assertInstanceOf($data['class'], Encoding::createDecoder(strtoupper($label), ""));
        $this->assertInstanceOf($data['class'], Encoding::createDecoder("    $label\n\n\r\t", ""));
    }

    public function testFailToCreateADecoderFromALabel() {
        $this->assertNull(Encoding::createDecoder("Not a label", ""));
    }

    public function provideLabelData() {
        $ns = "MensBeam\\Intl\\Encoding\\";
        $labels = [];
        $names = [];
        foreach (new \GlobIterator(\MensBeam\Intl\BASE."/lib/Encoding/*.php", \FilesystemIterator::CURRENT_AS_PATHNAME) as $file) {
            $file = basename($file, ".php");
            $className = $ns.$file;
            $class = new \ReflectionClass($className);
            if ($class->implementsInterface(\MensBeam\Intl\Encoding\Encoding::class) && $class->isInstantiable()) {
                $name = $class->getConstant("NAME");
                $names[$name] = $className;
                foreach ($class->getConstant("LABELS") as $label) {
                    $labels[$label] = $name;
                }
            }
        }
        $out = [];
        foreach ($labels as $label => $name) {
            $out[] = [(string) $label, ['label' => (string) $label, 'name' => $name, 'class' => $names[$name]]];
        }
        return $out;
    }
}
