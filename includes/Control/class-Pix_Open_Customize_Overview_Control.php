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
		<div id="oh-overview-description" class="opening-hours-section">
			<p>Example:</p>
			<div class="opening-hours-example">
				Monday 10am - 3pm<br/>
				Tuesday to Friday 9 - 17<br/>
				Sat noon - 2am<br/>
			</div>
			<a href="#" class="js-show-hours-scheme">Learn more about</a> setting hours.
		</div>

		<div class="opening-hours-section">
			<h3>Displaying the opening hours</h3>
			<p>There are two ways to display: </p>
			<ol class="opening-hours-list">
				<li>
                    <strong>Open Hours Widgets</strong>
                    <p>Use the two "Open Hours" widgets available to display an overview of the opening hours or the current status of your venue (open or closed).</p>
                </li>
				<li>
                    <strong>Shortcode</strong>
                    <p>Use the shortcodes below in a page content:</p>
                    <div class="opening-hours-example">[open-hours-overview]</div>
                    <div class="opening-hours-example">[open-hours-current-status]</div>
                </li>
			</ol>
		</div>
		<div class="preview_open_widget">
            <div class="opening-hours-preview-headline"><?php echo __('Preview'); ?></div>
			<?php
			$opening_hours_option = get_option('open_hours_overview_setting');

			if ($opening_hours_option) {
				echo do_shortcode( '[open-hours-overview ' . 'overview_option=' . base64_encode($opening_hours_option) . ']' );
			} else {
				echo __('You have not setup a schedule yet.', 'open_hours');
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