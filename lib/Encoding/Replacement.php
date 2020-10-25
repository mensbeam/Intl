<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

class Replacement implements Decoder {
    public const NAME = "replacement";
    public const LABELS = [
        "csiso2022kr",
        "hz-gb-2312",
        "iso-2022-cn",
        "iso-2022-cn-ext",
        "iso-2022-kr",
        "replacement",
    ];

    protected $len = 0;
    protected $done = false;
    protected $fatal = false;

    public $posErr = 0;

    public function __construct(string $string, bool $fatal = false, bool $allowSurrogates = false) {
        $this->len = strlen($string);
        $this->fatal = $fatal;
    }

    public function posByte(): int {
        return $this->done ? $this->len : 0;
    }

    public function posChar(): int {
        return $this->done ? 1 : 0;
    }

    public function nextChar(): string {
        if (!$this->eof()) {
            try {
                return $this->peekChar();
            } finally {
                $this->done = true;
                $this->posErr = 1;
            }
        }
        return "";
    }

    public function nextCode() {
        if (!$this->eof()) {
            try {
                return $this->peekCode()[0];
            } finally {
                $this->done = true;
                $this->posErr = 1;
            }
        }
        return false;
    }

    public function seek(int $distance): int {
        if ($distance > 0) {
            if (!$this->eof()) {
                $distance--;
                $this->nextCode();
            }
        } elseif ($distance < 0) {
            if ($this->eof()) {
                $distance++;
                $this->rewind();
            }
        }
        return $distance;
    }

    public function rewind(): void {
        $this->done = false;
    }

    public function peekChar(int $num = 1): string {
        if (!$this->eof() && $num > 0) {
            if ($this->fatal) {
                throw new DecoderException("Unable to decode string", self::E_INVALID_BYTE);
            }
            return "\u{FFFD}";
        }
        return "";
    }

    public function peekCode(int $num = 1): array {
        if (!$this->eof() && $num > 0) {
            if ($this->fatal) {
                throw new DecoderException("Unable to decode string", self::E_INVALID_BYTE);
            }
            return [0xFFFD];
        }
        return [];
    }

    public function lenByte(): int {
        return $this->len;
    }

    public function lenChar(): int {
        return (int) ($this->len > 0);
    }

    public function eof(): bool {
        return $this->done || $this->len === 0;
    }

    public function chars(): \Generator {
        if (!$this->eof()) {
            yield 0 => $this->nextChar();
        }
    }

    public function codes(): \Generator {
        if (!$this->eof()) {
            yield 0 => $this->nextCode();
        }
    }
}
