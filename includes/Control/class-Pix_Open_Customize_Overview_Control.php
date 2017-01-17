<?php

require_once 'class-Pix_Open_Customize_Control.php';

/**
 * Class Pix_Customize_HTML_Control
 * A simple HTML Control
 */
class Pix_Open_Customize_Overview_Control extends Pix_Open_Customize_Control {
	public $type = 'html';
	public $action = null;
	public $html = null;

	public function __construct( $wp_customize, $setting_id, $args = array() ) {
		parent::__construct( $wp_customize, $setting_id, $args );
		$this->html = '<p>This will be the example here. To add html. dsadasdsa dasdasdadsadas dsaasdsa </p>';
	}

	/**
	 * Render the control's content.
	 *
	 * @since 3.4.0
	 */
	public function render_content() {
		if ( ! empty( $this->html ) ) {
			echo( $this->html );
		}
	}
}