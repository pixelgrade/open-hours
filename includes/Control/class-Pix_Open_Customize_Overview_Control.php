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

		ob_start();
		?>
		<div id="oh-overview-description">
			<p>Example:</p>
			<div>
				Monday 10am - 3pm<br/>
				Tuesday to Friday 9 - 17<br/>
				Sat to Sun - 2am<br/>
			</div>
			<a href="#">Learn more about</a> setting hours.
		</div>

		<div>
			<h3>Displaying the opening hours</h3>
			<p>There are two ways to display: </p>
			<ol>
				<li><strong>Opening Hours Widget</strong></li>
				<p>Use the two "Opening Hours" widgets available to display an overview of the opening hours or the current status of your venuie (open or closed).</p>
				<li><strong>Shortcode</strong></li>
				<p>Use the shortcodes below in a page content:</p>
				[opening-hours-overview]
				[opening-hours-current-status]
			</ol>
		</div>
		<div class="preview_open_widget">
			sadas
		</div>
		<?php
		$this->html = ob_get_clean();
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