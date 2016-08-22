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

		$this->name = _x( 'Color (HTML5 field)', 'xprofile field type', 'bxcft' );

		$this->set_format( '/^.+$/', 'replace' );
		do_action( 'bp_xprofile_field_type_color', $this );
	}

	/**
	 * Output HTML for this field type on the wp-admin Profile Fields screen.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @param array $raw_properties Optional key/value array of permitted attributes that you want to add.
	 */
	public function admin_field_html( array $raw_properties = array() ) {
		$html = $this->get_edit_field_html_elements( array_merge(
			array( 'type' => 'color' ),
			$raw_properties
		) );
	?>
		<input <?php echo esc_html( $html ); ?>>
	<?php
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
		if ( isset( $raw_properties['user_id'] ) ) {
			unset( $raw_properties['user_id'] );
		}

		// HTML5 required attribute.
		if ( bp_get_the_profile_field_is_required() ) {
			$raw_properties['required'] = 'required';
		}

		$html = $this->get_edit_field_html_elements( array_merge(
			array(
				'type'  => 'color',
				'value' => bp_get_the_profile_field_edit_value(),
			),
			$raw_properties
		) );
	?>
		<label for="<?php bp_the_profile_field_input_name(); ?>">
			<?php bp_the_profile_field_name(); ?>
			<?php if ( bp_get_the_profile_field_is_required() ) : ?>
				<?php esc_html_e( '(required)', 'buddypress' ); ?>
			<?php endif; ?>
		</label>
		<?php do_action( bp_get_the_profile_field_errors_action() ); ?>
		<input <?php echo esc_html( $html ); ?>>
		<script>
			if (!Modernizr.inputtypes.color) {
				// No html5 field colorpicker => Calling jscolor.
				jQuery('input#<?php bp_the_profile_field_input_name() ?>').addClass('color');
			}
		</script>
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
