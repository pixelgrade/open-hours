<?php

class Pix_Open_Shortcodes {

	public function __construct() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'Helper/class-Pix_Open_Helper.php';
	}

	/**
	 * ['open-overview-shortcode'] shortcodes
	 */
	function add_open_overview_shortcodes( $atts, $content = null ) {
		$overview_option = get_option( 'open_hours_overview_setting' );
		$helper          = new Pix_Open_Helper();

		if ( ! $overview_option ) {
			return $atts;
		}

		$a = shortcode_atts(
			array(
				'title'        => isset( $atts['title'] ) ? $atts['title'] : '',
				'time_format'  => isset( $atts['time_format'] ) ? $atts['time_format'] : '',
				'closed_label' => isset( $atts['closed_label'] ) ? $atts['closed_label'] : ''
			),
			$atts
		);

		// Parse the option to an array of days and open hours
		$schedule = $helper->parse_open_hours( $overview_option, $atts['time_format'], $atts['closed_label'] );

		ob_start();

		if ( ! empty( $a['title'] ) ) {
			echo $a['title'];
		}

		if ( $schedule ) {
			// Display the schedule
			?>
			<table class="open_overview_shortcode">
				<?php
				foreach ( $schedule as $day => $hours ) {
				?>
				<tr>
					<td>
						<div class=<?php echo '-days-'; ?>><?php echo $day; ?></div>
					</td>
					<?php
					if ( $hours === $atts['closed_label'] ) {
						?>
						<td>
							<div class="open-hours-closed"
							     id=<?php echo '-hours-'; ?>><?php echo $hours; ?></div>
						</td>
						<?php
					} else {
						?>
						<td>
							<div id=<?php echo 'sdsa'; ?>><?php echo $hours; ?></div>
						</td>
						<?php
					}
					}
					?>
				</tr>
			</table>
			<?php
		} else {
			?>
			<p>You haven't setup a schedule yet.</p>
			<?php
		}

		return ob_get_clean();
	}

	/**
	 * ['open-time-shortcode'] shortcodes
	 */
	function add_open_time_shortcode( $atts ) {
		$a = shortcode_atts(
			array(
				'value' => isset( $atts['value'] ) ? $atts['value'] : '',
			),
			$atts
		);

		if ( ! isset( $atts['value'] ) ) {
			return false;
		}

		$helper = new Pix_Open_Helper();

		$response = $helper->get_shortcode_time( $atts['value'] );

		return $response;
	}

	/**
	 * ['open-current-status'] shortcodes
	 */
	function add_current_status_shortcode( $atts ) {
		$a = shortcode_atts(
			array(
				'open_note'  => isset( $atts['open_note'] ) ? $atts['open_note'] : '',
				'closed_note' => isset( $atts['closed_note'] ) ? $atts['closed_note'] : '',
			),
			$atts
		);

		$open_note  = $this->_replace_strings( $a['open_note'] );
		$closed_note = $this->_replace_strings( $a['closed_note'] );

		$open_note_id  = isset( $atts['open_note_id'] ) ? $atts['open_note_id'] : '';
		$close_note_id = isset( $atts['close_note_id'] ) ? $atts['close_note_id'] : '';

		ob_start();
		?>
		<div id=<?php echo $open_note_id ?>><?php echo __( $open_note, 'open_hours' ); ?></div>
		<div id=<?php echo $close_note_id ?>><?php echo __( $closed_note, 'text_domain' ); ?></div>
		<?php

		return ob_get_clean();
	}

	/**
	 * @param $string
	 *
	 * Parse a string that contains replacement tags
	 */
	function _replace_strings( $string ) {
		preg_match_all( '/\{(.*?)\}/', $string, $matches );
		$helper = new Pix_Open_Helper();

		if ( empty( $matches ) ) {
			// No match found, carry on with the same string
			return $string;
		}
		for ( $i = 0; $i < count( $matches[0] ); $i ++ ) {
			$string = str_replace( $matches[0][ $i ], $helper->get_shortcode_time( $matches[1][ $i ] ), $string );
		}

		return $string;
	}
}