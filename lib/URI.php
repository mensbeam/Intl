<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace JKingWeb\URI;

class URI {
    /** List of "special" schemes and their default port numbers */
    const SCHEME_SPECIAL = [
        'ftp'    => 21,
        'file'   => null,
        'gopher' => 70,
        'http'   => 80,
        'https'  => 443,
        'ws'     => 80,
        'wss'    => 443,
    ];
    /** Default port numbers for all schemes registered with IANA 
     * 
     * If a scheme is in the IANA registry but not listed here, then it likely did not exist when this list was last compiled
    */
    const SCHEME_DEFAULT_PORTS = [
        'aaa' => 3868,
        'aaas' => 5658,
        'about' => null,
        'acap' => 674,
        'acct' => null,
        'acr' => null,
        'adiumxtra' => null,
        'afp' => 548,
        'afs' => null,
        'aim' => null,
        'appdata' => null,
        'apt' => null,
        'attachment' => null,
        'aw' => null,
        'barion' => null,
        'beshare' => null, // multiple ports
        'bitcoin' => null,
        'blob' => null,
        'bolo' => null,
        'browserext' => null,
        'callto' => null,
        'cap' => 1026,
        'chrome' => null,
        'chrome-extension' => null,
        'cid' => null,
        'coap' => 5683,
        'coap+tcp' => 5683,
        'coap+ws' => null, // it's unclear which port applies here: WebSocket would imply 80, but the specification is ambiguous
        'coaps' => 5684,
        'coaps+tcp' => 5684,
        'coaps+ws' => null, // it's unclear which port applies here: WebSocket would imply 443, but the specification is ambiguous
        'com-eventbrite-attendee' => null,
        'content' => null,
        'conti' => null,
        'crid' => null,
        'cvs' => null,
        'data' => null,
        'dav' => null,
        'diaspora' => null,
        'dict' => 2628,
        'dis' => null,
        'dlna-playcontainer' => null,
        'dlna-playsingle' => null,
        'dns' => 53,
        'dntp' => null,
        'dtn' => null,
        'dvb' => null,
        'ed2k' => null,
        'example' => null, // not an actual scheme
        'facetime' => null,
        'fax' => null,
        'feed' => null,
        'feedready' => null,
        'file' => null,
        'filesystem' => null,
        'finger' => 79,
        'fish' => 22, // an application of SSH
        'ftp' => 21,
        'geo' => null,
        'gg' => null,
        'git' => 9418, // per https://git-scm.com/book/en/v2/Git-on-the-Server-The-Protocols#_the_git_protocol
        'gizmoproject' => null,
        'go' => 1096,
        'gopher' => 70,
        'graph' => null,
        'graphdata' => null, // not in the IANA list, but included as part of the registration for 'graph'
        'gtalk' => null,
        'h323' => null, // several ports are defined in the IANA port registry---unclear which (if any) is implied by the scheme
        'ham' => null,
        'hcp' => null,
        'http' => 80,
        'https' => 443,
        'hxxp' => null, // it would be inappropriate to modify these URLs
        'hxxps' => null, // it would be inappropriate to modify these URLs
        'hydrazone' => null,
        'iax' => null,
        'icap' => 1344,
        'icon' => null,
        'im' => null,
        'imap' => 143,
        'info' => null,
        'iotdisco' => null,
        'ipn' => null,
        'ipp' => 631,
        'ipps' => 631,
        'irc' => 6667,
        'irc6' => 6667,
        'ircs' => 994,
        'iris' => null,
        'iris.beep' => null,
        'iris.lwz' => null,
        'iris.xpc' => null,
        'iris.xpcs' => null,
        'isostore' => null,
        'itms' => null,
        'jabber' => null,
        'jar' => null,
        'jms' => null,
        'keyparc' => null,
        'lastfm' => null,
        'ldap' => 389,
        'ldaps' => 636,
        'lvlt' => null,
        'magnet' => null,
        'mailserver' => null,
        'mailto' => null,
        'maps' => null,
        'market' => null,
        'message' => null,
        'microsoft.windows.camera' => null,
        'microsoft.windows.camera.multipicker' => null,
        'microsoft.windows.camera.picker' => null,
        'mid' => null,
        'mms' => null,
        'modem' => null,
        'mongodb' => null,
        'moz' => null,
        'ms-access' => null,
        'ms-browser-extension' => null,
        'ms-drive-to' => null,
        'ms-enrollment' => null,
        'ms-excel' => null,
        'ms-gamebarservices' => null,
        'ms-gamingoverlay' => null,
        'ms-getoffice' => null,
        'ms-help' => null,
        'ms-infopath' => null,
        'ms-inputapp' => null,
        'ms-lockscreencomponent-config' => null,
        'ms-media-stream-id' => null,
        'ms-mixedrealitycapture' => null,
        'ms-officeapp' => null,
        'ms-people' => null,
        'ms-project' => null,
        'ms-powerpoint' => null,
        'ms-publisher' => null,
        'ms-restoretabcompanion' => null,
        'ms-search-repair' => null,
        'ms-secondary-screen-controller' => null,
        'ms-secondary-screen-setup' => null,
        // what the hell, Microsoft? Seriously?
        'ms-settings' => null,
        'ms-settings-airplanemode' => null,
        'ms-settings-bluetooth' => null,
        'ms-settings-camera' => null,
        'ms-settings-cellular' => null,
        'ms-settings-cloudstorage' => null,
        'ms-settings-connectabledevices' => null,
        'ms-settings-displays-topology' => null,
        'ms-settings-emailandaccounts' => null,
        'ms-settings-language' => null,
        'ms-settings-location' => null,
        'ms-settings-lock' => null,
        'ms-settings-nfctransactions' => null,
        'ms-settings-notifications' => null,
        'ms-settings-power' => null,
        'ms-settings-privacy' => null,
        'ms-settings-proximity' => null,
        'ms-settings-screenrotation' => null,
        'ms-settings-wifi' => null,
        'ms-settings-workplace' => null,
        // You could have just defined one ms-app scheme, you know, never mind one ms-settings...
        'ms-spd' => null,
        'ms-sttoverlay' => null,
        'ms-transit-to' => null,
        'ms-useractivityset' => null,
        'ms-virtualtouchpad' => null,
        'ms-visio' => null,
        'ms-walk-to' => null,
        'ms-whiteboard' => null,
        'ms-whiteboard-cmd' => null,
        'ms-word' => null,
        'msnim' => null,
        'msrp' => null, // explicitly no default port
        'msrps' => null, // explicitly no default port
        'mtqp' => 1038,
        'mumble' => 64738,
        'mupdate' => null,
        'mvn' => null,
        'news' => 119,
        'nfs' => 2049,
        'ni' => null,
        'nih' => null,
        'nntp' => 119,
        'notes' => null,
        'ocf' => null,
        'oid' => null,
        'onenote' => null,
        'onenote-cmd' => null,
        'opaquelocktoken' => null,
        'pack' => null,
        'palm' => null,
        'paparazzi' => null,
        'pkcs11' => null,
        'platform' => null,
        'pop' => 110,
        'pres' => null,
        'prospero' => 1525,
        'proxy' => null,
        'pwid' => null,
        'psyc' => 4404,
        'qb' => null,
        'query' => null,
        'redis' => 6379,
        'rediss' => 6379,
        'reload' => 6084,
        'res' => null,
        'resource' => null,
        'rmi' => 1099,
        'rsync' => 873,
        'rtmfp' => 1935,
        'rtmp' => 1935,
        'rtsp' => 554,
        'rtsps' => 322,
        'rtspu' => 554,
        'secondlife' => null,
        'service' => null,
        'session' => null,
        'sftp' => 22, // application of SSH
        'sgn' => null,
        'shttp' => 80,
        'sieve' => 4190,
        'sip' => 5060,
        'sips' => 5061,
        'skype' => null,
        'smb' => 445,
        'sms' => null,
        'smtp' => 25,
        'snews' => 563,
        'snmp' => 161,
        'soap.beep' => null, // explicit port bypasses SRV lookups
        'soap.beeps' => null, // explicit port bypasses SRV lookups
        'soldat' => null, // port required
        'spiffe' => null, // ports are not used
        'spotify' => null,
        'ssh' => 22,
        'steam' => null,
        'stun' => null,
        'stuns' => null,
        'submit' => 587, // SMTP submission
        'svn' => null,
        'tag' => null,
        'teamspeak' => 8767,
        'tel' => null,
        'teliaeid' => null,
        'telnet' => 23,
        'tftp' => 69,
        'things' => null,
        'thismessage' => null, // not an actual scheme
        'tip' => 3372,
        'tn3270' => 23,
        'tool' => null,
        'turn' => 3478,
        'turns' => 5349,
        'tv' => null,
        'udp' => null, // multiple independent uses
        'unreal' => 7777, // assumed based on Unreal Tournament
        'urn' => null,
        'ut2004' => 7777,
        'v-event' => null,
        'vemmi' => 575,
        'ventrilo' => 3784,
        'videotex' => 516,
        'vnc' => 5900,
        'view-source' => null,
        'wais' => 210,
        'webcal' => null, // unclear if port 80 or 443 should be assumed
        'wpid' => null, // alias of pwid
        'ws' => 80,
        'wss' => 443,
        'wtai' => null,
        'wyciwyg' => null,
        'xcon' => null, // not resolvable
        'xcon-userid' => null,
        'xfire' => null,
        'xmlrpc.beep' => null,  // explicit port bypasses SRV lookups
        'xmlrpc.beeps' => null, // explicit port bypasses SRV lookups
        'xmpp' => null,
        'xri' => null, // unclear; historical
        'ymsgr' => null,
        'z39.50' => 210,
        'z39.50r' => 210,
        'z39.50s' => 210,
        
    ];
    /** 
     * List of schemes which use locator syntax when they are actually names 
     * 
     * If a scheme has no documentation or examples at all, it is assumed to be among these schemes
     */
    const SCHEME_NONSTANDARD = [
        "diaspora",
        "dvb",
        "ed2k",
        "facetime",
        "gizmoproject",
        "hcp",
        "hydrazone",
        "keyparc",
        "lastfm",
        "market",
        "mongodb", // more than one host can be specified, which is non-standard
        "moz", // no documentation
        "moz-icon", // not in the IANA registry; equivalent to 'icon'
        "ms-enrollment",
        "ms-gamebarservices", // no documentation
        "ms-gamingoverlay", // no documentation
        "ms-getoffice", // no documentation
        "ms-help",
        "ms-inputapp", // no documentation
        "ms-lockscreencomponent-config", // no documentation
        "ms-mixedrealitycapture",
        "ms-officeapp",
        "ms-restoretabcompanion",
        "ms-sttoverlay",
        "ms-useractivityset",
        "ms-whiteboard",
        "ms-whiteboard-cmd",
        "ms-windows-store", // not in the IANA registry; documentation shows it uses incorrect syntax
        "notes",
        "onenote-cmd",
        "pack",
        "psyc", // authority section is non-standard
        "qb", // no documentation
        "res",
        "resource",
        "teliaeid",
        "wtai",
        "wyciwyg",
    ];

