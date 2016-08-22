<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code



/*
	custom variables
*/
if( !defined( "__APP_PATH__" ) )
	define( "__APP_PATH__", realpath( dirname( __FILE__ ) . "/../../" ));

define("__BASE_URL__", "http://52.200.157.158/tally");
define("__SECURE_BASE_URL__", "http://52.200.157.158/tally");

define( "__CUSTOMER_SUPPORT_EMAIL__", "info@whizsolutionsdev.com");
define( "__ADMIN_USER_EMAIL__", "lafleur.andrew@gmail.com");
define( "__ADMIN_MOBILE_NUMBER__", "+16475223242");

//define( "__ADMIN_USER_EMAIL__", "tyagi6931@gmail.com");
//define( "__ADMIN_MOBILE_NUMBER__", "+919718238798");

// Twilio Setup Credentials
define("__TWILIO_ACCOUNT_SID__", "ACbac4a85c6510c1049762cda4a1a965ec");
define("__TWILIO_AUTH_TOKEN__", "bd183a70b82a2c1a28b4ccb1ff055868");
define("__TWILIO_FROM_NUMBER__", "+12898035128");

// Twilio Setup Credentials
/*define("__TWILIO_ACCOUNT_SID__", "ACf3cef6647f7f2674e5ea82cdc8b06191");
define("__TWILIO_AUTH_TOKEN__", "c3b13eed7c4529bb91d81d1470854312");
define("__TWILIO_FROM_NUMBER__", "+12513339387");*/

// Finicity credentials
define("__FINICITY_API_URL__", "https://api.finicity.com/aggregation");
define("__FINICITY_API_KEY__", "448f936b64c14ee514cfe101055d6bfc");
define("__FINICITY_PARTNER_ID__", "2445581452177");
define("__FINICITY_PARTNER_SECRET__", "Q1LCPz8sqZWyV6ETJnSr");
define("__FINICITY_TEST_MODE__", false);
define("__FINICITY_ACCOUNT_OWNER_VERIFICATION_ENABLED__", false);

define("__BASE_ADMIN_URL__", __BASE_URL__ . "/admin");
define("__APP_PATH_ADMIN__", __APP_PATH__ . "/admin");

define("__BASE_IMAGES_URL__", __BASE_URL__ . "/images");
define("__APP_PATH_IMAGES__", __APP_PATH__ . "/images");

define("__BASE_ASSETS_URL__", __BASE_URL__ . "/assets");
define("__APP_PATH_ASSETS__", __APP_PATH__ . "/assets");

define("__APP_PATH_LOGS__", __APP_PATH__ . "/application/logs");

define( "__DBC_SCHEMATA_ADMIN__", "tbl_admin");
define( "__DBC_SCHEMATA_USERS__", "tbl_users");
define( "__DBC_SCHEMATA_SESSIONS__", "tbl_session");
define( "__DBC_SCHEMATA_TEMPLATES__", "tbl_templates");
define( "__DBC_SCHEMATA_CONSTANTS__", "tbl_constants");
define( "__DBC_SCHEMATA_INSTITUTIONS__", "tbl_institutions");
define( "__DBC_SCHEMATA_TRANSACTIONS__", "tbl_transactions");
define( "__DBC_SCHEMATA_USER_QUERIES__", "tbl_user_queries");
define( "__DBC_SCHEMATA_FINICITY_APP_TOKEN__", "tbl_finicity_app_token");
define( "__DBC_SCHEMATA_FORGOT_PASSWORD_LINK__", "tbl_forgotpasswordlink");
define( "__DBC_SCHEMATA_USER_MOBILE_VERIFY_MAP__", "tbl_user_mobile_verification");

define( "__DBC_SCHEMATA_USER_SAVINGS_TRANSFERS__", "tbl_svaings_transfers");
define( "__DBC_SCHEMATA_SAVING_ACCOUNT_CHANGED__", "tbl_saving_account_changed");
define( "__DBC_SCHEMATA_USER_CURRENT_BALANCE__", "tbl_user_linked_account_current_balance");
define( "__DBC_SCHEMATA_MONTHLY_CALCULATIONS__", "tbl_monthly_calculations");
define( "__DBC_SCHEMATA_SAVINGS_TRANSACTIONS__", "tbl_saving_transactions");

define( "__VLD_ERROR_DISPLAY__", "<div class=\"alert alert-danger\">{ERROR}</div>" );
define("__LOG_DATE_FORMAT__", 'd m Y H:i:s');
define("__LOG_LINE_FORMAT__", "{TIME} {SEVERITY} {ERROR_TYPE} {FILE} {FUNCTION} {LINE} {ERROR}\r\n");

