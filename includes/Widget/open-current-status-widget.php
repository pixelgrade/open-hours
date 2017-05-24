<?php

// Make sure the base widget class is loaded
if ( ! class_exists( 'OpenAbstract_Widget' ) ) {
	require_once( 'abstract-widget.php' );
}

if ( ! class_exists( 'OpenCurrentStatus_Widget' ) ) :

class OpenCurrentStatus_Widget extends OpenAbstract_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$title       = esc_html__( 'Open Hours: Current Status', 'open_hours' );
		$description = esc_html__( 'Open Hours: Current Status', 'open_hours' );

		parent::__construct( 'open_current_status_widget', $title, $description );

		add_action( 'rest_api_init', array( $this, 'add_rest_routes_api' ) );
	}

	function add_rest_routes_api() {
		//The Following registers an api route with multiple parameters.
		register_rest_route( 'open_hours/v1', '/get_time', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_time' ),
			'permission_callback' => array( $this, 'permission_nonce_callback' )
		) );
	}

	/**
	 * @return false|int
	 * Check the nonce
	 */
	function permission_nonce_callback() {
		$nonce = '';

		if ( isset( $_REQUEST['open_nonce'] ) ) {
			$nonce = $_REQUEST['open_nonce'];
		} elseif ( isset( $_POST['open_nonce'] ) ) {
			$nonce = $_POST['open_nonce'];
		}

		return wp_verify_nonce( $nonce, 'open_rest' );
	}

	/**
	 * @param $request
	 *
	 * @return WP_REST_Response
	 * An endpoint that returns the time
	 */
	function get_time( $request ) {
		$params = $request->get_body_params();

		if ( ! isset( $params['value'] ) ) {
			wp_send_json_error( 'No value!' );
		}

		$shortcode = do_shortcode( '[open-time-shortcode value=' . '"' . $params['value'] . '"' . ']' );

		return new WP_REST_Response( $shortcode );
	}

	protected function registerFields() {
		// Fields
		$this->addField( 'title', array(
			'type'    => 'text',
			'caption' => __( 'Title', 'open_hours' )
		) );

		$this->addField( 'title_foot', array(
			'type'    => 'description',
			'caption' => __( 'Title foot note', 'open_hours' ),
			'notes'   => array(
				'header' => 'Write the "Open" and "Closed" messages using the tags displayed below.',
				'footer' => __( 'See <a href="#" class="js-show-hours-scheme">available tags</a> scheme.' )
			)
		) );

		$this->addField( 'open_note', array(
			'type'      => 'text',
			'css_class' => 'js-time-autocomplete',
			'caption'   => __( 'Open Note', 'open_hours' ),
			'default'   => 'It\'s {time} and we\'re Open until {today-closing-time}'
		) );

		$this->addField( 'open_note_foot', array(
			'type'      => 'description',
			'caption'   => __( 'Open Note Foot Note', 'open_hours' ),
			'notes'     => array(
				'header' => 'It\'s {time} and we\'re Open until {today-closing-time}',
				'footer' => '{time} - It\'s today, we\'re Open.'
			),
			'css_class' => 'opening-hours-example',

		) );

		$this->addField( 'closed_note', array(
			'type'      => 'text',
			'css_class' => 'js-time-autocomplete',
			'caption'   => __( 'Closed Note', 'open_hours' ),
			'default'   => 'We\'re closed until {next-opening-day} at {next-opening-time}'
		) );

		$this->addField( 'closed_note_foot', array(
			'type'      => 'description',
			'caption'   => __( 'Closed Note Footnote', 'open_hours' ),
			'notes'     => array(
				'header' => 'We\'re closed until {next-opening-day} at {next-opening-time}',
				'footer' => '{time} - it\'s closed now'
			),
			'css_class' => 'opening-hours-example',
		) );


		$this->addField( 'time_format', array(
			'type'    => 'text',
			'caption' => __( 'Time Format', 'open_hours' )
		) );

		$this->addField( 'time_format_foot', array(
			'type'    => 'description',
			'caption' => __( 'TimeFormat Footnote', 'open_hours' ),
			'notes'   => array(
				'header' => __( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">Learn more about time formatting</a>' ),
				'footer' => ''
			)
		) );
	}

	/**
	 * @param $args
	 * @param $instance
	 *
	 * The widget content
	 */
	protected function widget_content( $args, $instance ) {
		$open_note_id  = $args['widget_id'] . '-openNote';
		$close_note_id = $args['widget_id'] . '-closeNote';

		// First time - set some defaults
		if ( empty( $instance ) ) {
			$instance = $this->update( $instance, array() );
		}

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// Display the open Note
		echo do_shortcode( '[open-hours-current-status ' . 'open_note_id=' . '"' . $open_note_id . '"' . ' close_note_id=' . '"' . $close_note_id . '"' . ' open_note=' . '"' . $instance['open_note'] . '"' . ' closed_note=' . '"' . $instance['closed_note'] . '"' . ' time_format=' . '"' . $instance['time_format'] . '"' . ']' );

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance                = array();
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['open_note']   = ( ! empty( $new_instance['open_note'] ) ) ? wp_strip_all_tags( $new_instance['open_note'] ) : 'It\'s {time} and we\'re Open until {today-closing-time}';
		$instance['closed_note'] = ( ! empty( $new_instance['closed_note'] ) ) ? wp_strip_all_tags( $new_instance['closed_note'] ) : 'We\'re closed until {next-opening-day} at {next-opening-time}';
		$instance['time_format'] = ( ! empty( $new_instance['time_format'] ) ) ? wp_strip_all_tags( $new_instance['time_format'] ) : 'g:i a';
		$instance['widget_id']   = $this->getWidgetId();

		return $instance;
	}

}

endif;
