"use strict";
// set out the output pre-formatted text element
window.out = document.createElement("pre");
document.documentElement.appendChild(out);

var encoding = document.getElementsByTagName("meta")[0].getAttribute("charset");

function encodeCodePoint(code, fatal) {
    if (code < 0 || code > 0x10FFFF) {
        return 'new EncoderException("", Encoding::E_INVALID_CODE_POINT)';
    } else {
        var l = document.createElement("a");
        l.href = "http://example.com/?" + String.fromCodePoint(code) + "a";
        var bytes = [];
        let url = l.search.substr(1, l.search.length - 2);
        for (let a = 0; a < url.length; a++) {
            if ((url.charAt(a) == "%" && url.substr(a, 6) == "%26%23") || url.charAt(a) == "&") {
                // character cannot be encoded
                if (fatal) {
                    return 'new EncoderException("", Encoding::E_UNAVAILABLE_CODE_POINT)';
                } else {
                    return decodeURIComponent(url);
                }
            } else if (url.charAt(a) == "%") {
                bytes.push(url.charAt(a + 1) + url.charAt(a + 2));
                a = a + 2;
            } else {
                bytes.push(url.charCodeAt(a).toString(16).padStart(2, "0"));
            }
        }
    }
    return bytes;
}

function wrapCodePoint(code, fatal) {
    var out = encodeCodePoint(code, fatal);
    if (Array.isArray(out)) {
        return '"' + out.join(" ") + '"';
    } else if (out.charAt(0) == "&") {
        return 'bin2hex("' + out + '")';
    } else {
        return out;
    }
}

if(typeof sampleStrings != 'undefined') {
    var decoder = new TextDecoder(encoding);
    for (let name in sampleStrings) {
        let input = sampleStrings[name].replace(/\s/g, "");
        let bytes = [];
        for (let a = 0; a < input.length; a = a + 2) {
            bytes.push(parseInt(input.substr(a, 2), 16));
        }
        let text = decoder.decode(new Uint8Array(bytes));
        let codes = [];
        for (let a = 0; a < text.length; a++) {
            let point = text.codePointAt(a);
            if (point >= 55296 && point <= 57343) {
                // non-BMP characters have trailing low surrogates in JavaScript strings
                continue;
            }
            codes.push(point);
        }
        codes = codes.join(", ");
        bytes = sampleStrings[name];
        let line = "'" + name + "' => [" + '"' + bytes + '", [' + codes + "]],\n";
        out.appendChild(document.createTextNode(line));
    }
    out.appendChild(document.createTextNode("\n\n"));
}

if(typeof sampleCharacters != 'undefined') {
    for (name in sampleCharacters) {
        let code = sampleCharacters[name];
        if (code > -1 && code % 1 == 0) code = "0x" + code.toString(16).toUpperCase();
        let line1 = "'" + name + " (HTML)'  => [false, " + code + ", " + wrapCodePoint(code, false) + "],\n";
        let line2 = "'" + name + " (fatal)' => [true,  " + code + ", " + wrapCodePoint(code, true) + "],\n";
        out.appendChild(document.createTextNode(line1));
        out.appendChild(document.createTextNode(line2));
    }
    out.appendChild(document.createTextNode("\n\n"));
}

if(typeof seekCodePoints != 'undefined') {
    // first gather statistics on the encoding of the specified array of code points
    var stats = [];
    var a = 0;
    var offset = 0;
    for (let b = 0; b < seekCodePoints.length; b++) {
        let code = seekCodePoints[b];
        stats[a] = {
            'code': code,
            'offset': offset,
            'length': 0,
            'bytes': "",
        };
        let bytes = encodeCodePoint(code, true);
        if (Array.isArray(bytes)) {
            stats[a].length = bytes.length;
            stats[a].bytes = bytes.join("").toUpperCase();
            offset = offset + bytes.length;
        } else {
            stats[a].length = 1;
            stats[a].bytes = "()";
            offset = offset + 1;
        }
        a++;
    }
    var end = [a, offset];
    // summarize the statistics in a comment
    var comment = "/*\n";
    for (let a = 0; a < stats.length; a++) {
        let length = (stats[a].length == 1) ? "(1 byte) " : "(" + stats[a].length + " bytes)";
        comment = comment + "    Char " + a + " U+" + stats[a].code.toString(16).padStart(4, "0").padEnd(6, " ").toUpperCase() + " " + length + " Offset " + stats[a].offset + "\n";
    }
    comment = comment + "    End of string at char " + end[0] + ", offset " + end[1] + "\n";
    comment = comment + "*/\n";
    // build the encoded byte string
    var bytes = [];
    for (let char of stats) {
        bytes.push(char.bytes);
    }
    bytes = 'protected $seekString = "' + bytes.join(" ") + '";' + "\n";
    // build the array of code points
    var codes = [];
    for (let char of stats) {
        codes.push("0x" + char.code.toString(16).toUpperCase());
    }
    codes = 'protected $seekCodes = [' + codes.join(", ") + "];\n";
    // build the array of offsets
    var offs = [];
    for (let char of stats) {
        offs.push(char.offset);
    }
    offs.push(end[1]);
    offs = 'protected $seekOffsets = [' + offs.join(", ") + "];\n";
    // output the results
    out.appendChild(document.createTextNode(comment));
    out.appendChild(document.createTextNode(bytes));
    out.appendChild(document.createTextNode(codes));
    out.appendChild(document.createTextNode(offs));
}