    // character class identifiers
    const CHR_C0 = 1;
    const CHR_C0_OR_SPACE = 2;
    const CHR_ASCII_ALPHA = 3;
    const CHR_ASCII_ALPHANUM = 4;

    // percent-encoding character set identifiers
    const PE_CONTROL = 1;
    const PE_FRAGMENT = 2;
    const PE_PATH = 3;
    const PE_USERINFO = 4;

    const PE_SET = [
        self::PE_CONTROL  => [],
        self::PE_FRAGMENT => [" ", '"', '`', "<", ">"],
        self::PE_PATH     => [" ", '"', '`', "<", ">", "#", "?", "{", "}"],
        self::PE_USERINFO => [" ", '"', '`', "<", ">", "#", "?", "{", "}", "/", ":", ";", "=", "@", "[", "]", "\\"],
    ];

    // error condition identifiers
    const ERR_LEADING_OR_TRAILING_WS = 1;
    const ERR_EMBEDDED_NEWLINE_OR_TAB = 2;
    const ERR_INVALID_SCHEME_CHAR = 3;
    const ERR_FILE_SCHEME_EXPECTING_DOUBLE_SLASH = 4;
    const ERR_RELATIVE_URL = 5;
    const ERR_SCHEME_EXPECTING_SLASH = 6;
    const ERR_BACKSLASH_FORBIDDEN = 7;
    const ERR_UNEXPECTED_SLASH = 8;
    const ERR_UNEXPECTED_AT = 9;
    const ERR_AUTHORITY_WITHOUT_HOST = 10;

