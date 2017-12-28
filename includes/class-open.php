<?php

class OpenPlugin {

	protected static $_instance = null; //hold our instance

	/**
	 * The plugin version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.4
	 */
	public $_version;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.4
	 */
	public $file;

	public $plugin_basepath = null;
	public $plugin_baseuri = null;

	/**
	 * Shortcodes class object
	 * @var Pix_Open_Shortcodes
	 * @access  public
	 * @since   1.0.4
	 */
	public $shortcodes = null;

	protected $base_url = null;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;


	private $widget_ids = array();

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 *
	 * @param string $file
	 * @param string $version
	 */
	public function __construct( $file, $version = '1.0.0' ) {

		//the main plugin file (the one that loads all this)
		$this->file = $file;
		//the current plugin version
		$this->_version = $version;

		//setup the helper variables for easily retrieving PATHS and URLS everywhere (these are already trailingslashit)
		$this->plugin_basepath = plugin_dir_path( $file );
		$this->plugin_baseuri  = plugin_dir_url( $file );
		$this->base_url  = home_url();

		$this->plugin_name = 'open';

		$this->define_hooks();
	}

	public function define_hooks() {
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_control_scripts' ) );

		add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_preview_scripts' ), 99999 );
		add_action( 'customize_register', array( $this, 'register_opening_hours_main_section' ), 11 );

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_styles' ) );

		// Add this as shortcode
		require_once $this->plugin_basepath . 'includes/Shortcode/class-Pix_Open_Shortcodes.php';
		if ( is_null( $this->shortcodes ) ) {
			$this->shortcodes = new Pix_Open_Shortcodes( $this );
		}

		add_shortcode( 'open-hours-overview', array( $this->shortcodes, 'add_open_overview_shortcodes' ) );
		add_shortcode( 'open-time-shortcode', array( $this->shortcodes, 'add_open_time_shortcode' ) );
		add_shortcode( 'open-hours-current-status', array( $this->shortcodes, 'add_current_status_shortcode' ) );

	}

	/**
	 * Enqueue control scripts
	 */
	public function enqueue_customizer_control_scripts() {
		if ( $this->is_customizer_control() ) {
			wp_enqueue_script( 'open-customizer-control', $this->plugin_baseuri . 'js/open-customizer-control.js', array(
				'jquery',
				'wp-util'
			), $this->_version, true );
			wp_enqueue_script( 'hour-parser', $this->plugin_baseuri . 'js/HoursParser.js' );
		}

		wp_enqueue_script( 'open-customizer-select2', $this->plugin_baseuri . 'js/jquery.autocomplete.min.js', array(
			'jquery',
		), $this->_version, true );

		// Load only if we are on the widgets page or in the customizer
		wp_enqueue_script( 'open-select-autocomplete', $this->plugin_baseuri . 'js/open-select-autocomplete.js' );

		setlocale( LC_ALL, get_locale() );
		$this->localize_control_js_data();

		wp_enqueue_style( $this->plugin_name, $this->plugin_baseuri . 'css/open.css', array(), $this->_version, 'all' );
	}

	/**
	 * Enqueue live preview scripts
	 */
	public function enqueue_customizer_preview_scripts() {
		wp_enqueue_script( 'open-customizer-preview', $this->plugin_baseuri . 'js/open-customizer-preview.js', array(
			'jquery',
			'wp-util'
		), $this->_version, true );
		wp_enqueue_script( 'hour-parser', $this->plugin_baseuri. 'js/HoursParser.js' );

		$this->localize_preview_js_data();
	}

