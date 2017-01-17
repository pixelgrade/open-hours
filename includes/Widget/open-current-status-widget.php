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
	}

	protected function registerFields() {
		// Fields
		$this->addField( 'title', array(
			'type'    => 'text',
			'caption' => __( 'Title', 'text_domain' )
		) );

		$this->addField( 'open_note', array(
			'type'    => 'text',
			'caption' => __( 'Open Note', 'text_domain' )
		) );

		$this->addField( 'close_note', array(
			'type'    => 'text',
			'caption' => __( 'Close Note', 'text_domain' )
		) );
	}

	// @TODO Change the output to use shortcodes
	protected function widget_content( $args, $instance ) {
		$open_note  = $args['widget_id'] . '-openNote';
		$close_note = $args['widget_id'] . '-closeNote';

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// Display the open Note
		if ( array_key_exists( 'open_note', $instance ) ) {
			?>
			<div id=<?php echo $open_note ?>><?php echo __( $instance['open_note'], 'text_domain' ); ?></div>
			<?php
		}

		// Display the close note
		if ( array_key_exists( 'close_note', $instance ) ) {
			?>
			<div id=<?php echo $close_note ?>><?php echo __( $instance['close_note'], 'text_domain' ); ?></div>
			<?php
		}

		echo esc_html__( 'Buh bye now', 'text_domain' );
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance               = array();
		$instance['title']      = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['open_note']  = ( ! empty( $new_instance['open_note'] ) ) ? strip_tags( $new_instance['open_note'] ) : '';
		$instance['close_note'] = ( ! empty( $new_instance['close_note'] ) ) ? strip_tags( $new_instance['close_note'] ) : '';
		$instance['widget_id']  = $this->getWidgetId();

		return $instance;
	}
}