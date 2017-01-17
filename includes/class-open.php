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

	private $widget_ids = array();
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
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_control_scripts' ) );
		add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_preview_scripts' ), 99999 );

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'customize_register', array( $this, 'open_customizer_register' ) );
		add_action( 'customize_register', array( $this, 'register_opening_hours_main_section' ), 11 );
	}

	/**
	 * Enqueue control scripts
	 */
	public function enqueue_customizer_control_scripts() {

		wp_enqueue_script( 'open-customizer-control', plugin_dir_url( __FILE__ ) . 'js/open-customizer-control.js', array(
			'jquery',
			'wp-util'
		), $this->plugin_version, true );
		wp_enqueue_script( 'hour-parser', plugin_dir_url( __FILE__ ) . 'js/HoursParser.js' );
	}

	/**
	 * Enqueue live preview scripts
	 */
	public function enqueue_customizer_preview_scripts() {
		wp_enqueue_script( 'open-customizer-preview', plugin_dir_url( __FILE__ ) . 'js/open-customizer-preview.js', array(
			'jquery',
			'wp-util'
		), $this->plugin_version, true );
		wp_enqueue_script( 'hour-parser', plugin_dir_url( __FILE__ ) . 'js/HoursParser.js' );

		$this->localize_js_data();
	}

	/**
	 * @param $wp_customize
	 * Register the Overview section
	 */
	function register_opening_hours_main_section( $wp_customize ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Control/class-Pix_Open_Customize_Overview_Control.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Control/class-Pix_Open_Customize_Textarea_Control.php';

		// Add our section to the customizer
		$wp_customize->add_section( 'open_hours_overview_section', array(
			'title'    => __( 'Opening Hours', 'open_hours' ),
			'priority' => 9999,
		) );

		$wp_customize->add_setting( 'open_hours_overview_setting', array(
			'transport' => 'postMessage',
			'type'      => 'option'
		) );

		$wp_customize->add_setting( 'open_hours_overview-dummy', array(
			'transport' => 'postMessage',
			'type'      => 'option'
		) );

		$overview_textarea_control = new Pix_Open_Customize_Textarea_Control(
			$wp_customize,
			'open_hours_overview_setting',
			array(
				'section'     => 'open_hours_overview_section',
				'description' => __( 'something something in the month of may 1' )
			)
		);

		$wp_customize->add_control( $overview_textarea_control );

		// Add control for Overview description
		$wp_customize->add_setting( 'open_hours_overview_description', array(
			'transport' => 'postMessage',
			'type'      => 'option'
		) );

		$overview_description_control = new Pix_Open_Customize_Overview_Control(
			$wp_customize,
			'open_hours_overview_description',
			array(
				'label'       => __( 'Description', 'open-hours' ),
				'section'     => 'open_hours_overview_section',
				'description' => __( 'something something in the month of may 2' )
			)
		);

		$wp_customize->add_control( $overview_description_control );
	}

	/**
	 * Register the Open Current Status Widget
	 */

	public function register_widgets() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Widget/open-current-status-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/Widget/open-overview-widget.php';

		OpenCurrentStatus_Widget::registerWidget();
		OpenOverview_Widget::registerWidget();
	}


	function open_customizer_register( WP_Customize_Manager $wp_customize ) {
		// Change transport for this widget to postMessage
		$this->_set_post_message_transport( 'OpenCurrentStatus_Widget', $wp_customize );
		$this->_set_post_message_transport( 'OpenOverview_Widget', $wp_customize );
	}

	/**
	 * A helper function that takes a widget's Class Name and returns the Customizer ID for that widget's settings
	 *
	 * @param $widget_class
	 * @param $wp_customize
	 *
	 * @return string
	 */
	function _set_post_message_transport( $widget_class, $wp_customize ) {
		$current_widget = $GLOBALS['wp_widget_factory']->widgets[ $widget_class ];
		$manager        = new WP_Customize_Widgets( $wp_customize );

		$widget_number  = $current_widget->number;
		$widget_base_id = $current_widget->control_options['id_base'];

		switch ( $widget_class ) {
			case 'OpenCurrentStatus_Widget':
				$widget_type = 'current_status';
				break;
			case 'OpenOverview_Widget':
				$widget_type = 'overview';
				break;
			default:
				$widget_type = '';
		}

		// Set the transport to be postMessage
		for ( $i = 0; $i <= $widget_number; $i ++ ) {
			$widget_id       = 'open_' . $widget_type . '_widget-' . $i;
			$customizer_id   = $manager->get_setting_id( $widget_id );
			// push to localized widget_ids array
			array_push($this->widget_ids, $widget_id);

			$widget_settings = $wp_customize->get_setting( $customizer_id );

			if ( $widget_settings ) {
				$wp_customize->get_setting( $customizer_id )->transport = 'postMessage';
			}
		}
	}

	/**
	 * @param string $key
	 * Localize js
	 */
	function localize_js_data( $key = 'open-customizer-preview' ) {

		$localized_data = array(
			'widget_ids'     => $this->widget_ids
		);

		wp_localize_script( $key, 'open_hours', $localized_data );
	}
}