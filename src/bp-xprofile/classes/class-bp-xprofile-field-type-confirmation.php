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
 * Checkbox acceptance field type.
 *
 * @since 2.7.0
 */
class BP_XProfile_Field_Type_Confirmation extends BP_XProfile_Field_Type {

	/**
	 * Constructor for the checkbox acceptance field type.
	 *
	 * @since 2.7.0
	 */
	public function __construct() {
		parent::__construct();

		$this->category = _x( 'Single Fields', 'xprofile field type category', 'buddypress' );
		$this->name = _x( 'Checkbox Acceptance', 'xprofile field type', 'buddypress' );

		$this->accepts_null_value   = true;
		$this->supports_options     = true;
		$this->validation_whitelist = array('1');

		$this->set_format( '/^.+$/', 'replace' );

		/**
		 * Fires inside __construct() method for BP_XProfile_Field_Type_Confirmation class.
		 *
		 * @since 2.7.0
		 *
		 * @param BP_XProfile_Field_Type_Confirmation $this Current instance of
		 *                                            the field type number.
		 */
		do_action( 'bp_xprofile_field_type_confirmation', $this );
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

		$options = $field->get_children( true );
		$text = '';
		foreach ( $options as $option ) {
			$text .= rawurldecode( $option->name );
		}

		$html = $this->get_edit_field_html_elements( array_merge(
			array( 'type' => 'checkbox' ),
			$raw_properties
		) );
	?>
		<label for="<?php bp_the_profile_field_input_name(); ?>">
			<input <?php echo esc_html( $html ); ?>>
			<?php echo esc_html( $text ); ?>
		</label>
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

		// User_id is a special optional parameter that we pass to
		// {@link bp_the_profile_field_options()}.
		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		} else {
			$user_id = bp_displayed_user_id();
		}
		$options = $field->get_children( true );
		$text = '';
		foreach ( $options as $option ) {
			$text .= rawurldecode( $option->name );
		}

		/** This action is documented in bp-xprofile/bp-xprofile-classes */
		do_action( bp_get_the_profile_field_errors_action() ); ?>

		<label for="<?php bp_the_profile_field_input_name(); ?>">
			<input <?php esc_attr( $this->get_edit_field_html_elements( $r ) ); ?>>
			<?php esc_html_e( $text ); ?>
		</label>

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
	public function admin_new_field_html( BP_XProfile_Field $current_field, $control_type = '' ) {
		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );
		if ( false === $type ) {
			return;
		}

		$class            = ($current_field->type !== $type) ? 'display: none;' : '';
		$current_type_obj = bp_xprofile_create_field_type( $type );

