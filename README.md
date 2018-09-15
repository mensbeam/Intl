# Dependency-free internationalization tools for PHP

While PHP's [internationalization extension][PHP_INTL] offers excellent and extensive functionality for dealing with human languages, character encodings, and various related things, it is not always available. Moreover, its character decoder does not yield the same results as [WHATWG's Encoding standard][ENCODING], making it unsuitable for implementing parsers for URLs or HTML. The more widely used [multi-byte string extension][PHP_MBSTRING] not only suffers the same problems, but is also very slow.

Included here is a partial suite of WHATWG-compatible seekable string decoders which are reasonably performant while requiring no external dependencies or PHP extensions. At present it includes the following encodings:

* UTF-8
* UTF-16
* gb18030
* GBK
* Big5
* EUC-KR
* all single-byte encodings
* x-user-defined

Where applicable, code point encoders are also included. In time it will be extended to cover the entire suite of WHATWG character encodings, and may also provide other character-centric internationalization functionality.

[PHP_INTL]:     https://php.net/manual/en/book.intl.php
[PHP_MBSTRING]: https://php.net/manual/en/book.mbstring.php
[ENCODING]:     https://encoding.spec.whatwg.org/