	/**
	 * Register the stylesheets for the admin area, only on the widgets edit page.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_admin_scripts_styles( $hook ) {
		if ( 'widgets.php' != $hook ) {
			return;
		}

		wp_enqueue_style( $this->plugin_name, $this->plugin_baseuri . 'css/open.css', array(), $this->_version, 'all' );

		wp_enqueue_script( 'open-customizer-select2', $this->plugin_baseuri . 'js/jquery.autocomplete.min.js', array(
			'jquery',
		), $this->_version, true );

		// Load only if we are on the widgets page or in the customizer
		wp_enqueue_script( 'open-select-autocomplete', $this->plugin_baseuri . 'js/open-select-autocomplete.js' );

		setlocale( LC_ALL, get_locale() );
		$this->localize_control_js_data();
	}

	/**
	 * @param $wp_customize
	 * Register the Overview section
	 */
	function register_opening_hours_main_section( $wp_customize ) {
		require_once $this->plugin_basepath . 'includes/Control/class-Pix_Open_Customize_Overview_Control.php';
		require_once $this->plugin_basepath . 'includes/Control/class-Pix_Open_Customize_Textarea_Control.php';

		// Add our section to the customizer
		$wp_customize->add_section( 'open_hours_overview_section', array(
			'title'    => __( '🕒 &nbsp;Open Hours', 'open_hours' ),
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
				'description' => __( 'Write your opening hours in a simple human readable format:' )
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
		require_once $this->plugin_basepath . 'includes/Widget/open-current-status-widget.php';
		register_widget( 'OpenCurrentStatus_Widget' );
		require_once $this->plugin_basepath . 'includes/Widget/open-overview-widget.php';
		register_widget( 'OpenOverview_Widget' );
	}

	/**
	 * @param string $key
	 * Localize js
	 */
	function localize_preview_js_data( $key = 'open-customizer-preview' ) {
		$this->_create_customizer_widget_ids( 'OpenCurrentStatus_Widget' );
		$this->_create_customizer_widget_ids( 'OpenOverview_Widget' );

		$localized_data = array(
			'widget_ids' => $this->widget_ids,
			'wp_rest'    => array(
				'root'       => esc_url_raw( rest_url() ),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'open_nonce' => wp_create_nonce( 'open_rest' )
			),
		);

		wp_localize_script( $key, 'open_hours', $localized_data );
	}

	/**
	 * @param string $key
	 * Localize js
	 */
	function localize_control_js_data( $key = 'open-customizer-control' ) {
		$localized_data = array(
			'wp_rest' => array(
				'root'       => esc_url_raw( rest_url() ),
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'open_nonce' => wp_create_nonce( 'open_rest' )
			),
		);

		wp_localize_script( $key, 'open_hours_control', $localized_data );
	}

	/**
	 * A helper function that takes a widget's Class Name and returns the Customizer ID for that widget's settings
	 *
	 * @param $widget_class
	 * @param $wp_customize
	 *
	 */
	function _create_customizer_widget_ids( $widget_class ) {
		global $wp_customize;

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
			$widget_id     = 'open_' . $widget_type . '_widget-' . $i;
			$customizer_id = $manager->get_setting_id( $widget_id );
			// push to localized widget_ids array
			array_push( $this->widget_ids, $widget_id );

			$widget_settings = $wp_customize->get_setting( $customizer_id );

			if ( $widget_settings ) {
//				$wp_customize->get_setting( $customizer_id )->transport = 'postMessage';
			}
		}
	}

	function is_customizer_control() {
		global $wp_customize;

		if ( $wp_customize ) {
			return true;
		}

		return false;
	}

	/**
	 * Main OpenPlugin Instance
	 *
	 * Ensures only one instance of OpenPlugin is loaded or can be loaded.
	 *
	 * @since  1.0.0
	 * @static
	 * @param string $file    File.
	 * @param string $version Version.
	 *
	 * @see    OpenPlugin()
	 * @return object Main OpenPlugin instance
	 */
	public static function instance( $file = '', $version = '1.0.5' ) {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {

		_doing_it_wrong( __FUNCTION__,esc_html( __( 'Cheatin&#8217; huh?' ) ), esc_html( $this->_version ) );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {

		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cheatin&#8217; huh?' ) ),  esc_html( $this->_version ) );
	} // End __wakeup ()


}