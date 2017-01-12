<?php

class OpenPlugin {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $plugin_version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'open';
		$this->version     = '1.0.0';

		$this->define_hooks();
	}

	public function define_hooks() {
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_preview_scripts' ) );

		add_action( 'widgets_init', array( $this, 'register_open_current_status_widget' ) );
		add_action( 'customize_register', array( $this, 'mytheme_customize_register' ) );

	}

	public function enqueue_preview_scripts() {

		wp_enqueue_script( 'open-customizer-preview', plugin_dir_url( __FILE__ ) . 'js/open-customizer-preview.js', array(
			'jquery',
			'wp-util'
		), $this->plugin_version, true );
	}


	/**
	 * Register the Open Current Status Widget
	 */

	public function register_open_current_status_widget() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Widget/open-current-status-widget.php';

		register_widget( 'OpenCurrentStatus_Widget' );
	}

	function mytheme_customize_register( WP_Customize_Manager $wp_customize ) {
//		var_dump(get_option('widget_open_current_status_widget'));
//		var_dump($GLOBALS['wp_widget_factory']->widgets['OpenCurrentStatus_Widget']);
		$widgetId= 'open_current_status_widget-2';
		$manager                      = new WP_Customize_Widgets( $wp_customize );
		$current_status_customizer_id = $manager->get_setting_id( $widgetId );

		$current_status_setting = $wp_customize->get_setting( $current_status_customizer_id );

		if ( $current_status_setting ) {
			$wp_customize->get_setting( $current_status_customizer_id )->transport = 'postMessage';
		}

		$value = array('hello'=>'there', 'is_widget_customizer_js_value'=>true);

//		$manager->sanitize_widget_js_instance($value);
		//		var_dump();
//		var_dump($wp_customize->get_setting( 'widget_open_current_status_widget[2]' )->transport);

	}


}