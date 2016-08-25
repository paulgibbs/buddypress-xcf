<?php
/**
 * BuddyPress XProfile Classes.
 *
 * @package BuddyPress
 * @subpackage XProfileClasses
 * @since 2.7.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Slider xprofile field type.
 *
 * @since 2.7.0
 */
class BP_XProfile_Field_Type_Slider extends BP_XProfile_Field_Type
{
	/**
	 * Constructor for the slider field type.
	 *
	 * @since 2.7.0
	 */
	public function __construct() {
		parent::__construct();

		$this->name = __( 'Range input (HTML5 field)', 'buddypress' );

		$this->accepts_null_value = true;
		$this->supports_options = true;

		$this->set_format( '/^\d+\.?\d*$/', 'replace' );

		do_action( 'bp_xprofile_field_type_slider', $this );
	}

	/**
	 * Output HTML for this field type on the wp-admin Profile Fields screen.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @since 2.7.0
	 *
	 * @param array $raw_properties Optional key/value array of permitted attributes that you want to add.
	 */
	public function admin_field_html( array $raw_properties = array() ) {
		global $field;

		$args = array(
			'type' => 'range',
			'class' => 'buddypress-slider',
		);

		$options = $field->get_children( true );
		if ( $options ) {
			foreach ( $options as $o ) {
				if ( strpos( $o->name, 'min_' ) !== false ) {
					$args['min'] = str_replace( 'min_', '', $o->name );
				}
				if ( strpos( $o->name, 'max_' ) !== false ) {
					$args['max'] = str_replace( 'max_', '', $o->name );
				}
			}
		}

		$html = $this->get_edit_field_html_elements( array_merge(
			$args,
			$raw_properties
		) );
	?>
		<input <?php echo esc_html( $html ); ?> />
	<?php
	}

	/**
	 * Output the edit field HTML for this field type.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @since 2.7.0
	 *
	 * @param array $raw_properties Optional key/value array of
	 *                              {@link http://dev.w3.org/html5/markup/input.html permitted attributes}
	 *                              that you want to add.
	 */
	public function edit_field_html( array $raw_properties = array() ) {

		// User_id is a special optional parameter that certain other fields
		// types pass to {@link bp_the_profile_field_options()}.
		if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		$r = bp_parse_args( $raw_properties, array(
			'type'  => 'range',
			'value' => bp_get_the_profile_field_edit_value(),
		) );
		$options = $field->get_children( true );
		if ( $options ) {
			foreach ( $options as $o ) {
				if ( strpos( $o->name, 'min_' ) !== false ) {
					$r['min'] = str_replace( 'min_', '', $o->name );
				}
				if ( strpos( $o->name, 'max_' ) !== false ) {
					$r['max'] = str_replace( 'max_', '', $o->name );
				}
			}
		}
		?>

		<label for="<?php bp_the_profile_field_input_name(); ?>">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</label>

		<?php

		/** This action is documented in bp-xprofile/bp-xprofile-classes */
		do_action( bp_get_the_profile_field_errors_action() ); ?>
	?>
		<input <?php esc_attr( $this->get_edit_field_html_elements( $r ) ); ?>>
		<span id="output-field_<?php echo esc_attr( $field->id ); ?>"></span>
	<?php
	}

	/**
	 * This method usually outputs HTML for this field type's children options on the wp-admin Profile Fields
	 * "Add Field" and "Edit Field" screens, but for this field type, we don't want it, so it's stubbed out.
	 *
	 * @since 2.7.0
	 *
	 * @param BP_XProfile_Field $current_field The current profile field on the add/edit screen.
	 * @param string            $control_type  Optional. HTML input type used to render the current
	 *                                         field's child options.
	 */
	public function admin_new_field_html( BP_XProfile_Field $current_field,
		$control_type = '' ) {
		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );
		if ( false === $type ) {
			return;
		}

		$class            = $current_field->type !== $type ? 'display: none;' : '';
		$current_type_obj = bp_xprofile_create_field_type( $type );

		$options = $current_field->get_children( true );
		$min = '';
		$max = '';
		if ( ! $options ) {
			$options = array();
			$i       = 1;
			while ( isset( $_POST[ $type . '_option' ][ $i ] ) ) {
				$is_default_option = true;

				$options[] = (object) array(
					'id'                => -1,
					'is_default_option' => $is_default_option,
					'name'              => sanitize_text_field( wp_unslash( $_POST[ $type . '_option' ][ $i ] ) ),
				);

				++$i;
			}

			if ( ! $options ) {
				$options[] = (object) array(
					'id'                => -1,
					'is_default_option' => false,
					'name'              => '2',
				);
			}
		} else {
			foreach ( $options as $o ) {
				if ( strpos( $o->name, 'min_' ) !== false ) {
					$min = str_replace( 'min_', '', $o->name );
				}
				if ( strpos( $o->name, 'max_' ) !== false ) {
					$max = str_replace( 'max_', '', $o->name );
				}
			}
		}
	?>
		<div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box"
		style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<h3><?php esc_html_e( 'Write min and max values.', 'buddypress' ); ?></h3>
			<div class="inside">
				<p>
					<label for="<?php echo esc_attr( "{$type}_option1" ); ?>">
						<?php esc_html_e( 'Minimum:', 'buddypress' ); ?>
					</label>
					<input type="text" name="<?php echo esc_attr( "{$type}_option[1]" ); ?>"
						id="<?php echo esc_attr( "{$type}_option1" ); ?>"
						value="<?php echo esc_attr( $min ); ?>" />
					<label for="<?php echo esc_attr( "{$type}_option2" ); ?>">
						<?php esc_html_e( 'Maximum:', 'buddypress' ); ?>
					</label>
					<input type="text" name="<?php echo esc_attr( "{$type}_option[2]" ); ?>"
						id="<?php echo esc_attr( "{$type}_option2" ); ?>"
						value="<?php echo esc_attr( $max ); ?>" />
				</p>
			</div>
		</div>
		<script>
			var error_msg_slider = "<?php esc_html_e( 'Min value cannot be bigger than max value.', 'buddypress' ); ?>",
				error_msg_slider_empty = "<?php esc_html_e( 'You have to fill the two fields.', 'buddypress' ); ?>";
		</script>
	<?php
	}

	/**
	 * Check the given string against the registered formats for this field type.
	 *
	 * This method doesn't support chaining.
	 *
	 * @since 2.7.0
	 *
	 * @param string|array $values Value to check against the registered formats.
	 * @return bool True if the value validates
	 */
	public function is_valid( $values ) {
		$this->validation_whitelist = null;
		return parent::is_valid( $values );
	}
}
