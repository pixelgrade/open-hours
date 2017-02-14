<?php

class Pix_Open_Helper {

	/**
	 * A helper function that takes in a raw JSON of timeframes and returns an array of human readable schedule
	 */
	public function parse_open_hours( $hours, $hours_format = null, $closed_label = 'Closed', $use_short_days = false, $compress_hours = false, $hide_closed_days = false ) {
		$hours_json = json_decode( $hours, true );
		$schedule   = array();

		if ( ! isset( $hours_json['timeframes'] ) ) {
			return false;
		}

		// Short day name or long day name
		if ( $use_short_days ) {
			$day_format = 'D';
		} else {
			$day_format = 'l';
		}

		// Create the initial array containing all the days of the week
		if ( ! $compress_hours && ! $hide_closed_days ) {
			for ( $i = 1; $i <= 7; $i ++ ) {
				$dow              = date( $day_format, strtotime( "Sunday +{$i} days" ) );
				$schedule[ $dow ] = $closed_label;
			}
		}
		// Get the closed days
		$closed_days = $this->_get_closed_days( $hours_json['timeframes'], $day_format );

		// Loop through our timeframes and add time intervals to our days
		foreach ( $hours_json['timeframes'] as $timeframe ) {
			if ( $compress_hours ) {
				// if the compress_opening_hours option is true - return compressed array
				$compressed_days          = $this->_parse_consecutive_days( $timeframe, $day_format, $hours_format );
				$compressed_days_interval = array_values( $compressed_days );

				$schedule[ key( $compressed_days ) ] = $compressed_days_interval[0];
			} else {
				// Build the normal array schedule
				foreach ( $timeframe['days'] as $day ) {
					$start = $this->_parse_hours( preg_replace( '/^\+/', '', $timeframe['open'][0]['start'] ), $hours_format );
					$end   = $this->_parse_hours( preg_replace( '/^\+/', '', $timeframe['open'][0]['end'] ), $hours_format );


					$day_key = date( $day_format, strtotime( "Sunday +{$day} days" ) );
					// Add the open time interval
					switch ( $day ) {
						case 1:
							$schedule[ $day_key ] = $start . ' - ' . $end;
							break;
						case 2:
							$schedule[ $day_key ] = $start . ' - ' . $end;
							break;
						case 3:
							$schedule[ $day_key ] = $start . ' - ' . $end;
							break;
						case 4:
							$schedule[ $day_key ] = $start . ' - ' . $end;
							break;
						case 5:
							$schedule[ $day_key ] = $start . ' - ' . $end;
							break;
						case 6:
							$schedule[ $day_key ] = $start . ' - ' . $end;
							break;
						case 7:
							$schedule[ $day_key ] = $start . ' - ' . $end;
							break;
						default:
							break;
					}
				}
			}
		}

		// if compressed hours - add the closed days at the end
		if ( $compress_hours && ! empty( $closed_days ) && ! $hide_closed_days ) {
			$schedule[ key( $closed_days ) ] = $closed_label;
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
	public function get_shortcode_time( $filter = null, $time_format = 'g:i A' ) {
		$dw              = date( "N", current_time( 'timestamp' ) );
		$next_day        = date( "N", current_time( 'timestamp' ) + 24 * 3600 );
		$overview_option = get_option( 'open_hours_overview_setting' );

		if ( ! $overview_option ) {
			return false;
		}

		$schedule = json_decode( $overview_option, true );
		$response = '';

		switch ( $filter ) {
			case 'time':
				$response = current_time( $time_format );
				break;
			case 'today':
				$response = date( 'l', strtotime( "Sunday + {$dw} days" ) );
				break;
			case 'today-opening-time':
				$today_interval = $this->_get_interval( $schedule, $dw );
				if ( $today_interval ) {
					$response = $this->_parse_hours( $today_interval[0]['start'], $time_format );
				}
				break;
			case 'today-closing-time':
				$today_interval = $this->_get_interval( $schedule, $dw );
				if ( $today_interval ) {
					$response = $this->_parse_hours( $today_interval[0]['end'], $time_format );
				}
				break;
			case 'today-timeframe':
				$today_interval = $this->_get_interval( $schedule, $dw );
				if ( $today_interval ) {
					$response = $this->_parse_hours( $today_interval[0]['start'], $time_format ) . ' - ' . $this->_parse_hours( $today_interval[0]['end'], $time_format );
				}
				break;
			case 'next-opening-day':
				$next_open_day = $this->get_next_open_day( $schedule, $dw, $time_format );
				$response      = date( 'l', strtotime( "Sunday + {$next_open_day} days" ) );
				break;
			case 'next-opening-time':
				$response = $this->get_next_open_day( $schedule, $dw, $time_format, true, 'start' );
				break;
			case 'next-closing-time':
				$response = $this->get_next_open_day( $schedule, $dw, $time_format, true, 'end' );
				break;
			case 'next-opening-timeframe':
				$response = $this->get_next_open_day( $schedule, $dw, $time_format, true, null );
				break;
			default:
				break;
		}

		return $response;
	}

	public function get_next_open_day( $schedule, $day, $time_format, $interval = null, $period = null ) {
		$today_interval = $this->_get_interval( $schedule, $day );

		// If we do have an interval for today - check to see if it's been finshed and return the correct day when it will be open again.
		if ( ! empty( $today_interval ) && ! $interval && ! $period ) {
			// Get the current timestamp and compare it to the end time for today's interval. If it's bigger - then today's schedule has ended.
			$current_timestamp = current_time( 'timestamp' );
			$today_end_time    = strtotime( preg_replace( '/^\+/', '', $today_interval[0]['end'] ) );

			// If the current timestamp is bigger than the day's end time - increment the day.
			if ( $current_timestamp > $today_end_time ) {
				$dw  = $day + 1;
				$day = date( 'N', strtotime( "Sunday + {$dw} days" ) );
			}

			return $day;
		}

		for ( $i = 0; $i < 7 * 24 * 3600; $i += 24 * 3600 ) {
			$day               = date( "N", current_time( 'timestamp' ) + $i );
			$next_day_interval = $this->_get_interval( $schedule, $day );

			if ( ! empty( $next_day_interval ) && ! $interval && ! $period ) {
				return $day;
			} elseif ( ! empty( $next_day_interval ) && $interval && $period ) {
				return $this->_parse_hours( $next_day_interval[0][ $period ], $time_format );
			} elseif ( ! empty( $next_day_interval ) && $interval && ! $period ) {
				return $this->_parse_hours( $next_day_interval[0]['start'], $time_format ) . ' - ' . $this->_parse_hours( $next_day_interval[0]['end'], $time_format );
			}
		}

		return false;
	}

	public function is_open() {
		$overview_option = get_option( 'open_hours_overview_setting' );

		if ( ! $overview_option ) {
			return false;
		}

		$parsed_option = json_decode( $overview_option, true );

		if ( ! isset( $parsed_option['timeframes'] ) ) {
			return false;
		}

		$today     = date( 'N', current_time( 'timestamp' ) );
		$yesterday = date( 'N', current_time( 'timestamp' ) - 24 * 3600 );
		$ct        = current_time( 'timestamp' );

		if ( ! isset( $parsed_option['timeframes'] ) ) {
			//exit
			return false;
		}

		foreach ( $parsed_option['timeframes'] as $timeframe ) {
			$days          = $timeframe['days'];
			$open_interval = $timeframe['open'];

			if ( in_array( $today, $days ) ) {
				if ( isset( $open_interval[0] ) ) {
					$start = strtotime( preg_replace( '/^\+/', '', $open_interval[0]['start'] ) );
					$end   = strtotime( preg_replace( '/^\+/', '', $open_interval[0]['end'] ) );

					if ( $end < $start ) {
						$end = strtotime( '+1 day', $end );
					}

					if ( ( $ct >= $start && $ct <= $end ) ) {
						// It's open
						return true;
					}
				}
			}

			// Check for prev day
			if ( in_array( $yesterday, $days ) ) {
				if ( isset( $open_interval[0] ) ) {
					$start = strtotime( preg_replace( '/^\+/', '', $open_interval[0]['start'] ) );
					$end   = strtotime( preg_replace( '/^\+/', '', $open_interval[0]['end'] ) );

					if ( $end < $start ) {
						$end = strtotime( '+1 day', $end );
					}

					if ( ( $ct >= $start && $ct <= $end ) ) {
						// It's open
						return true;
					}
				}
			}
		}

		return false;
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
			if ( in_array( $day, $timeframe['days'] ) ) {
				$interval = $timeframe['open'];
			}
		}

		return $interval;
	}

	function _parse_consecutive_days( $timeframe, $day_format, $hours_format ) {
		$days = $timeframe['days'];
		$open = $timeframe['open'];

		$start    = $this->_parse_hours( preg_replace( '/^\+/', '', $open[0]['start'] ), $hours_format );
		$end      = $this->_parse_hours( preg_replace( '/^\+/', '', $open[0]['end'] ), $hours_format );
		$response = array();

		$consecutive_days     = array();
		$non_consecutive_days = array();

		for ( $i = 0; $i < count( $days ); $i ++ ) {
			if ( isset( $days[ $i + 1 ] ) && $days[ $i ] == $days[ $i + 1 ] - 1 ) {
				// consecutive
				array_push( $consecutive_days, $days[ $i ] );
			} elseif ( isset( $days[ $i - 1 ] ) && $days[ $i ] == $days[ $i - 1 ] + 1 ) {
				array_push( $consecutive_days, $days[ $i ] );
			} else {
				// not consecutive
				array_push( $non_consecutive_days, $days[ $i ] );
			}
		}

		if ( ! empty( $consecutive_days ) ) {
			$parsed_first_day = date( $day_format, strtotime( "Sunday +{$consecutive_days[0]} days" ) );
			$last_element     = array_values( array_slice( $consecutive_days, - 1 ) );

			$parsed_last_day = date( $day_format, strtotime( "Sunday +{$last_element[0]} days" ) );

			$response[ $parsed_first_day . ' - ' . $parsed_last_day ] = $start . ' - ' . $end;
		}

		if ( ! empty ( $non_consecutive_days ) ) {
			foreach ( $non_consecutive_days as $ncd ) {
				$parsed_day              = date( $day_format, strtotime( "Sunday +{$ncd} days" ) );
				$response[ $parsed_day ] = $start . ' - ' . $end;
			}

		}

		return $response;
	}

	/**
	 * Helper function that returns closed days
	 */
	function _get_closed_days( $timeframes, $day_format = 'l' ) {
		$present_days = array();
		$all_days     = array( 1, 2, 3, 4, 5, 6, 7 );
		$key          = '';

		foreach ( $timeframes as $timeframe ) {
			$present_days = array_merge( $present_days, $timeframe['days'] );
		}

		$closed_days = array_diff( $all_days, $present_days );

		if ( ! empty( $closed_days ) ) {
			$num_days = count( $closed_days );
			$i        = 0;

			foreach ( $closed_days as $closed_day ) {
				$parsed_day = date( $day_format, strtotime( "Sunday +{$closed_day} days" ) );
				if ( ++ $i !== $num_days ) {
					$key .= $parsed_day . ', ';
				} else {
					$key .= $parsed_day;
				}
			}
		}
		$response[ $key ] = 'closed';

		return $response;
	}

}