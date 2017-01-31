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
		<div id="oh-overview-description" class="open-section">
			<p>Example:</p>
			<div class="open-example">
				Monday 10am - 3pm<br/>
				Tuesday to Friday 9 - 17<br/>
				Sat to Sun - 2am<br/>
			</div>
			<a href="#" class="js-show-hours-scheme">Learn more about</a> setting hours.
		</div>

		<div class="open-section">
			<h3>Displaying the opening hours</h3>
			<p>There are two ways to display: </p>
			<ol class="open-list">
				<li>
                    <strong>Opening Hours Widget</strong>
                    <p>Use the two "Opening Hours" widgets available to display an overview of the opening hours or the current status of your venuie (open or closed).</p>
                </li>
				<li>
                    <strong>Shortcode</strong>
                    <p>Use the shortcodes below in a page content:</p>
                    <div class="open-example">[opening-hours-overview]</div>
                    <div class="open-example">[opening-hours-current-status]</div>
                </li>
			</ol>
		</div>
		<div class="preview_open_widget">
            <div class="open-preview"><?php echo __('Preview'); ?></div>
			<?php
			$opening_hours_option = get_option('open_hours_overview_setting');

			if ($opening_hours_option) {
				echo do_shortcode( '[opening-hours-overview ' . 'overview_option=' . base64_encode($opening_hours_option) . ']' );
			} else {
				echo __('You have not setup a schedule yet.');
			}

			?>
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