<?php
/** @license MIT
 * Copyright 2018 J. King et al.
 * See LICENSE and AUTHORS files for details */

declare(strict_types=1);
namespace MensBeam\UTF8;

const NS_BASE = __NAMESPACE__."\\";
define(NS_BASE."BASE", dirname(__DIR__).DIRECTORY_SEPARATOR);
ini_set("memory_limit", "-1");
error_reporting(\E_ALL);
require_once BASE."vendor".DIRECTORY_SEPARATOR."autoload.php";

if (function_exists("xdebug_set_filter")) {
    xdebug_set_filter(\XDEBUG_FILTER_CODE_COVERAGE, \XDEBUG_PATH_WHITELIST, [BASE."lib/"]);
}
