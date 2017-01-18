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
		$schedule   = $this->_parse_open_hours( $open_hours, $instance['time_format'], $instance['closed_label'] );

		$open_note  = $args['widget_id'] . '-openNote';
		$close_note = $args['widget_id'] . '-closeNote';

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// Display the schedule
		?>
		<div class="open_overview_widget-schedule" >
		<?php
		foreach ( $schedule as $day => $hours ) {
			?>
			<div id=<?php echo $args['widget_id'] . '-days-' . $day; ?>><?php echo $day; ?></div>
			<div id=<?php echo $args['widget_id'] . '-hours-' . $day?>><?php echo $hours; ?></div><br/>
			<?php
		}
		?>
		</div>
		<?php

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

	/**
	 * A helper function that takes in a raw JSON of timeframes and returns an array of human readable schedule
	 */
	function _parse_open_hours( $hours, $hours_format = null, $closed_label = 'Closed' ) {
		$schedule = array(
			'monday'    => $closed_label,
			'tuesday'   => $closed_label,
			'wednesday' => $closed_label,
			'thursday'  => $closed_label,
			'friday'    => $closed_label,
			'saturday'  => $closed_label,
			'sunday'    => $closed_label,
		);

		$hours_json = json_decode( $hours, true );
		if ( ! isset( $hours_json['timeframes'] ) ) {
			return false;
		}

		foreach ( $hours_json['timeframes'] as $timeframe ) {
			foreach ( $timeframe['days'] as $day ) {
				$start = $this->_parse_hours( preg_replace( '/^\+/', '', $timeframe['open'][0]['start'] ), $hours_format );
				$end   = $this->_parse_hours( preg_replace( '/^\+/', '', $timeframe['open'][0]['end'] ), $hours_format );

				switch ( $day ) {
					case 1:
						$schedule['monday'] = $start . ' - ' . $end;
						break;
					case 2:
						$schedule['tuesday'] = $start . ' - ' . $end;
						break;
					case 3:
						$schedule['wednesday'] = $start . ' - ' . $end;
						break;
					case 4:
						$schedule['thursday'] = $start . ' - ' . $end;
						break;
					case 5:
						$schedule['friday'] = $start . ' - ' . $end;
						break;
					case 6:
						$schedule['saturday'] = $start . ' - ' . $end;
						break;
					case 7:
						$schedule['sunday'] = $start . ' - ' . $end;
						break;
					default:
						break;
				}
			}
		}

		return $schedule;
	}

	/**
	 * @param $hour
	 * @param null $format
	 *
	 * @return false|string
	 */
	function _parse_hours( $hour, $format = null ) {
		$timestamp = strtotime( $hour );
		$date      = gmdate( $format, $timestamp );

		return $date;
	}
}