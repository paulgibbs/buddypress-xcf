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
 * Color xprofile field type.
 *
 * @since 2.7.0
 */
class BP_XProfile_Field_Type_Color extends BP_XProfile_Field_Type
{
	/**
	 * Constructor for the color field type.
	 *
	 * @since 2.7.0
	 */
	public function __construct() {
		parent::__construct();

		$this->category = _x( 'Single Fields', 'xprofile field type category', 'buddypress' );
		$this->name = _x( 'Color (HTML5 field)', 'xprofile field type', 'buddypress' );

		// Only letters or digits.
		$this->set_format( '/^[a-zA-Z0-9]{6}$/', 'replace' );

		/**
		 * Fires inside __construct() method for BP_XProfile_Field_Type_Color class.
		 *
		 * @since 2.7.0
		 *
		 * @param BP_XProfile_Field_Type_Color $this Current instance of
		 *                                            the field type number.
		 */
		do_action( 'bp_xprofile_field_type_color', $this );
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
			'type'  => 'color',
			'value' => bp_get_the_profile_field_edit_value(),
		) ); ?>

		<label for="<?php bp_the_profile_field_input_name(); ?>">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</label>

		<?php

		/** This action is documented in bp-xprofile/bp-xprofile-classes */
		do_action( bp_get_the_profile_field_errors_action() ); ?>

		<input <?php esc_attr( $this->get_edit_field_html_elements( $r ) ); ?>>
		<?php
		// TODO: Fallback when input color type is not available.
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
		$r = bp_parse_args( $raw_properties, array(
			'type' => 'color',
		) ); ?>

		<label for="<?php bp_the_profile_field_input_name(); ?>" class="screen-reader-text">
		<?php
			/* translators: accessibility text */
			esc_html_e( 'Color field', 'buddypress' );
		?></label>
		<input <?php esc_attr_e( $this->get_edit_field_html_elements( $r ) ); ?>>
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
		$control_type = '' ) { }
}
