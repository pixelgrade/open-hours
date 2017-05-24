<?php

// Make sure the base widget class is loaded
if ( ! class_exists( 'OpenAbstract_Widget' ) ) {
	require_once( 'abstract-widget.php' );
}

if ( ! class_exists( 'OpenOverview_Widget' ) ) :

class OpenOverview_Widget extends OpenAbstract_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$title       = esc_html__( 'Open Hours: Overview', 'open_hours' );
		$description = esc_html__( 'Open Hours: Overview', 'open_hours' );

		parent::__construct( 'open_overview_widget', $title, $description );

		add_action( 'rest_api_init', array( $this, 'add_rest_routes_api' ) );
	}

	function add_rest_routes_api() {
		//The Following registers an api route with multiple parameters.
		register_rest_route( 'open_hours/v1', '/get_schedule_content', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_schedule_content' ),
			'permission_callback' => array( $this, 'permission_nonce_callback' )
		) );
	}

	function get_schedule_content( $request ) {
		$params    = $request->get_params();
		$shortcode = '';

		if ( ! isset( $params['values'] ) && ! isset( $params['overview_option'] ) ) {
			//exit
			wp_send_json_error( 'No data sent!' );
		}

		if ( isset( $params['overview_option'] ) && ! empty( $params['overview_option'] ) ) {
			$shortcode = do_shortcode( '[open-hours-overview ' . 'overview_option=' . base64_encode( $params['overview_option'] ) . ']' );
		} elseif ( isset( $params['values'] ) ) {
			$time_format  = $params['values']['time_format'];
			$closed_label = $params['values']['closed_label'];

			$shortcode = do_shortcode( '[open-hours-overview ' . 'time_format=' . '"' . $time_format . '"' . ' ' . 'closed_label=' . '"' . $closed_label . '"' . ']' );
		}

		wp_send_json( $shortcode );
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

	protected function registerFields() {
		// Fields
		$this->addField( 'title', array(
			'type'    => 'text',
			'caption' => __( 'Title', 'open_hours' )
		) );


		$this->addField( 'compress_opening_hours', array(
			'type'    => 'checkbox',
			'caption' => __( 'Compress Opening Hours', 'open_hours' )
		) );

		$this->addField( 'hide_closed_days', array(
			'type'    => 'checkbox',
			'caption' => __( 'Hide Closed Days', 'open_hours' )
		) );

		$this->addField( 'closed_label', array(
			'type'      => 'text',
			'css_class' => 'js-time-autocomplete',
			'caption'   => __( 'Closed Label', 'open_hours' ),
			'default'   => __( 'Closed' )
		) );

		$this->addField( 'time_format', array(
			'type'    => 'text',
			'caption' => __( 'Time Format', 'open_hours' ),
			'default' => __( 'g:i a' )
		) );

		$this->addField( 'time_format_foot', array(
			'type'    => 'description',
			'caption' => __( 'TimeFormat Footnote', 'open_hours' ),
			'notes'   => array(
				'header' => __( '<a href="#" class="js-show-hours-scheme">Learn more about time formatting</a>' ),
				'footer' => ''
			)
		) );

		$this->addField( 'short_day_name', array(
			'type'    => 'checkbox',
			'caption' => __( 'Use Short Day Name', 'open_hours' )
		) );
	}

	// @TODO Change the output to use shortcodes
	protected function widget_content( $args, $instance ) {
		$open_hours = get_option( 'open_hours_overview_setting' );

		// Parse the hours from the option json using the Helper class
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'Helper/class-Pix_Open_Helper.php';
		$helper = new Pix_Open_Helper();

		// First time - set some defaults
		if ( empty( $instance ) ) {
			$instance = $this->update( $instance, array() );
		}

		if ( isset( $instance['short_day_name'] ) && $instance['short_day_name'] == 1 ) {
			$use_short_days = true;
		} else {
			$use_short_days = false;
		}

		if ( isset( $instance['compress_opening_hours'] ) && $instance['compress_opening_hours'] == 1 ) {
			$compress_hours = true;
		} else {
			$compress_hours = false;
		}

		if ( isset( $instance['hide_closed_days'] ) && $instance['hide_closed_days'] == 1 ) {
			$hide_closed_days = true;
		} else {
			$hide_closed_days = false;
		}

		$schedule = $helper->parse_open_hours( $open_hours, $instance['time_format'], $instance['closed_label'], $use_short_days, $compress_hours, $hide_closed_days );

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
				</tr>
			</table>
			<?php
		} else {
			?><p><?php echo __('You haven\'t setup a schedule yet.', 'open_hours')?></p><?php
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
		$instance['title']                  = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : 'Opening Hours';
		$instance['compress_opening_hours'] = ( ! empty( $new_instance['compress_opening_hours'] ) ) ? '1' : '0';
		$instance['hide_closed_days']       = ( ! empty( $new_instance['hide_closed_days'] ) ) ? '1' : '0';
		$instance['closed_label']           = ( ! empty( $new_instance['closed_label'] ) ) ? wp_strip_all_tags( $new_instance['closed_label'] ) : 'Closed';
		$instance['time_format']            = ( ! empty( $new_instance['time_format'] ) ) ? wp_strip_all_tags( $new_instance['time_format'] ) : 'g:i a';
		$instance['short_day_name']         = ( ! empty( $new_instance['short_day_name'] ) ) ? '1' : '0';
		$instance['widget_id']              = $this->getWidgetId();

		return $instance;
	}
}

endif;