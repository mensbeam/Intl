<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\Intl\Encoding;

trait GenericEncoding {

    protected $string;
    protected $posByte = 0;
    protected $posChar = 0;
    protected $lenByte = null;
    protected $lenChar = null;
    protected $errMode = self::MODE_REPLACE;

    /** Constructs a new decoder
     * 
     * If $fatal is true, an exception will be thrown whenever an invalid code sequence is encountered; otherwise replacement characters will be substituted
     */
    public function __construct(string $string, bool $fatal = false) {
        $this->string = $string;
        $this->lenByte = strlen($string);
        $this->errMode = $fatal ? self::MODE_FATAL_DEC : self::MODE_REPLACE;
    }

    /** Returns the current byte position of the decoder */
    public function posByte(): int {
        return $this->posByte;
    }

    /** Returns the current character position of the decoder */
    public function posChar(): int {
        return $this->posChar;
    }

    /** Seeks to the start of the string
     *
     * This is usually faster than using the seek method for the same purpose
    */
    public function rewind() {
        $this->posByte = 0;
        $this->posChar = 0;
    }

    /** Retrieves the next $num characters (in UTF-8 encoding) from the string without advancing the character pointer */
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

    /** Retrieves the next $num code points from the string, without advancing the character pointer */
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

    /** Calculates the length of the string in code points
     *
     * Note that this may involve processing to the end of the string
    */
    public function len(): int {
        return $this->lenChar ?? (function() {
            $state = $this->stateSave();
            while ($this->nextCode() !== false);
            $this->lenChar = $this->posChar;
            $this->stateApply($state);
            return $this->lenChar;
        })();
    }

    /** Generates an iterator which steps through each character in the string */
    public function chars(): \Generator {
        while (($c = $this->nextChar()) !== "") {
            yield ($this->posChar - 1) => $c;
        }
    }

    /** Generates an iterator which steps through each code point in the string  */
    public function codes(): \Generator {
        while (($c = $this->nextCode()) !== false) {
            yield ($this->posChar - 1) => $c;
        }
    }

    /** Returns a copy of the decoder's state to keep in memory */
    protected function stateSave(): array {
        return [
            'posChar' => $this->posChar,
            'posByte' => $this->posByte,
        ];
    }

    /** Sets the decoder's state to the values specified */
    protected function stateApply(array $state) {
        foreach ($state as $key => $value) {
            $this->$key = $value;
        }
    }

    /** Handles decoding and encoding errors */
    protected static function err(int $mode, $data = null) {
        switch ($mode) {
            case self::MODE_NULL:
                // used internally during backward seeking
                return null;
            case self::MODE_REPLACE:
                // standard "replace" mode
                return 0xFFFD;
            case self::MODE_HTML:
                // the "html" replacement mode; not applicable to Unicode transformation formats
                return "&#".(string) $data.";";
            case self::MODE_FATAL_DEC:
                // fatal replacement mode for decoders
                throw new DecoderException("Invalid code sequence at character offset {$data[0]} (byte offset {$data[1]})", self::E_INVALID_BYTE);
            case self::MODE_FATAL_ENC:
                // fatal replacement mode for decoders; not applicable to Unicode transformation formats
                throw new EncoderException("Code point $data not available in target encoding", self::E_UNAVAILABLE_CODE_POINT);
            default:
                // indicative of internal bug; should never be triggered
                throw new DecoderException("Invalid replacement mode {$mode}", self::E_INVALID_MODE); // @codeCoverageIgnore
        }
    }
}
