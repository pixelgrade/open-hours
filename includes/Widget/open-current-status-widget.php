<?php

require_once( plugin_dir_path( __FILE__ ) . 'abstract-widget.php' );

class OpenCurrentStatus_Widget extends OpenAbstract_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$title       = esc_html__( 'Open: Current Status A wonderful widget', 'text_domain' );
		$description = esc_html__( 'A Foo Widget', 'text_domain' );

		parent::__construct( 'open_current_status_widget', $title, $description );

		add_action( 'rest_api_init', array( $this, 'add_rest_routes_api' ) );
	}

	function add_rest_routes_api() {
		//The Following registers an api route with multiple parameters.
		register_rest_route( 'open_hours/v1', '/get_time', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'get_time' ),
//			'permission_callback' => array( $this, 'permission_nonce_callback' )
		) );
	}

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
			'caption' => __( 'Title', 'text_domain' )
		) );

		$this->addField( 'title_foot', array(
			'type'    => 'description',
			'caption' => __( 'Open Note', 'text_domain' ),
			'notes'   => array(
				'header' => 'Write the "Open" and "Closed" messages using the tags displayed below.',
				'footer' => _( 'See <a href="#">available tags</a> scheme.' )
			)
		) );

		$this->addField( 'open_note', array(
			'type'    => 'text',
			'caption' => __( 'Open Note', 'text_domain' )
		) );

		$this->addField( 'open_note_foot', array(
			'type'    => 'description',
			'caption' => __( 'Open Note Foot Note', 'text_domain' ),
			'notes'   => array(
				'header' => 'It\'s {time} and we\'re Open until {today-end-time}',
				'footer' => '{time} - It\'s today, we\'re Open.'
			),
			'css'     => 'background-color:#F4F4F4;font-style:italic;'
		) );

		$this->addField( 'closed_note', array(
			'type'    => 'text',
			'caption' => __( 'Closed Note', 'text_domain' )
		) );

		$this->addField( 'closed_note_foot', array(
			'type'    => 'description',
			'caption' => __( 'Closed Note Footnote', 'text_domain' ),
			'notes'   => array(
				'header' => 'We\'re closed until {next-day} at {next-time}',
				'footer' => '{time} - it\'s closed now'
			),
			'css'     => 'background-color:#F4F4F4;font-style:italic;'
		) );


		$this->addField( 'time_format', array(
			'type'    => 'text',
			'caption' => __( 'Time Format', 'text_domain' )
		) );

		$this->addField( 'time_format_foot', array(
			'type'    => 'description',
			'caption' => __( 'TimeFormat Footnote', 'text_domain' ),
			'notes'   => array(
				'header' => _( '<a href="#">Learn more about time formatting</a>' ),
				'footer' => ''
			)
		) );

		$this->addField( 'use_short_day_name', array(
			'type'    => 'checkbox',
			'caption' => __( 'Use short day name', 'text_domain' )
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

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// Display the open Note
		echo do_shortcode( '[open-current-status ' . 'open_note_id=' . '"' . $open_note_id . '"' . ' close_note_id=' . '"' . $close_note_id . '"' . ' open_note=' . '"' . $instance['open_note'] . '"' . ' closed_note=' . '"' . $instance['closed_note'] . '"' . ' time_format=' . '"' . $instance['time_format'] . '"' . ']' );

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance                = array();
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['open_note']   = ( ! empty( $new_instance['open_note'] ) ) ? esc_attr( $new_instance['open_note'] ) : '';
		$instance['closed_note'] = ( ! empty( $new_instance['closed_note'] ) ) ? esc_attr( $new_instance['closed_note'] ) : '';
		$instance['time_format'] = ( ! empty( $new_instance['time_format'] ) ) ? strip_tags( $new_instance['time_format'] ) : '';
		$instance['use_short_day_name']         = ( ! empty( $new_instance['use_short_day_name'] ) ) ? '1' : '0';
		$instance['widget_id']   = $this->getWidgetId();

		return $instance;
	}

}