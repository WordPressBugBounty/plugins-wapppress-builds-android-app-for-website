<?php
class instantappy_pwa {
	public static $dirPath;
	public static $dirUrl;
	public static $dirInc;
	public static $dirJs;
	public static $dirCss;
	public static $dirImg;
	function __construct() 
	{
if ( ! defined( 'ABSPATH' ) ) {
	return;
}
/**
 * Define constants
 */
if ( ! defined( 'INSTANTAPPY_NAME' ) )
{
		define( 'INSTANTAPPY_NAME'	, 'instantappy-pwa' );
	
}
if ( ! defined( 'INSTANTAPPY_VERSION' ) ) {
	define( 'INSTANTAPPY_VERSION', '0.1' ); // plugin current version
}

if ( ! defined( 'INSTANTAPPY_DIR_URL' ) ) {
	define( 'INSTANTAPPY_DIR_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'INSTANTAPPY_DIR' ) ) {
	define( 'INSTANTAPPY_DIR', __DIR__ );
}
if ( ! defined( 'INSTANTAPPY_PATH' ) ){
 	define( 'INSTANTAPPY_PATH'	, plugin_dir_path( __FILE__ ) ); // Absolute path of plugin directory. 
}
if ( ! defined( 'INSTANTAPPY_PATH_SRC' ) ) {
	define( 'INSTANTAPPY_PATH_SRC'	, plugin_dir_url( __FILE__ ) ); // Plugin SRC/Link .
	}

// Load Files
if ( ! defined('ABSPATH') ) exit;
/***********pluginConstants**********/
		$dirPath  = trailingslashit( plugin_dir_path( __FILE__ ) );
		$dirUrl   = trailingslashit( plugins_url( dirname( plugin_basename( __FILE__ ) ) ) );
		$dirInc   = $dirPath  . 'includes/';
		$dirCss   = $dirUrl  . 'webroot/css/';
		$dirImg   = $dirUrl  . 'webroot/images/';
		$dirJs    = $dirUrl  . 'webroot/js/';
		
	}
}
new instantappy_pwa();