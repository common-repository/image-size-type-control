<?php
/**
 * Image Size Limiter - Options
 *
 * @since Version 1.0
 */


/**
 * Register the form setting for our imsl_options array.
 *
 * This function is attached to the admin_init action hook.
 *
 * @since Version 1.0
 */
 if ( ! function_exists( 'imsl_options_init' ) ) :
	function imsl_options_init() {

		// If we have no options in the database, let's add them now.
		if ( false === imsl_get_options() )
			add_option( 'imsl_options', imsl_get_default_options() );

		register_setting(
			'media',       // Options group
			'imsl_options', // Database option, see imsl_get_options()
			'imsl_options_validate' // The sanitization callback, see imsl_options_validate()
		);

		add_settings_field( 
			'img_upload_limit', 
			'Maximum File Size for Images', 
			'imsl_settings_field_img_upload_limit', 
			'media', 
			'uploads' 
		);
		add_settings_field( 
			'img_upload_max_dimension_limit',
			'Maximum Image dimension', 
			'imsl_settings_field_img_upload_dimension_limit', 
			'media', 
			'uploads' 
		);
		add_settings_field( 
			'img_upload_min_dimension_limit',
			'Minimum Image dimension', 
			'imsl_settings_field_img_upload_min_dimension_limit', 
			'media', 
			'uploads' 
		);
		add_settings_field( 
			'img_upload_allowed_ext_limit',
			'Allowed Extension',
			'imsl_settings_field_allowed_ext_limit', 
			'media', 
			'uploads' 
		);
		add_settings_field( 
			'img_upload_disallowed_ext_limit',
			'Disallowed Extension',
			'imsl_settings_field_disallowed_ext_limit', 
			'media', 
			'uploads' 
		);
	}
endif;
add_action( 'admin_init', 'imsl_options_init' );


/**
 * Returns the default options.
 *
 * @since Version 1.0
 */
 if ( ! function_exists( 'imsl_get_default_options' ) ) :
	function imsl_get_default_options() {
		$wpisl = new IMSL_Size_Limit;
		$limit = $wpisl->imsl_limit();
		$default_options = array(
			'img_upload_limit' => $limit,
			'img_upload_disallowed_ext_limit' => '',
			'img_upload_allowed_ext_limit' => '',
			'img_upload_disallowed_ext_limit_checkbox' => 'no',
			'img_upload_allowed_ext_limit_checkbox' => 'no',
			'img_upload_min_dimension_limit_checkbox' => 'no',
			'img_upload_max_dimension_limit_checkbox' => 'no',
			'img_upload_min_dimension_height_limit' => '',
			'img_upload_min_dimension_width_limit' => '',
			'img_upload_max_dimension_height_limit' => '',
			'img_upload_max_dimension_width_limit' => '',
		);

		return apply_filters( 'imsl_default_options', $default_options );
	}
endif;
	
/**
 * Returns the options array.
 *
 * @since Version 1.0
 */
 if ( ! function_exists( 'imsl_get_options' ) ) :
	function imsl_get_options() {
		return get_option( 'imsl_options', imsl_get_default_options() );
	}
endif;


/**
 * Renders the Maximum Upload Size setting field.
 *
 * @since Version 1.0
 *
 */

if ( ! function_exists( 'imsl_settings_field_img_upload_limit' ) ) :
	function imsl_settings_field_img_upload_limit() {
		$options = imsl_get_options();
		$imsl = new IMSL_Size_Limit;
		$limit = $imsl->imsl_limit();

			// Sanitize
			$id = 'img_upload_limit';

			if ( isset($options[$id]) && ($options[$id] < $limit) ) {
				$value = $options[$id];
			} 
			/*elseif  ( empty($options[$id])  )  {
				$value = '1000';
			} */
			else {
				$value = $limit;
			}

			$field = '<p>
				<input name="imsl_options[' . $id . ']' . '" id="imsl-limit" type="text" value="' . $value . '" size="4" maxlength="5" /> KB
				<br>
				<span class="description">' . __('Server maximum: ', 'image-size-type-control') . $limit.' KB</span>
			</p>';

		echo $field;

	}
