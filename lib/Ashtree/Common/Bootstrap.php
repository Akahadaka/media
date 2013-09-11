<?php
/**
 * Gets Ashtree CMS classes up and running
 * @author andrew.nash
 * @version 3.0
 */

// =========
// Constants
// =========
// Load default config options
// Define all root paths relative to the Ashtree CMS libraries
/**
 * Always relative to root of libraries "lib" folder 
 */
define('ASH_ROOTNAME',   str_replace('//', '/', '/' . preg_replace('/lib.*/', '', str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __FILE__)))), TRUE);

define('ASH_ROOTPATH',   preg_replace('/\/$/', '', $_SERVER['DOCUMENT_ROOT']) . ASH_ROOTNAME, TRUE);

define('ASH_ROOTHTTP',  'http://' . $_SERVER['SERVER_NAME'] . ASH_ROOTNAME, TRUE);

define('ASH_ROOTHTTPS', 'https://' . $_SERVER['SERVER_NAME'] . ASH_ROOTNAME, TRUE);

// Define all base paths relative to the initiating script
define('ASH_BASENAME',  str_replace('//', '/', pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME) . '/'), TRUE);

define('ASH_BASEPATH',  preg_replace('/\/$/', '', $_SERVER['DOCUMENT_ROOT']) . ASH_BASENAME, TRUE);

define('ASH_BASEHTTP',  'http://' . $_SERVER['SERVER_NAME'] . ASH_BASENAME, TRUE);

define('ASH_BASEHTTPS', 'https://' . $_SERVER['SERVER_NAME'] . ASH_BASENAME, TRUE);

// Define some library paths
define('ASH_LIB', ASH_ROOTNAME . 'lib/', TRUE);

define('ASH_LIB_JSS', ASH_LIB . 'Javascript/', TRUE);

define('ASH_INC', ASH_ROOTPATH . 'inc/', TRUE);

define('ASH_PLUGINS', ASH_ROOTNAME . 'plugins/', TRUE);

// =========
// Functions
// =========
/**
 * Load local "/cfg.ini" if exists
 * @param String $file
 */
function snow_cfg_definitions($file)
{
    $filename = preg_replace('/^\//', ASH_ROOTPATH,  $file);
    if (is_file($filename) && ($sections = parse_ini_file($filename, TRUE)))
    {
        foreach((array)$sections['SITE'] as $const=>$value)
		{
			define($const, $value, TRUE);	
		}
		
		foreach((array)$sections['DEBUG'] as $const=>$value)
		{
			define($const, $value, TRUE);	
		}
		//Setup any user defined database connections
		$conn = array();
		foreach((array)$sections['DATABASE'] as $var=>$con)
		{
		    if ($var == 'ASH_CONN_FILE') $conn['file'] = $con;
		    else foreach((array)$con as $key=>$val) $conn['here'][$key][strtolower($var)] = $val;
		}//foreach
		
		if (isset($conn['file'])) foreach($conn['file'] as $con) FALSE;#Ashtree_Common_Connection::define($con);
		if (isset($conn['here'])) foreach($conn['here'] as $con) Ashtree_Database_Connection::define($con);
		
		//Setup any user defined ftp connections
		$conn = array();
		foreach((array)$sections['FTP'] as $var=>$con)
		{
		    if ($var == 'ASH_FTP_CONN_FILE') $conn['file'] = $con;
		    else foreach((array)$con as $key=>$val) $conn['here'][$key][strtolower($var)] = $val;
		}//foreach
		
		if (isset($conn['file'])) foreach($conn['file'] as $con) FALSE;#Ashtree_Common_Ftp::define($con);
		if (isset($conn['here'])) foreach($conn['here'] as $con) FALSE;#Ashtree_Common_Ftp::define($con);
	
		return TRUE;
    }
    
    return FALSE;
}

/**
 * Autoload lib classes
 * @param String $class
 */
function ash_lib_autoload($class) 
{
	if (substr_count($class, '_')) {
		if (substr_count($class, 'Ashtree')) {
			$dir_struct_class = 'lib/' . str_replace('_', '/', $class);
		} else {
			list($type, $parent, $name) = explode('_', strtolower($class));
			$dir_struct_class = "{$type}s/{$parent}.{$name}/{$parent}.{$name}";
		}
		require(ASH_ROOTPATH . $dir_struct_class . ".class.php");
	}
}


// ========
// Activate
// ========
// Load local "/php.ini" if exists

// Start a session
session_start();

// Autoload lib classes
spl_autoload_register('ash_lib_autoload');

// Load local "/cfg.ini" if exists
snow_cfg_definitions('/cfg.ini');

// Make some of the common functions directly accessible
function dump($data, $pre=FALSE) {return Ashtree_Common_Debug::dump($data, $pre);}
function redirect($url=NULL) {return Ashtree_Common::redirect($url);}
function is($bool) {return Ashtree_Common::is($bool);}
#function s($word) {return Ashtree_Common::s($word);}

// Auto include include files from "/inc" directory
Ashtree_Common::get_all_includes('/inc');

// Catch and log errors


// Catch and email fatal errors
