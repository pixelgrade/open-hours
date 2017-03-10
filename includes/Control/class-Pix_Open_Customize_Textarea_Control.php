<?php

require_once 'class-Pix_Open_Customize_Control.php';

/**
 * Class Pix_Customize_Text_Control
 * A simple Text Control
 */
class Pix_Open_Customize_Textarea_Control extends Pix_Open_Customize_Control {
	public $type = 'textarea';
	public $live = false;

	/**
	 * Render the control's content.
	 *
	 * @since 3.4.0
	 */
	public function render_content() {
		?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
			<textarea rows="5" id="open_hours_overview-dummy"
			          data-customize-setting-link="open_hours_overview-dummy"><?php echo esc_textarea( $this->value( 'open_hours_overview-dummy' ) ); ?></textarea>
			<textarea rows="5"
			          class="hidden" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
		</label>
		<?php

	}
}
