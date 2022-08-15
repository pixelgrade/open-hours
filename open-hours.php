<?php
/*
Plugin Name: Open Hours
Plugin URI: https://pixelgrade.com
Description: An easy to use opening hours WordPress plugin manager for any kind of venue.
Author: pixelgrade
Version: 1.0.9
Author URI: https://pixelgrade.com
Text Domain: open-hours
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function OpenPlugin() {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-open.php' );
	$instance = OpenPlugin::instance( __FILE__, '1.0.9' );

	return $instance;
}
$open_plugin = OpenPlugin();