endif;
/**
 * Renders the Disallowed ext.
 *
 * @since Version 1.0
 *
 */
 if ( ! function_exists( 'imsl_settings_field_disallowed_ext_limit' ) ) :
	function imsl_settings_field_disallowed_ext_limit() {
		$options = imsl_get_options();
		$imsl = new IMSL_Size_Limit;
		$limit = $imsl->imsl_limit();

			// Sanitize
			$id = 'img_upload_disallowed_ext_limit';
			$checkbox_id = 'img_upload_disallowed_ext_limit_checkbox';

			if ( isset($options[$id]) ) {
				$value = $options[$id];
			}
			else {
				$value = '';
			}
			
			if ( isset($options[$checkbox_id]) ) {
				$checkbox_val = $options[$checkbox_id];
			}
			else {
				$checkbox_val = 'no';
			}
			

			$field = '<input name="imsl_options[' . $checkbox_id . ']' . '" id="' . $checkbox_id . '" type="checkbox" ' . checked($checkbox_val,'yes',false) . '>
					<label for="' . $checkbox_id . '">' . __('Enable Disllow extension Restriction', 'image-size-type-control') . '</label>
					<p>&nbsp;</p>
					<input name="imsl_options[' . $id . ']' . '" id="' . $id . '" type="text" value="' . $value . '" placeholder="' . __('jpg,jpeg,png', 'image-size-type-control') . '" class="large-text">
					<br>';
					

		echo $field;
	}
endif;
/**
 * Renders the Allowed ext.
 *
 * @since Version 1.0
 *
 */
 if ( ! function_exists( 'imsl_settings_field_allowed_ext_limit' ) ) :
	function imsl_settings_field_allowed_ext_limit() {
		$options = imsl_get_options();
		$imsl = new IMSL_Size_Limit;
		$limit = $imsl->imsl_limit();

			// Sanitize
			$id = 'img_upload_allowed_ext_limit';
			$checkbox_id = 'img_upload_allowed_ext_limit_checkbox';

			if ( isset($options[$id]) ) {
				$value = $options[$id];
			}
			else {
				$value = '';
			}
			
			if ( isset($options[$checkbox_id]) ) {
				$checkbox_val = $options[$checkbox_id];
			}
			else {
				$checkbox_val = 'no';
			}

			$field = '<input name="imsl_options[' . $checkbox_id . ']' . '" id="' . $checkbox_id . '" type="checkbox" ' . checked($checkbox_val,'yes',false) . '>
					<label for="' . $checkbox_id . '">' . __('Enable Allow extension Restriction', 'image-size-type-control') . '</label>
					<p>&nbsp;</p>
					<input name="imsl_options[' . $id . ']' . '" id="' . $id . '" type="text" value="' . $value . '" placeholder="' . __('jpg,jpeg,png', 'image-size-type-control') . '" class="large-text">
					<br>';
					

		echo $field;
	}
endif;
/**
 * Renders the Maximum Upload Dimemtion setting field.
 *
 * @since Version 1.0
 *
 */
 if ( ! function_exists( 'imsl_settings_field_img_upload_dimension_limit' ) ) :
	function imsl_settings_field_img_upload_dimension_limit() {
		$options = imsl_get_options();
		$imsl = new IMSL_Size_Limit;
		$limit = $imsl->imsl_limit();

			// Sanitize
			$height_id = 'img_upload_max_dimension_height_limit';
			$width_id = 'img_upload_max_dimension_width_limit';
			$checkbox_id = 'img_upload_max_dimension_limit_checkbox';

			if ( isset($options[$height_id]) ) {
				$height_value = $options[$height_id];
			} 
			else {
				$height_value = 300;
			}

			if ( isset($options[$width_id]) ) {
				$width_value = $options[$width_id];
			} 
			else {
				$width_value = 300;
			}
			
			if ( isset($options[$checkbox_id]) ) {
				$checkbox_val = $options[$checkbox_id];
			}
			else {
				$checkbox_val = 'no';
			}

			$field = '<input name="imsl_options[' . $checkbox_id . ']' . '" id="' . $checkbox_id . '" type="checkbox" ' . checked($checkbox_val,'yes',false) . '>
					<label for="' . $checkbox_id . '">' . __('Enable maximum image dimension restriction at the time of upload.', 'image-size-type-control') . '</label>
					<p>&nbsp;</p>
					<fieldset><legend class="screen-reader-text"><span>' . __('Thumbnail size', 'image-size-type-control') . '</span></legend>
					<label for="' . $width_id . '_size_">' . __('Width', 'image-size-type-control') . '</label>
					<input name="imsl_options[' . $width_id . ']' . '" id="' . $width_id . '" value="' . $width_value . '" type="number" step="1" min="0" class="small-text">
					<br>
					<label for="' . $height_id . '_size_">' . __('Height', 'image-size-type-control') . '</label>
					<input name="imsl_options[' . $height_id . ']' . '" id="' . $height_id . '" value="' . $height_value . '" type="number" step="1" min="0" class="small-text">
					</fieldset>';

		echo $field;
	}
