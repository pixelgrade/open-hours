<?php

/**
 * @package Open
 * @version 1.0
 */

/*
Plugin Name: Open
Plugin URI: https://pixelgrade.com
Description: A plugin where your site's user can subscribe to receive newsletters.
Author: pixelgrade
Version: 1.0
Author URI: https://pixelgrade.com
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function OpenPlugin() {
	require_once( plugin_dir_path( __FILE__ ) . '/includes/class-open.php' );
	$instance = new OpenPlugin( __FILE__, '1.0.0' );

	return $instance;
}

global $open_plugin;

$open_plugin = OpenPlugin();

/**
 * Ensures that this plugin is loaded after WP REST API plugin.
 *
 * Once WP_REST_Controller is in core this will not be necessary and will be
 * possibly removed or kept for backwards compatability.
 *
 * @see add_action( 'activated_plugin' ) && add_action( 'deactivated_plugin' ) Hooked into both.
 */
function load_my_plugin_last() {
	// Ensure path to this file is via main wp plugin path.
	$wp_path_to_this_file = preg_replace( '/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR . '/$2', __FILE__ );
	$this_plugin          = plugin_basename( trim( $wp_path_to_this_file ) );
	$active_plugins       = get_option( 'active_plugins' );
	$this_plugin_key      = array_search( $this_plugin, $active_plugins );

	if ( in_array( $this_plugin, $active_plugins ) && end( $active_plugins ) !== $this_plugin ) {
		array_splice( $active_plugins, $this_plugin_key, 1 );
		array_push( $active_plugins, $this_plugin );
		update_option( 'active_plugins', $active_plugins );

	}
}

add_action( 'activated_plugin', 'load_my_plugin_last' );
add_action( 'deactivated_plugin', 'load_my_plugin_last' );