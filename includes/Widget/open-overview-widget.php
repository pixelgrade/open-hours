<?php

require_once( plugin_dir_path( __FILE__ ) . 'abstract-widget.php' );

class OpenOverview_Widget extends OpenAbstract_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$title       = esc_html__( 'Opening Hours: Overview', 'text_domain' );
		$description = esc_html__( 'A Foo Widget', 'text_domain' );

		parent::__construct( 'open_overview_widget', $title, $description );

		add_action( 'rest_api_init', array( $this, 'add_rest_routes_api' ) );
	}

	function add_rest_routes_api() {
		//The Following registers an api route with multiple parameters.
		register_rest_route( 'open_hours/v1', '/get_schedule_content', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'get_schedule_content' ),
//			'permission_callback' => array( $this, 'permission_nonce_callback' )
		) );
	}

	function get_schedule_content( $request ) {
		$params = $request->get_body_params();

		if ( ! isset( $params['values'] ) ) {
			//exit
			wp_send_json_error( 'damn' );
		}

		$time_format  = $params['values']['time_format'];
		$closed_label = $params['values']['closed_label'];

		$shortcode = do_shortcode( '[open-overview-shortcode ' . 'time_format=' . '"' . $time_format . '"' . ' ' . 'closed_label=' . '"' . $closed_label . '"' . ']' );

		wp_send_json( $shortcode );
	}

	protected function registerFields() {
		// Fields
		$this->addField( 'title', array(
			'type'    => 'text',
			'caption' => __( 'Title', 'text_domain' )
		) );

		$this->addField( 'compress_opening_hours', array(
			'type'    => 'checkbox',
			'caption' => __( 'Compress Opening Hours', 'text_domain' )
		) );

		$this->addField( 'hide_closed_days', array(
			'type'    => 'checkbox',
			'caption' => __( 'Hide Closed Days', 'text_domain' )
		) );

		$this->addField( 'closed_label', array(
			'type'    => 'text',
			'caption' => __( 'Closed Label', 'text_domain' )
		) );

		$this->addField( 'time_format', array(
			'type'    => 'text',
			'caption' => __( 'Time Format', 'text_domain' )
		) );

		$this->addField( 'short_day_name', array(
			'type'    => 'checkbox',
			'caption' => __( 'Use Short Day Name', 'text_domain' )
		) );
	}

	// @TODO Change the output to use shortcodes
	protected function widget_content( $args, $instance ) {
		$open_hours = get_option( 'open_hours_overview_setting' );

		// Parse the hours from the option json using the Helper class
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'Helper/class-Pix_Open_Helper.php';
		$helper = new Pix_Open_Helper();

		$schedule = $helper->parse_open_hours( $open_hours, $instance['time_format'], $instance['closed_label'] );

		$open_note  = $args['widget_id'] . '-openNote';
		$close_note = $args['widget_id'] . '-closeNote';

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		if ( $schedule ) {
			// Display the schedule
			?>
			<table class="open_overview_widget-schedule">
				<?php
				foreach ( $schedule as $day => $hours ) {
				?>
				<tr>
					<td>
						<div id=<?php echo $args['widget_id'] . '-days-' . $day; ?>><?php echo $day; ?></div>
					</td>
					<?php
					if ( $hours === $instance['closed_label'] ) {
						?>
						<td>
							<div class="open-hours-closed"
							     id=<?php echo $args['widget_id'] . '-hours-' . $day; ?>><?php echo $hours; ?></div>
						</td>
						<?php
					} else {
						?>
						<td>
							<div id=<?php echo $args['widget_id'] . '-hours-' . $day; ?>><?php echo $hours; ?></div>
						</td>
						<?php
					}
					}
					?>
					</td>
			</table>
			<?php
		} else {
			?>
			<p>You haven't setup a schedule yet.</p>
			<?php
		}
		echo $args['after_widget'];
	}


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                           = array();
		$instance['title']                  = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['compress_opening_hours'] = ( ! empty( $new_instance['compress_opening_hours'] ) ) ? strip_tags( $new_instance['compress_opening_hours'] ) : '';
		$instance['hide_closed_days']       = ( ! empty( $new_instance['hide_closed_days'] ) ) ? strip_tags( $new_instance['hide_closed_days'] ) : '';
		$instance['closed_label']           = ( ! empty( $new_instance['closed_label'] ) ) ? strip_tags( $new_instance['closed_label'] ) : '';
		$instance['time_format']            = ( ! empty( $new_instance['time_format'] ) ) ? strip_tags( $new_instance['time_format'] ) : '';
		$instance['short_day_name']         = ( ! empty( $new_instance['short_day_name'] ) ) ? strip_tags( $new_instance['short_day_name'] ) : '';
		$instance['widget_id']              = $this->getWidgetId();

		return $instance;
	}
}