endif;

/**
 * Renders the Minimum Upload Dimemtion setting field.
 *
 * @since Version 1.0
 *
 */
 
 if ( ! function_exists( 'imsl_settings_field_img_upload_min_dimension_limit' ) ) :
	function imsl_settings_field_img_upload_min_dimension_limit() {
		$options = imsl_get_options();
		$imsl = new IMSL_Size_Limit;
		$limit = $imsl->imsl_limit();

			// Sanitize
			$height_id = 'img_upload_min_dimension_height_limit';
			$width_id = 'img_upload_min_dimension_width_limit';
			$checkbox_id = 'img_upload_min_dimension_limit_checkbox';

			if ( isset($options[$height_id]) ) {
				$height_value = $options[$height_id];
			} 
			else {
				$height_value = 300;
			}

			if ( isset($options[$width_id]) ) {
				$width_value = $options[$width_id];
			} 
			else {
				$width_value = 300;
			}
			
			if ( isset($options[$checkbox_id]) ) {
				$checkbox_val = $options[$checkbox_id];
			}
			else {
				$checkbox_val = 'no';
			}

			$field = '<input name="imsl_options[' . $checkbox_id . ']' . '" id="' . $checkbox_id . '" type="checkbox" ' . checked($checkbox_val,'yes',false) . '>
					<label for="' . $checkbox_id . '">' . __('Enable minimum image dimension restriction at the time of upload.', 'image-size-type-control') . '</label>
					<p>&nbsp;</p>
					<fieldset><legend class="screen-reader-text"><span>' . __('Thumbnail size', 'image-size-type-control') . '</span></legend>
					<label for="' . $width_id . '_size_">' . __('Width', 'image-size-type-control') . '</label>
					<input name="imsl_options[' . $width_id . ']' . '" id="' . $width_id . '" value="' . $width_value . '" type="number" step="1" min="0" class="small-text">
					<br>
					<label for="' . $height_id . '_size_">' . __('Height', 'image-size-type-control') . '</label>
					<input name="imsl_options[' . $height_id . ']' . '" id="' . $height_id . '" value="' . $height_value . '" type="number" step="1" min="0" class="small-text">
					</fieldset>';

		echo $field;
	}
endif;
/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see imsl_options_init()
 * @since Version 1.0
 */
if ( ! function_exists( 'imsl_options_validate' ) ) :
	function imsl_options_validate( $input ) {
		$output = $defaults = imsl_get_default_options();
		$imsl = new IMSL_Size_Limit;
		$limit = $imsl->imsl_limit();

		$output['img_upload_limit'] = str_replace(',','', $input['img_upload_limit']);
		
		$output['img_upload_disallowed_ext_limit'] = $input['img_upload_disallowed_ext_limit'];
		$output['img_upload_disallowed_ext_limit_checkbox'] = ( isset($input['img_upload_disallowed_ext_limit_checkbox']) ? 'yes' : 'no' );
		$output['img_upload_allowed_ext_limit'] = $input['img_upload_allowed_ext_limit'];
		$output['img_upload_allowed_ext_limit_checkbox'] = ( isset($input['img_upload_allowed_ext_limit_checkbox']) ? 'yes' : 'no' );

		$output['img_upload_min_dimension_limit_checkbox'] = ( isset($input['img_upload_min_dimension_limit_checkbox']) ? 'yes' : 'no' );
		$output['img_upload_max_dimension_limit_checkbox'] = ( isset($input['img_upload_max_dimension_limit_checkbox']) ? 'yes' : 'no' );
		
		$output['img_upload_min_dimension_height_limit'] = $input['img_upload_min_dimension_height_limit'];
		$output['img_upload_min_dimension_width_limit'] = $input['img_upload_min_dimension_width_limit'];
		
		$output['img_upload_max_dimension_height_limit'] = $input['img_upload_max_dimension_height_limit'];
		$output['img_upload_max_dimension_width_limit'] = $input['img_upload_max_dimension_width_limit'];

		$output['img_upload_limit'] = absint( intval( $output['img_upload_limit'] ) );

		if ( $output['img_upload_limit'] > $limit ) {
			$output['img_upload_limit'] = $limit;
		}
		

		return apply_filters( 'imsl_options_validate', $output, $input, $defaults );
	}
endif;

if ( ! function_exists( 'imsl_unique_identifyer_admin_notices' ) ) :
	function imsl_unique_identifyer_admin_notices() {
		 settings_errors( 'imsl_img_upload_limit' );
	}
endif;
add_action( 'admin_notices', 'imsl_unique_identifyer_admin_notices' );