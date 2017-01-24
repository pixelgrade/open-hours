<?php

class Pix_Open_Helper {

	/**
	 * A helper function that takes in a raw JSON of timeframes and returns an array of human readable schedule
	 */
	public function parse_open_hours( $hours, $hours_format = null, $closed_label = 'Closed' ) {
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
	public function _parse_hours( $hour, $format = null ) {
		$timestamp = strtotime( $hour );
		$date      = gmdate( $format, $timestamp );

		return $date;
	}

	/**
	 * @param null $filter
	 *
	 * @return bool|false|string
	 * Returns the time for a specific filter
	 */
	public function get_shortcode_time( $filter = null ) {
		$dw = date( "N", time() );
		$overview_option = get_option( 'open_hours_overview_setting' );

		if ( ! $overview_option ) {
			return false;
		}

		$schedule = json_decode( $overview_option, true );
		$response = '';

		switch ( $filter ) {
			case 'time-now':
				$response = date( 'Format String', time() );
				break;
			case 'today':
				$response = $dw;
				break;
			case 'next-day':
				$response = $dw + 1;
				break;
			case 'today-start-time':
				$today_interval = $this->_get_interval( $schedule, $dw );
				$response       = $this->_parse_hours( $today_interval[0]['start'], 'g : i A' );
				break;
			case 'today-end-time':
				$today_interval = $this->_get_interval( $schedule, $dw );
				$response       = $this->_parse_hours( $today_interval[0]['end'], 'g : i A' );
				break;
			case 'next-start-time':
				$next_day_interval = $this->_get_interval( $schedule, $dw + 1);
				$response       = $this->_parse_hours( $next_day_interval[0]['start'], 'g : i A' );
				break;
			case 'next-end-time':
				$next_day_interval = $this->_get_interval( $schedule, $dw + 1);
				$response       = $this->_parse_hours( $next_day_interval[0]['end'], 'g : i A' );
				break;
			default:
				break;
		}

		return $response;
	}

	/**
	 * @param $schedule
	 * @param $day
	 *
	 * @return array|bool
	 *
	 * A helper function that receives the JSON formatted schedule and returns the open interval for a specific day
	 */
	function _get_interval( $schedule, $day ) {

		if ( ! isset( $schedule['timeframes'] ) ) {
			return false;
		}

		$interval = array();

		foreach ( $schedule['timeframes'] as $timeframe ) {
			if ( array_key_exists( $day, $timeframe['days'] ) ) {
				$interval = $timeframe['open'];
			}
		}

		return $interval;
	}
}