    // parser state identifiers
    const ST_SCHEME_START = 1;
    const ST_SCHEME = 2;
    const ST_NO_SCHEME = 3;
    const ST_FILE = 4;
    const ST_SPECIAL_RELATIVE_OR_AUTHORITY = 5;
    const ST_SPECIAL_AUTHORITY_SLASHES = 6;
    const ST_PATH_OR_AUTHORITY = 7;
    const ST_CANNOT_BE_A_BASE_URL_PATH = 8;
    const ST_FRAGMENT = 9;
    const ST_RELATIVE = 10;
    const ST_SPECIAL_AUTHORITY_IGNORE_SLASHES = 11;
    const ST_AUTHORITY = 12;
    const ST_PATH = 13;
    const ST_RELATIVE_SLASH = 14;
    const ST_HOST = 15;
    const ST_HOSTNAME = 16;
    const ST_FILE_HOST = 17;

    public static $confUseAllSchemePorts = false;

    public $scheme = null;
    public $path = [];

    public $cannotBeBaseUrl = false;
    public $err = [];

    protected function basicUrlParser(string $input, self $base = null, string $encodingOverride = "", self $url = null, int $stateOverride = 0) {
        $pointer = -1;
        $pos = -1;
        // start by getting the byte length of the input
        // this will later function as a signal for end of input
        // initially it also functions to show whether characters 
        // have been removed by stripping operations
        $eof = strlen($input);
        // begin algorithm
        # If url is not given:
        if (!$url) {
            # Set url to a new URL.
            $url = new self;
            # Remove any leading and trailing C0 or space from input.
            $input = trim($input, " \u{0}\u{1}\u{2}\u{3}\u{4}\u{5}\u{6}\u{7}\u{8}\u{9}\u{A}\u{B}\u{C}\u{D}\u{E}\u{F}\u{10}\u{11}\u{12}\u{13}\u{14}\u{15}\u{16}\u{17}\u{18}\u{19}\u{1A}\u{1B}\u{1C}\u{1D}\u{1E}\u{1F}");
            # If input contains any leading or trailing C0 control or space, validation error.
            if (strlen($input) != $oef) {
                $url->err[] = [$pointer, $pos, self::ERR_LEADING_OR_TRAILING_WS];
                $eof = strlen($input);
            }
        }
        # Remove all ASCII tab or newline from input.
        $input = str_replace(["\r", "\n", "\t",], "", $input);
        # If input contains any ASCII tab or newline, validation error.
        if (strlen($input) != $oef) {
            $url->err[] = [$pointer, $pos, self::ERR_EMBEDDED_NEWLINE_OR_TAB];
            $eof = strlen($input);
        }
        # Let state be state override if given, or scheme start state otherwise.
        $state = $stateOverride ?? self::ST_SCHEME_START;
        # Let encoding be UTF-8. If encoding override is given, set encoding to the result of getting an output encoding from encoding override.
        $encoding = ($encodingOverride=="") ? "utf-8" : $this->getOutputEncoding($encodingOverride);
        # Let buffer be the empty string.
        $buffer = "";
        # Let the @ flag, [] flag, and passwordTokenSeenFlag be unset.
        $flagAtSign = $flagSquareBracket = $flagPasswordTokenSeen = false;
        # Let pointer be a pointer to first code point in input.
        // we operate on byte strings: $pos is the byte offset of the character referred to by $pointer
        $pos = 0;
        # Keep running the following state machine by switching on state.
        # If after a run pointer points to the EOF code point, go to the next step.
        # Otherwise, increase pointer by one and continue with the state machine.
        // Note: the state machine is designed to run once even with an empty string
        do {
            # Within a parser algorithm that uses a pointer variable, c references the code point the pointer variable points to.
            // we operate on byte strings: $pos is the byte offset of the character referred to by $pointer; 
            // $posNext is the start of "remaining" i.e. the offset of the next UTF-8 character
            $c = UTF8::get($input, $pos, $posNext);
            // when the algorithm specifies to decrease the pointer by one, the result is to reprocess the current character; we
            // accomplish this by going back to this label, which skips the increment at the end of each iteration
            processChar:
            // switch on state
            switch ($state) {
                # scheme start state
                case self::ST_SCHEME_START:
                    if ($this->isChr($c, self::CHR_ASCII_ALPHA)) {
                        # If c is an ASCII alpha, append c, lowercased, to buffer, and set state to scheme state.
                        $buffer .= strtolower($c);
                        $state = self::ST_SCHEME;
                    } elseif (!$stateOverride) {
                        # Otherwise, if state override is not given, set state to no scheme state, and decrease pointer by one.
                        $state = self::ST_NO_SCHEME;
                        goto processChar;
                    } else {
                        # Otherwise, validation error, return failure.
                        # NOTE: This indication of failure is used exclusively by Location object’s protocol attribute.
                        $url->err[] = [$pointer, $pos, self::ERR_INVALID_SCHEME_CHAR];
                        $url->failure = true;
                        return $url;
                    }
                    break;
                # scheme state
                case self::ST_SCHEME:
                    if ($this->isChr($c, self::CHR_ASCII_ALPHANUM) || strpos("+-.", $c) !== false) {
                        # If c is an ASCII alphanumeric, U+002B (+), U+002D (-), or U+002E (.), append c, lowercased, to buffer.
                        $buffer .= strtolower($c);
                    } elseif ($c==":") {
                        # Otherwise, if c is U+003A (:), then:
                        # If state override is given, then:
                        if ($stateOverride &&
                            # If url’s scheme is a special scheme and buffer is not a special scheme, then return.
                            ($this->isSpecial($url) && !$this->isSpecial($buffer)) ||
                            # If url’s scheme is not a special scheme and buffer is a special scheme, then return.
                            (!$this->isSpecial($url) && $this->isSpecial($buffer)) ||
                            # If url includes credentials or has a non-null port, and buffer is "file", then return.
                            ($buffer=="file" || !is_null($url->port) || strlen((string) $url->username) || strlen((string) $url->password)) ||
                            # If url’s scheme is "file" and its host is an empty host or null, then return.
                            ($url->scheme=="file" && !strlen((string) $url->host))
                        ) {
                            return $url;
                        }
                        # Set url’s scheme to buffer.
                        $url->scheme = $buffer;
                        # If state override is given, then:
                        if ($stateOverride) {
                            # If url’s port is url’s scheme’s default port, then set url’s port to null.
                            // OPTIONAL DEVIATION: we optionally allow any registered scheme's port to be defaulted
                            $portList = (self::$confUseAllSchemePorts ? self::SCHEME_DEFAULT_PORTS : self::SCHEME_SPECIAL);
                            $url->port = (isset($portList[$url->scheme]) && $url->port==$portList[$url->scheme]) ? null : $url->port;
                            # Return.
                            return $url;
                        }
                        # Set buffer to the empty string.
                        $buffer = "";
                        if ($url->scheme=="file") {
                            # If url’s scheme is "file", then:
                            if (substr($input, $posNext, 2) !== "//") {
                                # If remaining does not start with "//", validation error.
                                $url->err[] = [$pointer + 1, $posNext, self::ERR_FILE_SCHEME_EXPECTING_DOUBLE_SLASH];
                            }
                            # Set state to file state.
                            $state = self::ST_FILE;
                        } elseif ($base && $base->scheme===$url->scheme && $this->isSpecial($url)) {
                            # Otherwise, if url is special, base is non-null, and base’s scheme is equal to url’s scheme, set state to special relative or authority state.
                            # NOTE: This means that base’s cannot-be-a-base-URL flag is unset.
                            $state = self::ST_SPECIAL_RELATIVE_OR_AUTHORITY;
                        } elseif ($this->isSpecial($url)) {
                            # Otherwise, if url is special, set state to special authority slashes state.
                            $state = self::ST_SPECIAL_AUTHORITY_SLASHES;
                        } elseif ($input[$posNext]=="/") {
                            # Otherwise, if remaining starts with an U+002F (/), set state to path or authority state and increase pointer by one.
                            $state = self::ST_PATH_OR_AUTHORITY;
                            $pos  = $posNext;
                            $pointer++;
                        } else {
                            # Otherwise, set url’s cannot-be-a-base-URL flag, append an empty string to url’s path, and set state to cannot-be-a-base-URL path state.
                            $url->cannotBeBaseUrl = true;
                            $url->path[] = "";
                            $state = self::ST_CANNOT_BE_A_BASE_URL_PATH;
                        }
                    } elseif (!$stateOverride) {
                        # Otherwise, if state override is not given, set buffer to the empty string, state to no scheme state, and start over (from the first code point in input).
                        $buffer = "";
                        $state = self::ST_NO_SCHEME;
                        $pos = 0;
                        $pointer = 0;
                        goto processChar;
                    } else {
                        # Otherwise, validation error, return failure.
                        # NOTE: This indication of failure is used exclusively by Location object’s protocol attribute. Furthermore, the non-failure termination earlier in this state is an intentional difference for defining that attribute.
                        $url->err[] = [$pointer, $pos, self::ERR_INVALID_SCHEME_CHAR];
                        $url->failure = true;
                        return $url;
                    }
                    break;
                # no scheme state
                case self::ST_NO_SCHEME:
                    if (!$base || ($base->cannotBeBaseUrl && $c != "#")) {
                        # If base is null, or base’s cannot-be-a-base-URL flag is set and c is not U+0023 (#), validation error, return failure.
                        $url->err[] = [$pointer, $pos, self::ERR_RELATIVE_URL];
                        $url->failure = true;
                        return $url;
                    } elseif ($base->cannotBeBaseUrl && $c=="#") {
                        # Otherwise, if base’s cannot-be-a-base-URL flag is set and c is U+0023 (#)
                        $this->map($url, $base, [
                            # set url’s scheme to base’s scheme, 
                            "scheme",
                            # url’s path to a copy of base’s path,
                            "path",
                            # url’s query to base’s query,
                            "query",
                        ]);
                        # url’s fragment to the empty string, 
                        $url->fragment = "";
                        # set url’s cannot-be-a-base-URL flag,
                        $url->cannotBeBaseUrl = true;
                        # and set state to fragment state.
                        $state = self::ST_FRAGMENT;
                    } elseif ($base->scheme != "file") {
                        # Otherwise, if base’s scheme is not "file", set state to relative state and decrease pointer by one.
                        $state = self::ST_RELATIVE;
                        goto processChar;
                    } else {
                        # Otherwise, set state to file state and decrease pointer by one.
                        $state = self::ST_FILE;
                        goto processChar;
                    }
                    break;
                # special relative or authority state
                case self::ST_SPECIAL_RELATIVE_OR_AUTHORITY:
                    if ($c=="/" && $input[$posNext]=="/") {
                        # If c is U+002F (/) and remaining starts with U+002F (/), then set state to special authority ignore slashes state and increase pointer by one.
                        $state = self::ST_SPECIAL_AUTHORITY_IGNORE_SLASHES;
                    } else {
                        # Otherwise, validation error, set state to relative state and decrease pointer by one.
                        $url->err[] = [$pointer, $pos, self::ERR_SCHEME_EXPECTING_SLASH];
                        $state = self::ST_RELATIVE;
                        goto processChar;
                    }
                    break;
                # path or authority state
                case self::ST_PATH_OR_AUTHORITY:
                    if ($c=="/") {
                        # If c is U+002F (/), then set state to authority state.
                        $state = self::ST_AUTHORITY;
                    } else {
                        # Otherwise, set state to path state, and decrease pointer by one.
                        $state = self::ST_PATH;
                        goto processChar;
                    }
                    break;
                # relative state
                case self::ST_RELATIVE:
                    # Set url’s scheme to base’s scheme, and then, switching on c:
                    $url->scheme = $base->scheme;
                    switch ($c) {
                        case "": # The EOF code point
                            $this->map($url, $base, [
                                # Set url’s username to base’s username,
                                "username",
                                # url’s password to base’s password,
                                "password",
                                # url’s host to base’s host,
                                "host",
                                # url’s port to base’s port,
                                "port",
                                # url’s path to a copy of base’s path,
                                "path",
                                # and url’s query to base’s query.
                                "query",
                            ]);
                            break;
                        case "/":
                            # Set state to relative slash state.
                            $state = self::ST_RELATIVE_SLASH;
                            break;
                        case "?":
                            $this->map($url, $base, [
                                # Set url’s username to base’s username,
                                "username",
                                # url’s password to base’s password,
                                "password",
                                # url’s host to base’s host,
                                "host",
                                # url’s port to base’s port,
                                "port",
                                # url’s path to a copy of base’s path,
                                "path",
                            ]);
                            # url’s query to the empty string, 
                            $url->query = "";
                            # and state to query state.
                            $state = self::ST_QUERY;
                            break;
                        case "#":
                            $this->map($url, $base, [
                                # Set url’s username to base’s username,
                                "username",
                                # url’s password to base’s password,
                                "password",
                                # url’s host to base’s host,
                                "host",
                                # url’s port to base’s port,
                                "port",
                                # url’s path to a copy of base’s path,
                                "path",
                                # url’s query to base’s query,
                                "query",
                            ]);
                            # url’s fragment to the empty string,
                            $url->fragment = "";
                            # and state to fragment state.
                            $state = self::ST_FRAGMENT;
                            break;
                        default:
                            if ($this->isSpecial($url) && $c = "\\") {
                                # If url is special and c is U+005C (\), validation error, set state to relative slash state.
                                $url->err[] = [$pointer, $pos, self::ERR_BACKSLASH_FORBIDDEN];
                                $state = self::ST_RELATIVE_SLASH;
                            } else {
                                # Otherwise, run these steps:
                                $this->map($url, $base, [
                                    # Set url’s username to base’s username,
                                    "username",
                                    # url’s password to base’s password,
                                    "password",
                                    # url’s host to base’s host,
                                    "host",
                                    # url’s port to base’s port,
                                    "port",
                                    # url’s path to a copy of base’s path,
                                    "path",
                                ]);
                                # and then remove url’s path’s last item, if any.
                                array_pop($url->path);
                                # Set state to path state, and decrease pointer by one.
                                $state = self::ST_PATH;
                                goto processChar;
                            }
                    }
                    break;
                # relative slash state
                case self::ST_RELATIVE_SLASH:
                    if ($this->isSpecial($url) && ($c=="/" || $c=="\\")) {
                        # If url is special and c is U+002F (/) or U+005C (\), then:
                        # If c is U+005C (\), validation error.
                        if ($c=="\\") {
                            $url->err[] = [$pointer, $pos, self::ERR_BACKSLASH_FORBIDDEN];
                        }
                        # Set state to special authority ignore slashes state.
                        $state = self::ST_SPECIAL_AUTHORITY_IGNORE_SLASHES;
                    } elseif ($c="/") {
                        # Otherwise, if c is U+002F (/), then set state to authority state.
                        $state = self::ST_AUTHORITY;
                    } else {
                        # Otherwise, 
                        $this->map($url, $base, [
                            # set url’s username to base’s username,
                            "username",
                            # url’s password to base’s password,
                            "password",
                            # url’s host to base’s host,
                            "host",
                            # url’s port to base’s port,
                            "port",
                        ]);
                        # state to path state, and then, decrease pointer by one.
                        $state = self::ST_PATH;
                        goto processChar;
                    }
                    break;
                # special authority slashes state
                case self::ST_SPECIAL_AUTHORITY_SLASHES:
                    if ($c=="/" && substr($input, $posNext, 1)=="/") {
                        # If c is U+002F (/) and remaining starts with U+002F (/), then set state to special authority ignore slashes state and increase pointer by one.
                        $state = self::ST_SPECIAL_AUTHORITY_IGNORE_SLASHES;
                        // this has the effect of increasing the pointer by one
                        $pos = $posNext;
                        $c = UTF8::get($input, $pos, $posNext);
                    } else {
                        # Otherwise, validation error, set state to special authority ignore slashes state, and decrease pointer by one.
                        $url->err[] = [$pointer, $pos, self::ERR_SCHEME_EXPECTING_SLASH];
                        $state = self::ST_SPECIAL_AUTHORITY_IGNORE_SLASHES;
                        goto processChar;
                    }
                    break;
                # special authority ignore slashes state
                case self::ST_SPECIAL_AUTHORITY_IGNORE_SLASHES:
                    if ($c != "/" && $c != "\\") {
                        # If c is neither U+002F (/) nor U+005C (\), then set state to authority state and decrease pointer by one.
                        $state = self::ST_AUTHORITY;
                        goto processChar;
                    } else {
                        # Otherwise, validation error.
                        $url->err[] = [$pointer, $pos, self::ERR_UNEXPECTED_SLASH];
                    }
                    break;
                # authority state
                case self::ST_AUTHORITY:
                    if($c=="@") {
                        # If c is U+0040 (@), then:
                        # Validation error.
                        $url->err[] = [$pointer, $pos, self::ERR_UNEXPECTED_AT];
                        # If the @ flag is set, prepend "%40" to buffer.
                        if ($flagAtSign) {
                            $buffer = "%40".$buffer;
                        }
                        # Set the @ flag.
                        $flagAtSign = true;
                        # For each codePoint in buffer:
                        $bPos = 0;
                        $bEof = strlen($buffer);
                        while ($bPos < $bEof) {
                            $codePoint = UTF8::get($buffer, $bPos, $bPosNext);
                            # If codePoint is U+003A (:) and passwordTokenSeenFlag is unset, then set passwordTokenSeenFlag and continue.
                            if ($codePoint==":" && !$flagPasswordTokenSeen) {
                                $flagPasswordTokenSeen = true;
                                // "continue" in the specification means going to the next character
                                $bPos = $bPosNext;
                                continue;
                            }
                            # Let encodedCodePoints be the result of running UTF-8 percent encode codePoint using the userinfo percent-encode set.
                            $encodedCodePoints = $this->percentEncode($codePoint, self::PE_USERINFO);
                            if ($flagPasswordTokenSeen) {
                                # If passwordTokenSeenFlag is set, then append encodedCodePoints to url’s password.
                                $url->password .= $encodedCodePoints;
                            } else {
                                # Otherwise, append encodedCodePoints to url’s username.
                                $url->username .= $encodedCodePoints;
                            }
                        }
                        # Set buffer to the empty string.
                        $buffer = "";
                    } elseif (
                        # Otherwise, if one of the following is true
                        in_array($c, ["/", "?", "#", ""]) || # c is the EOF code point, U+002F (/), U+003F (?), or U+0023 (#)
                        ($this->isSpecial($url) && $c=="\\") # url is special and c is U+005C (\)
                    ) {
                        # then:
                        # If @ flag is set and buffer is the empty string, validation error, return failure.
                        if ($flagAtSign && $buffer = "") {
                            $url->err[] = [$pointer, $pos, self::ERR_AUTHORITY_WITHOUT_HOST];
                            $url->failure = true;
                            return $url;
                        }
                        # Decrease pointer by the number of code points in buffer plus one,
                        // DEVIATION: as with decreasing the pointer by one to reprocess characters, we'll ignore the "plus one" here for self-consitency
                        // we first count the number of characters in the buffer
                        $c = UTF8::len($buffer);
                        // then decrease the advisorty character pointer by that amount
                        $pointer -= $c;
                        // then seek back the same number of characters
                        $pos = UTF8::seek($input, -$c, $pos);
                        // and finally consume that character to get the position of the next character to continue the loop correctly
                        $c = UTF8::get($input, $pos, $posNext);
                        # set buffer to the empty string, and set state to host state.
                        $buffer = "";
                        $state = self::ST_HOST;
                        // and reprocess the first character in the erstwhile buffer
                        goto processChar;
                    } else {
                        # Otherwise, append c to buffer.
                        $buffer .= $c;
                    }
                    break;
                // invalid or unimplemented state
                default:
                    // FIXME: this should be an error, but until the whole state machine is implemented, we stop processing instead
                    return $url;
            }
            # If after a run pointer points to the EOF code point, go to the next step.
            # Otherwise, increase pointer by one and continue with the state machine.
            // we operate on byte strings: $pos is the byte offset of the character referred to by $pointer; 
            // $posNext is the start of "remaining" i.e. the offset of the next UTF-8 character
            $pos = $posNext;
            $pointer++;
        } while ($pos <= $eof);
    }

