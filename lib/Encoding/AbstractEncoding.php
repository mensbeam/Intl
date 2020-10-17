<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

abstract class AbstractEncoding implements Encoding {
    /** @var string $string The string being decoded */
    protected $string;
    /** @var int $posByte The current byte position in the string */
    protected $posByte = 0;
    /** @var int $posChar The current character (code point) position in the string */
    protected $posChar = 0;
    /** @var int $lenByte The length of the string, in bytes */
    protected $lenByte = null;
    /** @var int $lenChar The length of the string in characters, if known */
    protected $lenChar = null;
    /** @var array $errStack A list of error data to aid in backwards seeking; the most recent error is kept off the stack */
    protected $errStack = [];
    /** @var int $errMark The byte position marking the most recent error. The one or more bytes previous to this position constitute an invalid character */
    protected $errMark = -1;
    /** @var int $errSync The byte position to which to move to skip over the most recent erroneous character */
    protected $errSync = -2;
    /** @var int $errMode The selected error mode (fatal or replace) */
    protected $errMode = self::MODE_REPLACE;
    /** @var bool $allowSurrogates Whether surrogates in encodings other than UTF-16 should be passed through */
    protected $allowSurrogates = false;
    /** @var bool $selfSynchronizing Whether the concrete class represents a self-synchronizing decoder. Such decoders do not use the error stack */
    protected $selfSynchronizing = false;
    /** @var string[] $stateProps The list of properties which constitutee state which must be saved when peeking/seeking; some encodings may add to this last for their own purposes */
    protected $stateProps = ["posChar", "posByte", "posErr"];

    public $posErr = 0;

    abstract protected function seekBack(int $distance): int;

    public function __construct(string $string, bool $fatal = false, bool $allowSurrogates = false) {
        $this->string = $string;
        $this->lenByte = strlen($string);
        $this->errMode = $fatal ? self::MODE_FATAL : self::MODE_REPLACE;
        $this->allowSurrogates = $allowSurrogates;
    }

    public function posByte(): int {
        return $this->posByte;
    }

    public function posChar(): int {
        return $this->posChar;
    }

    public function rewind(): void {
        $this->posByte = 0;
        $this->posChar = 0;
        $this->errMark = -1;
        $this->errSync = -2;
        $this->errStack = [];
    }

    public function nextChar(): string {
        // get the byte at the current position
        $b = @$this->string[$this->posByte];
        if ($b === "") {
            // if the byte is end of input, simply return it
            return "";
        } elseif (ord($b) < 0x80) {
            // if the byte is an ASCII character, simply return it
            $this->posChar++;
            $this->posByte++;
            return $b;
        } else {
            // otherwise return the serialization of the code point at the current position
            return UTF8::encode($this->nextCode());
        }
    }

    public function seek(int $distance): int {
        if ($distance > 0) {
            do {
                $p = $this->nextCode();
            } while ($p !== false && --$distance);
            return $distance;
        } elseif ($distance < 0) {
            $distance = abs($distance);
            if (!$this->posChar) {
                return $distance;
            }
            $mode = $this->errMode;
            $this->errMode = self::MODE_NULL;
            $out = $this->seekBack($distance);
            $this->errMode = $mode;
            return $out;
        } else {
            return 0;
        }
    }

    public function peekChar(int $num = 1): string {
        $out = "";
        $state = $this->stateSave();
        try {
            while ($num-- > 0 && ($b = $this->nextChar()) !== "") {
                $out .= $b;
            }
        } finally {
            $this->stateApply($state);
        }
        return $out;
    }

    public function peekCode(int $num = 1): array {
        $out = [];
        $state = $this->stateSave();
        try {
            while ($num-- > 0 && ($b = $this->nextCode()) !== false) {
                $out[] = $b;
            }
        } finally {
            $this->stateApply($state);
        }
        return $out;
    }

    public function lenByte(): int {
        return $this->lenByte;
    }

    public function lenChar(): int {
        return $this->lenChar ?? (function() {
            $state = $this->stateSave();
            while ($this->nextCode() !== false);
            $this->lenChar = $this->posChar;
            $this->stateApply($state);
            return $this->lenChar;
        })();
    }

    public function eof(): bool {
        return $this->posByte >= $this->lenByte;
    }

    public function chars(): \Generator {
        while (($c = $this->nextChar()) !== "") {
            yield ($this->posChar - 1) => $c;
        }
    }

    public function codes(): \Generator {
        while (($c = $this->nextCode()) !== false) {
            yield ($this->posChar - 1) => $c;
        }
    }

    /** Returns a copy of the decoder's state to keep in memory */
    protected function stateSave(): array {
        $out = ['errCount' => sizeof($this->errStack)];
        foreach ($this->stateProps as $prop) {
            $out[$prop] = $this->$prop;
        }
        return $out;
    }

    /** Sets the decoder's state to the values specified */
    protected function stateApply(array $state): void {
        while (sizeof($this->errStack) > $state['errCount']) {
            list($this->errMark, $this->errSync) = array_pop($this->errStack);
        }
        unset($state['errCount']);
        foreach ($state as $key => $value) {
            $this->$key = $value;
        }
    }

    /** Handles decoding errors */
    protected function errDec(int $mode, int $charOffset, int $byteOffset): ?int {
        if ($mode !== self::MODE_NULL) {
            // expose the error to the user; this disambiguates a literal replacement character
            $this->posErr = $this->posChar;
            // unless the decoder is self-synchronizing, mark the error so that it can be skipped when seeking back
            if (!$this->selfSynchronizing) {
                $this->errStack[] = [$this->errMark, $this->errSync];
                $this->errMark = $this->posByte;
                $this->errSync = $byteOffset;
            }
            if ($mode === self::MODE_FATAL) {
                throw new DecoderException("Invalid code sequence at character offset $charOffset (byte offset $byteOffset)", self::E_INVALID_BYTE);
            } else {
                return 0xFFFD;
            }
        }
        return null;
    }

    /** Handles encoding errors */
    protected static function errEnc(bool $htmlMode, $data = null): string {
        if ($htmlMode) {
            return "&#".(string) $data.";";
        } else {
            // fatal replacement mode for encoders; not applicable to Unicode transformation formats
            throw new EncoderException("Code point $data not available in target encoding", self::E_UNAVAILABLE_CODE_POINT);
        }
    }
}
