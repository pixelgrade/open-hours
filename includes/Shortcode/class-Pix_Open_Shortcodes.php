<?php

class Pix_Open_Shortcodes {

	/**
	 * The main plugin object (the parent).
	 * @var     OpenPlugin
	 * @access  public
	 * @since     1.0.0
	 */
	public $parent = null;

	public function __construct( $parent ) {
		$this->parent = $parent;

		require_once $this->parent->plugin_basepath . 'includes/Helper/class-Pix_Open_Helper.php';
	}

	/**
	 * [open-hours-overview] shortcodes
	 */
	function add_open_overview_shortcodes( $atts, $content = null ) {
		$overview_option = get_option( 'open_hours_overview_setting' );
		$helper          = new Pix_Open_Helper();

		if ( isset( $atts['overview_option'] ) && ! empty( $atts['overview_option'] ) ) {
			$overview_option = base64_decode($atts['overview_option']);
		}

		if ( ! $overview_option ) {
			return $atts;
		}

		$a = shortcode_atts(
			array(
				'title'        => isset( $atts['title'] ) ? $atts['title'] : '',
				'time_format'  => isset( $atts['time_format'] ) ? $atts['time_format'] : 'g:i a',
				'closed_label' => isset( $atts['closed_label'] ) ? $atts['closed_label'] : 'Closed'
			),
			$atts
		);

		// Parse the option to an array of days and open hours
		$schedule = $helper->parse_open_hours( $overview_option, $a['time_format'], $a['closed_label'] );

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
				<tr class="open-entry">
					<td>
						<span class="open-entry__day"><?php echo $day; ?></span>
					</td>
					<td>
						<?php
						if ( $hours === $a['closed_label'] ) { ?>
							<span class="open-entry__hours-closed"><?php echo $hours; ?></span>
							<?php
						} else { ?>
							<span class="open-entry__hours-schedule"><?php echo $hours; ?></span>
							<?php
						} ?>
					</td>
				</tr>
				<?php }
				?>
			</table>
			<?php
		} else {
			?><p><?php echo __('You haven\'t setup a schedule yet.', 'open_hours')?></p><?php
		}

		return ob_get_clean();
	}

	/**
	 * [open-time-shortcode] shortcodes
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
	 * ['open-hours-current-status'] shortcodes
	 */
	function add_current_status_shortcode( $atts ) {
		$helper          = new Pix_Open_Helper();
		$helper->is_open();

		$a = shortcode_atts(
			array(
				'open_note'   => isset( $atts['open_note'] ) ? $atts['open_note'] : '',
				'closed_note' => isset( $atts['closed_note'] ) ? $atts['closed_note'] : '',
				'time_format' => isset( $atts['time_format'] ) ? $atts['time_format'] : 'g : i A'
			),
			$atts
		);

		$open_note   = $this->_replace_strings( $a['open_note'], $a['time_format'] );
		$closed_note = $this->_replace_strings( $a['closed_note'], $a['time_format'] );

		$open_note_id  = isset( $atts['open_note_id'] ) ? $atts['open_note_id'] : '';
		$close_note_id = isset( $atts['close_note_id'] ) ? $atts['close_note_id'] : '';

		ob_start();
		?>
		<?php if ($helper->is_open()) { ?>
			<div id="<?php echo $open_note_id ?>" class="opening-hours-note  opening-hours-note--open"><?php echo esc_attr( $open_note ); ?></div>
		<?php } else { ?>
			<div id="<?php echo $close_note_id ?>" class="opening-hours-note  opening-hours-note--closed"><?php echo esc_attr( $closed_note ); ?></div>
			<?php
		}
		?>
		<?php

		return ob_get_clean();
	}

	/**
	 * @param $string
	 *
	 * Parse a string that contains replacement tags
	 */
	function _replace_strings( $string, $time_format = 'g : i A' ) {
		preg_match_all( '/\{(.*?)\}/', $string, $matches );
		$helper = new Pix_Open_Helper();

		if ( empty( $matches ) ) {
			// No match found, carry on with the same string
			return $string;
		}
		for ( $i = 0; $i < count( $matches[0] ); $i ++ ) {
			$string = str_replace( $matches[0][ $i ], $helper->get_shortcode_time( $matches[1][ $i ], $time_format ), $string );
		}

		return $string;
	}
}