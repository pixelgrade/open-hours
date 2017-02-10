<?php


abstract class OpenAbstract_Widget extends WP_Widget {

	/**
	 * String with unique widget identifier
	 * @var       string
	 */
	protected $widgetId;

	/**
	 * The Widget title
	 * @var       string
	 */
	protected $title;

	/**
	 * Widget description for widget admin panel
	 * @var       string
	 */
	protected $description;

	/**
	 * Associative array with:
	 *  key:    string with field name
	 *  value:  associative array w/ field options
	 *
	 * @var       array
	 */
	protected $fields;

	/**
	 * Register widget with WordPress.
	 */
	function __construct( $id, $title, $description ) {
		$this->id          = $id;
		$this->title       = $title;
		$this->description = $description;
//		$this->shortcode = $shortcode;
		$this->fields = array();

		$this->registerFields();

		parent::__construct( $id, $title, $description );
	}


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$this->widget_content( $args, $instance );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		foreach ( $this->fields as $field ) {
			$this->renderField( $field, $instance );
		}

	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		return $instance;
	}

	/**
	 * @param $args
	 * @param $instance
	 * Widget content
	 */
	protected function widget_content( $args, $instance ) {
	}

	/**
	 * Renders a single field from the collection
	 *
	 * @param     array $field The field config array
	 * @param     array $instance The current widget instance
	 *
	 * @return    string                The field markup
	 */
	public function renderField( array $field, array $instance ) {
		$value = array_key_exists( $field['name'], $instance ) ? $instance[ $field['name'] ] : null;

		// Set a default value
		if ( ! $value && isset( $field['default'] ) ) {
			$value = $field['default'];
		}

		// Switch based on type
		switch ( $field['type'] ) {
			case 'text':
				?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( $field['name'] ) ); ?>"><?php esc_attr_e( $field['caption'], 'text_domain' ); ?></label>
					<input id="<?php echo esc_attr( $this->get_field_id( $field['name'] ) ); ?>"
					       class="widefat<?php if ( ! empty ( $field['css_class'] ) ) {
						       echo '  ' . $field['css_class'];
					       } ?>"
					       name="<?php echo esc_attr( $this->get_field_name( $field['name'] ) ); ?>" type="text"
					       value="<?php echo esc_attr( $value ); ?>">
				</p>
				<?php
				break;
			case 'checkbox':
				?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( $field['name'] ) ); ?>"><?php esc_attr_e( $field['caption'], 'text_domain' ); ?></label>
					<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( $field['name'] ) ); ?>"
					       name="<?php echo esc_attr( $this->get_field_name( $field['name'] ) ); ?>" type="checkbox"
					       value=<?php echo esc_attr( $this->get_field_id( $field['name'] ) ); ?> <?php checked( isset( $instance[ $field['name'] ] ) ? $instance[ $field['name'] ] : 0 ); ?>>
				</p>
				<?php
				break;
			case 'description':
				?>
				<div class="<?php if ( isset( $field['css_class'] ) ) {
					echo $field['css_class'];
				}; ?>"><?php echo $field['notes']['header'] ?></div>
				<br/>
				<?php
				if ( isset( $field['notes'] ) && $field['notes']['footer'] ) {
					?>
					<div class="<?php if ( isset( $field['css_class'] ) ) {
						echo $field['css_class'];
					}; ?>"><?php echo $field['notes']['footer'] ?></div>
					<?php
				}
				break;
			default:
				break;
		}
		?>

		<?php
	}

	/**
	 * Adds a field to the collection
	 *
	 * @param     string $name The field name
	 * @param     array $options The field options
	 */
	public function addField( $name, array $options ) {
		$options['name']       = $name;
		$this->fields[ $name ] = $options;
	}

	/**
	 * Getter: (single) Field
	 *
	 * @param     string $name The name to search for
	 *
	 * @return    array               The field options
	 */
	public function getField( $name ) {
		return $this->fields[ $name ];
	}

	public static function registerWidget() {
		register_widget( get_called_class() );
	}

	/** Adds all fields for this Widget */
	abstract protected function registerFields();

	/**
	 * Getter: Widget Id
	 * @return    string
	 */
	public function getWidgetId() {
		return $this->id;
	}

	/**
	 * Getter: Title
	 * @return    string
	 */
	public function getTitle() {
		return $this->title;
	}

}