    protected function getOutputEncoding(string $encoding): string {
        // FIXME: stub
        return $encoding;
    }

    protected function isChr(string $c, int $chrClass) {
        switch ($chrClass) {
            case self::CHR_C0:
                return ($c <= "\u{1F}");
            case self::CHR_C0_OR_SPACE:
                return ($c == " " || $c <= "\u{1F}");
            case self::CHR_ASCII_ALPHA:
                return (($c >= "A" && $c <= "Z") ||  ($c >= "z" && $c <= "z"));
            case self::CHR_ASCII_ALPHANUM:
                return (
                    ($c == (string) (int) $c) || // digits
                    ($c >= "A" && $c <= "Z") ||  // uppercase alphabetic
                    ($c >= "z" && $c <= "z")     // lowercase alphabetic
                );
            default:
                throw new \Exception;
        }
    }

    protected function map(URI $to, URI $from, array $properties): bool {
        foreach ($properties as $prop) {
            $to->$prop = $from->prop;
        }
        return true;
    }

    protected function isSpecial($test): bool {
        $test = ($est instanceof URI) ? $test->scheme : $test;
        return array_key_exists($test, self::SCHEME_SPECIAL);
    }

    protected function percentEncode(string $bytes, int $set): string {
        if (!isset(self::PE_SET[$set])) {
            throw new \Exception;
        }
        $buffer = "";
        foreach ($bytes as $b) {
            if ($b < "\x20" || $b > "\x7E" || in_array($b, self::PE_SET[$set])) {
                $buffer .= "%".strtoupper(bin2hex($b));
            } else {
                $buffer .= $b;
            }
        }
        return $buffer;
    }
}
