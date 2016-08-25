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
 * Taxonomy xprofile field type.
 *
 * @since 2.7.0
 */
class BP_XProfile_Field_Type_Taxonomy extends BP_XProfile_Field_Type
{
	/**
	 * Constructor for the taxonomy field type.
	 *
	 * @since 2.7.0
	 */
	public function __construct() {
		parent::__construct();

		$this->category = _x( 'Multi Fields', 'xprofile field type category', 'buddypress' );
		$this->name = _x( 'Custom Taxonomy Selector', 'xprofile field type', 'buddypress' );

		$this->supports_options = true;

		$this->set_format( '/^.+$/', 'replace' );
		do_action( 'bp_xprofile_field_type_select_custom_taxonomy', $this );
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
		$html = $this->get_edit_field_html_elements( $raw_properties );
	?>
		<select <?php echo esc_html( $html ); ?>>
			<?php bp_the_profile_field_options(); ?>
		</select>
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
		if ( ! $options ) {
			$options = array();
			$i       = 1;
			while ( isset( $_POST[ $type . '_option' ][ $i ] ) ) {
				if ( $current_type_obj->supports_options &&
					! $current_type_obj->supports_multiple_defaults &&
					isset( $_POST[ "isDefault_{$type}_option" ][ $i ] ) &&
					(int) $_POST[ "isDefault_{$type}_option" ] === $i ) {
					$is_default_option = true;
				} elseif ( isset( $_POST[ "isDefault_{$type}_option" ][ $i ] ) ) {
					$is_default_option = (bool) $_POST[ "isDefault_{$type}_option" ][ $i ];
				} else {
					$is_default_option = false;
				}

				$options[] = (object) array(
					'id'                => -1,
					'is_default_option' => $is_default_option,
					'name'              => sanitize_text_field(
					wp_unslash( $_POST[ $type . '_option' ][ $i ] ) ),
				);

				++$i;
			}

			if ( ! $options ) {
				$options[] = (object) array(
					'id'                => -1,
					'is_default_option' => false,
					'name'              => '',
				);
			}
		}

		$taxonomies = get_taxonomies( array(
			'public'    => true,
			'_builtin'  => false,
		) );
	?>
		<div id="<?php echo esc_attr( $type ); ?>" class="postbox bp-options-box"
		style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
		<?php if ( ! $taxonomies ) : ?>
			<h3><?php esc_html_e( 'There is no custom taxonomy. You need to create at
				least one to use this field.', 'buddypress' ); ?></h3>
		<?php else : ?>
			<h3><?php esc_html_e( 'Select a custom taxonomy:', 'buddypress' ); ?></h3>
			<div class="inside">
				<p>
					<?php esc_html_e( 'Select a custom taxonomy:', 'buddypress' ); ?>
					<select name="<?php esc_attr_e( "{$type}_option[1]" ); ?>"
					id="<?php esc_attr_e( "{$type}_option[1]" ); ?>">
						<option value=""><?php esc_attr_e( 'Select...', 'buddypress' ); ?></option>
					<?php foreach ( $taxonomies as $k => $v ) : ?>
						<option value="<?php esc_attr_e( $k ); ?>"
						<?php if ( $options[0]->name === $k ) : ?> selected="selected"<?php endif; ?>>
							<?php esc_html_e( $v ); ?>
						</option>
					<?php endforeach; ?>
					</select>
				</p>
			</div>
	<?php endif; ?>
		</div>
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
		$user_id = bp_displayed_user_id();

		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		}

		// HTML5 required attribute.
		if ( bp_get_the_profile_field_is_required() ) {
			$raw_properties['required'] = 'required';
		}

		$html = $this->get_edit_field_html_elements( $raw_properties );
	?>
		<label for="<?php bp_the_profile_field_input_name(); ?>">
			<?php bp_the_profile_field_name(); ?>
			<?php if ( bp_get_the_profile_field_is_required() ) : ?>
				<?php esc_html_e( '(required)', 'buddypress' ); ?>
			<?php endif; ?>
		</label>
		<?php do_action( bp_get_the_profile_field_errors_action() ); ?>
		<select <?php esc_html_e( $html ); ?>>
			<option value=""><?php esc_html_e( 'Select...', 'buddypress' ); ?></option>
			<?php bp_the_profile_field_options( "user_id={$user_id}" ); ?>
		</select>
	<?php
	}

	/**
	 * Output the edit field options HTML for this field type.
	 *
	 * BuddyPress considers a field's "options" to be, for example, the items in a selectbox.
	 * These are stored separately in the database, and their templating is handled separately.
	 * Populate this method in a child class if it's required. Otherwise, you can leave it out.
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
		$options        = $this->field_obj->get_children();
		$term_selected  = BP_XProfile_ProfileData::get_value_byid( $this->field_obj->id,
		$args['user_id'] );

		$html = '';
		if ( $options ) {
			$taxonomy_selected = $options[0]->name;
			if ( ! empty( $_POST[ 'field_' . $this->field_obj->id ] ) ) {
				$new_term_selected = (int) $_POST[ 'field_' . $this->field_obj->id ];
				$term_selected = ( $term_selected !== $new_term_selected ) ?
					$new_term_selected : $term_selected;
			}
			// Get terms of custom taxonomy selected.
			$terms = get_terms( $taxonomy_selected, array(
				'hide_empty' => false,
			) );
			if ( $terms ) {
				foreach ( $terms as $term ) {
					$html .= sprintf( '<option value="%s"%s>%s</option>',
						$term->term_id,
						( $term_selected === $term->term_id ) ? ' selected="selected"' : '',
						$term->name
					);
				}
			}
		}

		esc_html_e( apply_filters( 'bp_get_the_profile_field_select_custom_taxonomy',
		$html, $args['type'], $term_selected, $this->field_obj->id ) );
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
		$validated = false;

		// Some types of field (e.g. multi-selectbox) may have multiple values to check.
		foreach ( (array) $values as $value ) {

			// Validate the $value against the type's accepted format(s).
			foreach ( $this->validation_regex as $format ) {
				if ( 1 === preg_match( $format, $value ) ) {
					$validated = true;
					continue;

				} else {
					$validated = false;
				}
			}
		}

		// Handle field types with accepts_null_value set if $values is an empty array.
		if ( ! $validated && is_array( $values ) && empty( $values ) &&
			$this->accepts_null_value ) {
			$validated = true;
		}

		return (bool) apply_filters( 'bp_xprofile_field_type_is_valid', $validated,
		$values, $this );
	}

	/**
	 * Modify the appearance of value. Apply autolink if enabled.
	 *
	 * @since 2.7.0
	 *
	 * @param  string $field_value Original value of field.
	 * @param  int    $field_id Id of field.
	 * @return string Value formatted
	 */
	public static function display_filter( $field_value, $field_id = '' ) {

		$new_field_value = $field_value;

		if ( ! empty( $field_value ) && ! empty( $field_id ) ) {
			$field = BP_XProfile_Field::get_instance( $field_id );
			if ( $field ) {
				$childs = $field->get_children();
				if ( ! empty( $childs ) && isset( $childs[0] ) ) {
					$taxonomy_selected = $childs[0]->name;
				}
				$field_value = trim( $field_value );
				$term = wpcom_vip_get_term_by( 'id', $field_value, $taxonomy_selected );
				if ( $term && $term->taxonomy === $taxonomy_selected ) {
					$new_field_value = $term->name;
				} else {
					$new_field_value = __( '--', 'buddypress' );
				}
			}
		}

		return $new_field_value;
	}
}
