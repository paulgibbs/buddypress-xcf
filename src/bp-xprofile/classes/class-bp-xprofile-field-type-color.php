<?php
/**
 * BuddyPress XProfile Classes.
 *
 * @package BuddyPress
 * @subpackage XProfileClasses
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Color xprofile field type.
 */
class BP_XProfile_Field_Type_Color extends BP_XProfile_Field_Type
{
	/**
	 * Constructor for the color field type.
	 */
	public function __construct() {
		parent::__construct();

		$this->name = _x( 'Color (HTML5 field)', 'xprofile field type', 'buddypress' );

		// Only letters or digits.
		$this->set_format( '/^[a-zA-Z0-9]{6}$/', 'replace' );

		/**
		 * Fires inside __construct() method for BP_XProfile_Field_Type_Color class.
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
	 * @param BP_XProfile_Field $current_field The current profile field on the add/edit screen.
	 * @param string            $control_type  Optional. HTML input type used to render the current
	 *                                         field's child options.
	 */
	public function admin_new_field_html( BP_XProfile_Field $current_field,
		$control_type = '' ) { }

	/**
	 * Modify the appearance of value. Apply autolink if enabled.
	 *
	 * @param  string $field_value Original value of field.
	 * @param  int    $field_id Id of field.
	 * @return string Value formatted.
	 */
	public static function display_filter( $field_value, $field_id = '' ) {

		$new_field_value = $field_value;

		if ( ! empty( $field_value ) ) {
			if ( ! empty( $field_id ) ) {
				$field = BP_XProfile_Field::get_instance( $field_id );
				if ( $field ) {
					$do_autolink = apply_filters( 'bxcft_do_autolink',
					$field->get_do_autolink() );
					if ( $do_autolink ) {
						$query_arg = bp_core_get_component_search_query_arg( 'members' );
						$search_url = add_query_arg( array(
								$query_arg => urlencode( $field_value ),
							),
						bp_get_members_directory_permalink() );
						$new_field_value = '<a href="' . esc_url( $search_url ) .
							'" rel="nofollow">' . $new_field_value . '</a>';
					}
				}
			}
		}

		/**
		 * Use this filter to modify the appearance of Color
		 * field value.
		 *
		 * @param  $new_field_value Value of field
		 * @param  $field_id Id of field.
		 * @return  Filtered value of field.
		 */
		return apply_filters( 'bxcft_color_display_filter', $new_field_value, $field_id );
	}
}