/**
 * Validate an int, uses is_numeric
 *
 *
 */
	define( "__VLD_CASE_NUMERIC__", "NUMERIC" );
	
/**
 * Validate an digits, uses /^[0-9]*$/
 *
 *
 */
	define( "__VLD_CASE_DIGITS__", "DIGITS" );
	
/**
 * Validate an credit card, uses is_numeric
 *
 * 
 */
	define( "__VLD_CASE_CARD__", "CARD" );
/**
 * Validate numbers & letters, REGEX: /^[a-z0-9]+$/i
 *
 * 
 */
	define( "__VLD_CASE_ALPHANUMERIC__", "ALPHANUMERIC" );

/**
 * Validate URI, REGEX: /^[a-z0-9\-\_\.]+$/i
 *
 * 
 */
	define( "__VLD_CASE_URI__", "URI" );
/**
 * Validate alpha letters, REGEX: /^[a-z]+$/i
 *
 * 
 */
	define( "__VLD_CASE_ALPHA__", "ALPHA" );
/**
 * Validate anything, allows ANYTHING!
 *
 * 
 */
	define( "__VLD_CASE_ANYTHING__", "ANYTHING" );
/**
 * Validate email, REGEX: /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z0-9]{2,3})$/i
 *
 * 
 */
	define( "__VLD_CASE_EMAIL__", "EMAIL" );
/**
 * Validate boolean, uses is_bool || === 0 || === "0"
 *
 * 
 */
	define( "__VLD_CASE_BOOL__", "BOOL" );
/**
 * Validate boolean, uses is_bool
 *
 * 
 */
	define( "__VLD_CASE_STRICTBOOL__", "STRICTBOOL" );
/**
 * Validate address, REGEX: /^[a-z0-9,.#-_\s]+$/i
 *
 * 
 */
	define( "__VLD_CASE_ADDRESS__", "ADDRESS" );
/**
 * Validate name, REGEX: /^[a-z0-9,.#-_\s]+$/i
 *
 * 
 */
	define( "__VLD_CASE_NAME__", "NAME" );
/**
 * Validate url, REGEX: _^(?:([^:/?#]+):)?(?://([^/?#]*))?([^?#]*)(?:\?([^#]*))?(?:#(.*))?$_
 *
 *
 */
	define( "__VLD_CASE_URL__", "URL" );
/**
 * Validate username, REGEX: /^[\S]+$/i
 *
 * 
 */
	define( "__VLD_CASE_USERNAME__", "USERNAME" );
/**
 * Validate password, REGEX: /^[\S]+$/i
 *
 * 
 */
	define( "__VLD_CASE_PASSWORD__", "PASSWORD" );
/**
 * Validate date, uses strtotime
 *
 * 
 */
	define( "__VLD_CASE_DATE__", "DATE" );
/**
 * Validate phone, REGEX: /^\d{3}-\d{3}-\d{4}$/
 *
 * 
 */
	define( "__VLD_CASE_PHONE__", "PHONE" );
/**
 * Validate mobile phone, REGEX: /^\d{3}-\d{3}-\d{4}$/
 *
 * 
 */
	define( "__VLD_CASE_MOBILE_PHONE__", "MOBILE_PHONE" );
/**
 * Validate phone, REGEX: /^\d{10}$/
 *
 *
 */
	define( "__VLD_CASE_PHONE2__", "PHONE_2" );
/**
 * Validate US money, REGEX: /^[0-9]+(\.[0-9]{2})*$/
 *
 * 
 */
	define( "__VLD_CASE_MONEY_US__", "MONEY_US" );
/**
 * Validate DECIMAL, REGEX: /^[0-9]+(\.[0-9]{2})*$/
 *
 *
 */
	define( "__VLD_CASE_DECIMAL__", "DECIMAL" );
/**
 * Validate drop down is selected and > 0
 *
 *
 */
	define( "__VLD_CASE_DD_NON_0__", "DD_NON_0" );
	
/**
 * Validate for a whole number
 *
 *
 */
	define( "__VLD_CASE_WHOLE_NUM__", "WHOLE_NUM" );
	
/**
 * Validate for a file name
 *
 *
 */
	define( "__VLD_CASE_FILE_NAME__", "FILE_NAME" );

/**
 * Validate Image is selected and type
 *
 *
 */
	define( "__VLD_CASE_IMAGE__", "IMAGE" );
	
define("__FRONT_END_COOKIE__","tally_front");
define("__SESSION_LIFETIME__", "+ 12 Hours");
define("__ENCRYPT_KEY__", "t@11y");
define("__PAGINATION_RECORD_LIMIT__", 10);
