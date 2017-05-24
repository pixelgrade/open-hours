<?php
/*
Plugin Name: Open Hours
Plugin URI: https://pixelgrade.com
Description: An easy to use opening hours WordPress plugin manager for any kind of venue.
Author: pixelgrade
Version: 1.0.4
Author URI: https://pixelgrade.com
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function OpenPlugin() {
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-open.php' );
	$instance = new OpenPlugin( __FILE__, '1.0.4' );

	return $instance;
}

global $open_plugin;

$open_plugin = OpenPlugin();
