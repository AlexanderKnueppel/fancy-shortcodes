<?php
/*
Plugin Name: Fancy Shortcodes
Plugin URI: http://github.com/AlexanderKnueppel/fancy-shortcodes
Description: Adds a button to the rich editor for simple shortcode adding
Version: 0.1
License: MIT
Text Domain: fancyshortcodes
Author: Coding Guy Alex - CodingGuyAlex.com
Author URI: http://alexander-knueppel.de
*/

// don't you load it directly, young lord!
if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden!' );



// some constants
define( 'FANCY_SH_NAME', "Fancy Shortcodes" );
define( 'FANCY_SH_VERSION', "0.1" );
define( 'FANCY_SH_PLUGIN_FILE', __FILE__  );
define( 'FANCY_SH_PLUGIN_SLUG',  plugin_basename( __FILE__ ) );
define( 'FANCY_SH_ENABLE_CACHE', true );
define( 'FANCY_SH_DEBUG', true ); //develop

define( 'FANCY_SH_REQUIRED_PHP_VERSION', '5.3' );                          // because of get_called_class()
define( 'FANCY_SH_REQUIRED_WP_VERSION',  '3.1' );                          // because of esc_textarea()

/**
 * Checks if the system requirements are met
 *
 * @return bool True if system requirements are met, false if not
 */
function fsh_requirements_met() {
	global $wp_version;

	if ( version_compare( PHP_VERSION, FANCY_SH_REQUIRED_PHP_VERSION, '<' ) ) {
		return false;
	}
	if ( version_compare( $wp_version, FANCY_SH_REQUIRED_WP_VERSION, '<' ) ) {
		return false;
	}
	return true;
}

/**
 * Prints an error that the system requirements weren't met.
 */
function fsh_requirements_error() {
	global $wp_version;
	
	require_once( dirname( __FILE__ ) . '/views/requirements-error.php' );
}

/*
 * Check requirements and load main class
 */
if ( fsh_requirements_met() ) {
	require_once( __DIR__ . '/classes/module.php' );
	require_once( __DIR__ . '/classes/settings.php' );
	require_once( __DIR__ . '/classes/fancy-shortcodes.php' );
	require_once( __DIR__ . '/classes/autoupdate.php' );
	require_once( __DIR__ . '/classes/map.php' );

	if ( class_exists( 'Fancy_Shortcodes' ) ) {
		global $fshObject;
		
		$fshObject = Fancy_Shortcodes::get_instance();
		
		register_activation_hook(   __FILE__, array( $fshObject, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $fshObject, 'deactivate' ) );
	}
} else {
	add_action( 'admin_notices', 'fsh_requirements_error' );
}
