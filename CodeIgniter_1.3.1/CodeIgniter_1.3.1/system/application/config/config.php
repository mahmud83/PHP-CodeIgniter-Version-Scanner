<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your Code Igniter root. Typically this will be your base URL, 
| WITH a trailing slash:
|
|	http://www.your-site.com/
|
*/
$config['base_url']	= "http://www.your-site.com/";

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to 
| something else. If you are using mod_rewrite to remove the page set this 
| variable so that it is blank.
|
*/
$config['index_page'] = "index.php";


/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure 
| there is an available translation if you intend to use something other
| than english.
|
*/
$config['language']	= "english";


/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the 
| URI string.  The default setting of "auto" works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
| 
| 'auto'			Default - auto detects
| 'path_info'		Uses the PATH_INFO 
| 'query_string'	Uses the QUERY_STRING
|
*/
$config['uri_protocol']	= "auto";

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
|
| This option allows you to add a suffix to all URLs generated by Code Igniter.
| For more information please see the user guide:
| 
| http://www.codeigniter.com/user_guide/general/urls.html
*/

$config['url_suffix'] = "";


/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
|
| By default Code Igniter uses search-engine friendtly segment based URLs:
| www.your-site.com/who/what/where/
|
| You can optionally enable standard query string based URLs
| www.your-site.com?who=me&what=something&where=here
|
| Options are: TRUE or FALSE (boolean)
|
| The two other items let you set the query string "words" that will
| invoke your controllers and functions:
| www.your-site.com/index.php?c=controller&m=function
|
| Please note that some of the helpers won't work as expected when
| this feature is enabled, since Code Igniter is designed primarily to
| use segment based URLs.
|
*/
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are "local" or "gmt".  This pref tells the system whether to use 
| your server's local time as the master "now" reference, or convert it to 
| GMT.  See the "date helper" page of the user guide for information 
| regarding date handling.
|
*/
$config['time_reference'] = 'local';

/*
|--------------------------------------------------------------------------
| Enable/Disable Error Logging
|--------------------------------------------------------------------------
|
| If you would like errors or debug messages logged set this variable to 
| TRUE (boolean).  Note: You must set the file permissions on the "logs" folder
| such that it is writable.
|
*/
$config['log_errors'] = FALSE;

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| If you have enabled error logging, you can set an error threshold to 
| determine what gets logged. Threshold options are:
| 	
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise 
| your log files will fill up very fast.
|
*/
$config['log_threshold'] = 4;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| system/logs/ folder.  Use a full server path with trailing slash.
|
*/
$config['log_path'] = '';

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| system/cache/ folder.  Use a full server path with trailing slash.
|
*/
$config['cache_path'] = '';


/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| If you use the Encryption class or the Sessions class with encryption 
| enabled you MUST set an encryption key.  See the user guide for info.
|
*/
$config['encryption_key'] = "";


/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'session_cookie_name' = the name you want for the cookie
| 'encrypt_sess_cookie' = TRUE/FALSE (boolean).  Whether to encrypt the cookie
| 'session_expiration'  = the number of SECONDS you want the session to last.
|  by default sessions last 7200 seconds (two hours).  Set to zero for no expiration.
|
*/
$config['sess_cookie_name']		= 'ci_session';
$config['sess_expiration']		= 7200;
$config['sess_encrypt_cookie']	= FALSE;
$config['sess_use_database']	= FALSE;
$config['sess_table_name']		= 'ci_sessions';
$config['sess_match_ip']		= TRUE;
$config['sess_match_useragent']	= TRUE;


/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
| 
| 'cookie_prefix' = Set a prefix if you need to avoid collisions
| 'cookie_domain' = Set to .your-domain.com for site-wide cookies
| 'cookie_path'   =  Typically will be a forward slash
|
*/
$config['cookie_prefix']	= "";
$config['cookie_domain']	= ""; 
$config['cookie_path']		= "/";

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Determines whether the XSS filter is always active when GET, POST or 
| COOKIE data is encountered
|
*/
$config['global_xss_filtering'] = FALSE;


?>