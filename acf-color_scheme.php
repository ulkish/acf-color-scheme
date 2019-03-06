<?php

/*
Plugin Name: Color Scheme Field for Advanced Custom Fields PRO
Plugin URI:
Description: Adds a color scheme field type to Advanced Custon Fields Pro. Create your own color schemes using hex and make them available from your
admin panel. You will need to know some PHP to be able to actually change something using this, since this is just the front-end of the functionality, but if you're looking for a controller this is for you.
Version: 1.5.1
Author: Hugo Moran
Author URI: http://tipit.net
License: GPL2+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_plugin_color_scheme') ) :

class acf_plugin_color_scheme {

	// vars
	var $settings;

	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	void
	*  @return	void
	*/

	function __construct() {

		// settings
		// - these will be passed into the field class.
		$this->settings = array(
			'version'	=> '1.0.0',
			'url'		=> plugin_dir_url( __FILE__ ),
			'path'		=> plugin_dir_path( __FILE__ )
		);


		// include field
		add_action('acf/include_field_types', 	array($this, 'include_field')); // v5
		add_action('acf/register_fields', 		array($this, 'include_field')); // v4


	}


	/*
	*  include_field
	*
	*  This function will include the field type class
	*
	*  @type	function
	*  @date	17/02/2016
	*  @since	1.0.0
	*
	*  @param	$version (int) major ACF version. Defaults to false
	*  @return	void
	*/

	function include_field( $version = false ) {

		// support empty $version
		if( !$version ) $version = 4;


		// load textdomain
		load_plugin_textdomain( 'TEXTDOMAIN', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );


		// include
		include_once('fields/class-acf-field-color_scheme-v' . $version . '.php');
	}



}

// initialize
new acf_plugin_color_scheme();

// adds css to admin panel.
function acfcs_css() {
	wp_register_style('acfcs_css', plugins_url('/assets/css/style.css',__FILE__ ));
	wp_enqueue_style('acfcs_css');
}
add_action( 'admin_init','acfcs_css');

// class_exists check
endif;

?>
