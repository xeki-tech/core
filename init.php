<?php
$_DEBUG_MODE = true;
$_DEFAULT_PAGE_ERROR = '_default_error.php';
// load base library
require_once('libs/xeki_util_methods.php');
require_once('libs/main_core.php');

\xeki\core::$SYSTEM_PATH_BASE = $_SYSTEM_PATH_BASE;
\xeki\core::init();


error_reporting(E_ALL);
function errorHandler()
{
    global $_DEBUG_MODE;
    global $_DEFAULT_PAGE_ERROR;
    $error = error_get_last();
    // fatal error, E_ERROR === 1
    if ($_DEBUG_MODE && isset($error['type'])) {
        if ($error['line'] != 0) {
            d("File: " . $error['file'] . " Line: <b>" . $error['line'] . "</b>");
            d("Type: " . $error['type']);
            d("Message: " . $error['message']);
        }
    }

    if (isset($error['type']) && $error['type'] === 64) {## handle errors
        require("core/controllers/$_DEFAULT_PAGE_ERROR");
        die();
    }
}

set_error_handler('errorHandler');
register_shutdown_function('errorHandler');

// option origin valid

$_ARRAY_RUN_END = array();
## general project
require_once('core/config.php');

## CHECK FORCE SSL
// if is not ssl and
if ($AG_FORCE_SSL) {
    $redirect_to_ssl = false;
    if (isset($_SERVER['HTTP_CF_VISITOR'])) { #for cloudflare ssl
        $info_cf = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
        if ($info_cf['scheme'] == "http") {
            $redirect_to_ssl = true;
        }
    } else if (!isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') {
        $redirect_to_ssl = true;
    } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https") {
        $redirect_to_ssl = true;
        $_SERVER['HTTPS'] == 'on';
        $_SERVER['scheme'] == 'https';
        $_SERVER['REQUEST_SCHEME'] == 'https';
    }
    // valid domain
    if ($redirect_to_ssl) {
        if (is_countable($AG_SSL_DOMAINS)) {
            $temp_len = count($AG_SSL_DOMAINS);
            if ($temp_len > 0) {
                $redirect_to_ssl = false;
                foreach ($AG_SSL_DOMAINS as $item) {
                    if ($_SERVER['HTTP_HOST'] == $item) $redirect_to_ssl = true;
                }
            }
        }

    }
    if ($redirect_to_ssl) {
        $to = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: " . $to);
        echo '<meta http-equiv="refresh" content="0;URL="' . $to . '"/>';
        echo '<script>window.location.replace("' . $to . '");</script>';
        exit();
    }
}

## Check compress for domains
if (is_countable($COMPRESS_DOMAIN)) {
    $temp_len = count($COMPRESS_DOMAIN);
    if ($temp_len > 0) {
        foreach ($COMPRESS_DOMAIN as $item) {
            if ($_SERVER['HTTP_HOST'] == $item) {
                $_DEBUG_MODE = false;
            }
        }
    }
}


define("DEBUG_MODE", $_DEBUG_MODE, true);

// Enable error reporting from config
if ($_DEBUG_MODE) error_reporting(E_ALL);
else error_reporting(0);

### url analyzer ----------------------------------
// URL
require_once('libs/http_request.php');
require_once('libs/routes.php');
$AG_HTTP_REQUEST = new \xeki\http_request();
$path_html = "$_SYSTEM_PATH_BASE/core/pages/";## this update by modules
$path_cache = sys_get_temp_dir() . "/cache/pages/";## this update by modules
//  check auto load
if (!file_exists('libs/vendor/autoload.php')) {
    d("Run composer, <br>More details https://xeki.io/php/composer");
    die();
}
require_once('libs/vendor/autoload.php');


// load Module
require_once('libs/module_manager.php');

$MODULE_CORE_PATH = "$_SYSTEM_PATH_BASE/core/";

// Global params for controllers
$URL_BASE = $html->URL_BASE;
$URL_BASE_COMPLETE = $html->URL_BASE_COMPLETE;
$AG_PARAMS = $html->AG_PARAMS;
$AG_L_PARAM = $html->AG_L_PARAM;