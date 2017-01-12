<?php

require_once( plugin_dir_path( __FILE__ ) . 'abstract-widget.php');

class OpenCurrentStatus_Widget extends OpenAbstract_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		$title = esc_html__( 'Open: Current Status A wonderful widget', 'text_domain' );
		$description =  esc_html__( 'A Foo Widget', 'text_domain' );

		parent::__construct( 'open_current_status_widget', $title , $description);
	}

	protected function registerFields () {

		// Fields
		$this->addField('title', array(
			'type' => 'text',
			'caption' => __('Title', 'text_domain')
		));

		$this->addField('open_note', array(
			'type' => 'text',
			'caption' => __('Open Note', 'text_domain')
		));

		$this->addField('close_note', array(
			'type' => 'text',
			'caption' => __('Close Note', 'text_domain')
		));


	}
}