		$text = '';
		$options = $current_field->get_children( true );
		if ( ! $options ) {
			$options = array();
			$i       = 1;
			while ( isset( $_POST[ $type . '_option' ][ $i ] ) ) {
				if ( $current_type_obj->supports_options && ! $current_type_obj->supports_multiple_defaults && isset( $_POST[ "isDefault_{$type}_option" ][ $i ] ) && (int) $_POST[ "isDefault_{$type}_option" ] === $i ) {
					$is_default_option = true;
				} elseif ( isset( $_POST[ "isDefault_{$type}_option" ][ $i ] ) ) {
					$is_default_option = (bool) $_POST[ "isDefault_{$type}_option" ][ $i ];
				} else {
					$is_default_option = false;
				}

				$options[] = (object) array(
					'id'                => 0,
					'is_default_option' => $is_default_option,
					'name'              => sanitize_text_field( wp_unslash( $_POST[ $type . '_option' ][ $i ] ) ),
				);

				$text .= sanitize_text_field( wp_unslash( $_POST[ $type . '_option' ][ $i ] ) );
				++$i;
			}

			if ( ! $options ) {
				$options[] = (object) array(
					'id'                => 0,
					'is_default_option' => false,
					'name'              => '',
				);
			}
		} else {
			foreach ( $options as $option ) {
				$text .= rawurldecode( $option->name );
			}
		}
	?>
		<div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<h3><?php esc_html_e( 'Use this field to write a text that should be displayed beside the checkbox:', 'buddypress' ); ?></h3>
			<div class="inside">
				<p>
					<textarea name="<?php echo esc_attr( "{$type}_text" ); ?>"
							  id="<?php echo esc_attr( "{$type}_text" ); ?>" rows="5" cols="60"><?php echo esc_textarea( $text ); ?></textarea>
				</p>
			</div>
			<?php if ( $options ) : ?>
				<?php $i = 1; ?>
				<?php foreach ( $options as $option ) : ?>
				<input type="hidden" name="<?php echo esc_attr( "{$type}_option[{$i}]" ); ?>"
					   id="<?php echo esc_attr( "{$type}_option{$i}" ); ?>" value="<?php echo esc_attr( $option->name ); ?>" />
					<?php $i++; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php
	}

	/**
	 * Output the edit field options HTML for this field type.
	 *
	 * BuddyPress considers a field's "options" to be, for example, the items in a selectbox.
	 * These are stored separately in the database, and their templating is handled separately.
	 *
	 * This templating is separate from {@link BP_XProfile_Field_Type::edit_field_html()} because
	 * it's also used in the wp-admin screens when creating new fields, and for backwards compatibility.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @since 2.7.0
	 *
	 * @param array $args Optional. The arguments passed to {@link bp_the_profile_field_options()}.
	 */
	public function edit_field_options_html( array $args = array() ) {
		$options                = $this->field_obj->get_children();
		$checkbox_acceptance    = maybe_unserialize( BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id, $args['user_id'] ) );

		if ( ! empty( $_POST[ 'field_' . $this->field_obj->id ] ) ) {
			$new_checkbox_acceptance = sanitize_text_field( wp_unslash( $_POST[ 'field_' . $this->field_obj->id ] ) );
			$checkbox_acceptance = ( $checkbox_acceptance !== $new_checkbox_acceptance ) ? $new_checkbox_acceptance : $checkbox_acceptance;
		}

		$html = '<input type="checkbox" name="' . bp_get_the_profile_field_input_name() . '" id="check_acc_' . bp_get_the_profile_field_input_name() . '"';
		if ( '1' === $checkbox_acceptance ) {
			$html .= ' checked="checked"';
		}
		if ( isset( $args['required'] ) && $args['required'] ) {
			$html .= ' required="required" aria-required="true"';
		}
		$html .= ' value="1" /> ';

		/**
		 * Filters the HTML output for an individual field options confirmation.
		 *
		 * @since 2.7.0
		 *
		 * @param string $html Text.
		 * @param object $args['type'] Type of field.
		 * @param int    $id       ID of the field object being rendered.
		 * @param string $checkbox_acceptance Current value.
		 */
		esc_html_e( apply_filters( 'bp_get_the_profile_field_confirmation', $html, $args['type'], $this->field_obj->id, $checkbox_acceptance ) );
	}

	/**
	 * Modify the appearance of value. Apply autolink if enabled.
	 *
	 * @since 2.7.0
	 *
	 * @param  string $field_value      Original value of field.
	 * @param  int    $field_id   Id of field.
	 * @return string   Value formatted
	 */
	public static function display_filter( $field_value, $field_id = '' ) {
		if ( '1' === $field_value ) {
			return __( 'yes', 'buddypress' );
		}

		return __( 'no', 'buddypress' );
	}

	/**
     * Allow field types to modify submitted values before they are validated.
     *
     * In some cases, it may be appropriate for a field type to catch
     * submitted values and modify them before they are passed to the
     * is_valid() method. For example, URL validation requires the
     * 'http://' scheme (so that the value saved in the database is always
     * a fully-formed URL), but in order to allow users to enter a URL
     * without this scheme, BP_XProfile_Field_Type_URL prepends 'http://'
     * when it's not present.
     *
     * By default, this is a pass-through method that does nothing. Only
     * override in your own field type if you need this kind of pre-
     * validation filtering.
     *
     * @since 2.1.0
     * @since 2.4.0 Added the `$field_id` parameter.
     *
     * @param mixed      $field_value Submitted field value.
     * @param string|int $field_id    Optional. ID of the field.
     * @return mixed
     */
    public static function pre_validate_filter( $field_value, $field_id = '' ) {
        if (empty($field_value)) {
            $field_value = '0';
        }

        return $field_value;
    }
}
