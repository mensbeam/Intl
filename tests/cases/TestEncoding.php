<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\TestCase;

use MensBeam\Intl\Encoding;
use MensBeam\Intl\Encoding\Encoder;
use MensBeam\Intl\Encoding\UTF16BE;
use MensBeam\Intl\Encoding\UTF16LE;
use MensBeam\Intl\Encoding\UTF8;

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

    /** @dataProvider provideBOMSniffings */
    public function testCreateADecoderWhileSniffingBOM(string $label, string $string, string $class) {
        $this->assertInstanceOf($class, Encoding::createDecoder($label, $string));
    }

    public function testFailToCreateADecoderFromALabel() {
        $this->assertNull(Encoding::createDecoder("Not a label", ""));
    }

    /** @dataProvider provideLabelData */
    public function testCreateAnEncoderFromALabel(string $label, array $data) {
        if ($data['encoder']) {
            $this->assertInstanceOf(Encoder::class, Encoding::createEncoder($label));
            $this->assertInstanceOf(Encoder::class, Encoding::createEncoder(strtoupper($label)));
            $this->assertInstanceOf(Encoder::class, Encoding::createEncoder("    $label\n\n\r\t"));
        } else {
            $this->assertNull(Encoding::createEncoder($label));
            $this->assertNull(Encoding::createEncoder(strtoupper($label)));
            $this->assertNull(Encoding::createEncoder("    $label\n\n\r\t"));
        }
    }

    public function testFailToCreateAnEncoderFromALabel() {
        $this->assertNull(Encoding::createEncoder("Not a label"));
    }

    public function provideLabelData() {
        $ns = "MensBeam\\Intl\\Encoding\\";
        $labels = [];
        $names = [];
        foreach (new \GlobIterator(\MensBeam\Intl\BASE."/lib/Encoding/*.php", \FilesystemIterator::CURRENT_AS_PATHNAME) as $file) {
            $file = basename($file, ".php");
            $className = $ns.$file;
            $class = new \ReflectionClass($className);
            if ($class->implementsInterface(\MensBeam\Intl\Encoding\Decoder::class) && $class->isInstantiable()) {
                $name = $class->getConstant("NAME");
                $names[$name] = $className;
                foreach ($class->getConstant("LABELS") as $label) {
                    $labels[$label] = $name;
                }
            }
        }
        foreach ($labels as $label => $name) {
            $class = $names[$name];
            $encoder = !in_array($name, ["UTF-16LE", "UTF-16BE", "replacement"]);
            yield [(string) $label, ['label' => (string) $label, 'name' => $name, 'class' => $class, 'encoder' => $encoder]];
        }
    }

    public function provideBOMSniffings() {
        return [
            'No BOM'       => ["UTF-8",     "Hello world!",      UTF8::class],
            'UTF-8 BOM'    => ["Shift_JIS", "\xEF\xBB\xBFA",     UTF8::class],
            'UTF-16BE BOM' => ["UTF-8",     "\xFE\xFF\x00A",     UTF16BE::class],
            'UTF-16LE BOM' => ["UTF-8",     "\xFF\xFEA\x00",     UTF16LE::class],
            'GB18030 BOM'  => ["UTF-8",     "\x84\x31\x95\x33A", UTF8::class],
        ];
    }
}
