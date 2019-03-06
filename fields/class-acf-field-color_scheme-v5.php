<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('acf_field_color_scheme') ) :


class acf_field_color_scheme extends acf_field {

	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/

	function __construct( $settings ) {

		// vars
		$this->name = 'color_scheme';
		$this->label = __("Color Scheme",'acf');
		$this->category = 'choice';
		$this->defaults = array(
			'choices'			=> array(),
			'default_value'		=> '',
			'other_choice'		=> 0,
			'save_other_choice'	=> 0,
			'allow_null' 		=> 0,
			'return_format'		=> 'value'
		);

		// do not delete!
    	parent::__construct();

	}

	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/

	function render_field( $field ) {

		// vars
		$i = 0;
		$e = '';
		$ul = array(
			'class'				=> 'acf-radio-list',
			'data-allow_null'	=> $field['allow_null'],
			'data-other_choice'	=> $field['other_choice']
		);
		$display_acfcs_warning = false;
		$rejected_colors = array();


		// select value
		$checked = '';
		$value = strval($field['value']);


		// selected choice
		if( isset($field['choices'][ $value ]) ) {

			$checked = $value;

		// custom choice
		} elseif( $field['other_choice'] && $value !== '' ) {

			$checked = 'other';

		// allow null
		} elseif( $field['allow_null'] ) {

			// do nothing

		// select first input by default
		} else {

			$checked = key($field['choices']);

		}


		// ensure $checked is a string (could be an int)
		$checked = strval($checked);


		// bail early if no choices
		if( empty($field['choices']) ) return;


		// hiden input
		$e .= acf_get_hidden_input( array('name' => $field['name']) );


		// open
		$e .= '<ul ' . acf_esc_attr($ul) . '>';


		// print_r($field['value']);
		// foreach choices
		foreach( $field['choices'] as $value => $label ) {

			// ensure value is a string
			$value = strval($value);
			$class = '';

			// increase counter
			$i++;

			// vars
			$atts = array(
				'type'	=> 'radio',
				'id'	=> $field['id'],
				'name'	=> $field['name'],
				'value'	=> $value
			);

			// checked
			if( $value === $checked ) {

				$atts['checked'] = 'checked';
				$class = ' class="selected"';

			}


			// deisabled
			if( isset($field['disabled']) && acf_in_array($value, $field['disabled']) ) {

				$atts['disabled'] = 'disabled';

			}


			// id (use crounter for each input)
			if( $i > 1 ) {

				$atts['id'] .= '-' . $value;

			}

			// append


			$colors_array = explode(',', $label);
			$cleaned_colors = array();
			$final_colors = array();

			// checks input is clean and in hex
			foreach($colors_array as $color) {

				$color = str_replace('#', '', $color);
				$color = str_replace(' ', '', $color);
				if (ctype_xdigit($color)) {
					array_push($cleaned_colors, $color);
				} else {
					array_push($rejected_colors, $color);
						$display_acfcs_warning = true;
				}
			};
			// puts hashtag back
			foreach($cleaned_colors as $color) {
				$final_color = '#' . $color;
				array_push($final_colors, $final_color);
			}
			$colors='';
			foreach ($final_colors as $value) {
				$colors .= '<span style="background-color:' . $value . ';"></span>' ;
				// code...
			}

			$colorscheme_name = ucfirst($atts['value']);
			$e .= '<li>' . $colorscheme_name . '<br><label' . $class . '><input ' . acf_esc_attr( $atts ) . '/>' . '<div id="acfcs_colors_group">' . $colors . '</div>' . '</label></li>';

			$colors = false;


		}

		$acfcs_warning = "<div class='acfcs_warning'>Certain colors (" . implode(', ', $rejected_colors) . ") were not considered valid hexadecimal digits. Go to your <a href='" . get_home_url() . "/wp-admin/edit.php?post_type=acf-field-group" . "'>custom fields page </a> to fix it.</div><br>";

		if ($display_acfcs_warning) {
			print_r($acfcs_warning);
		}

		// close
		$e .= '</ul>';


		// return
		echo $e;

	}
	function render_field_settings( $field ) {

			// encode choices (convert from array)
			$field['choices'] = acf_encode_choices($field['choices']);


			// choices
			acf_render_field_setting( $field, array(
				'label'			=> __('Choices','acf'),
				'instructions'	=> __('Enter each color scheme on a new line.','acf') . '<br /><br />' . __('Specify both a value and label like this:','acf'). '<br /><br />' . __('blue : #004AEA, #002557, ...','acf'),
				'type'			=> 'textarea',
				'name'			=> 'choices',
			));




			// return_format
			acf_render_field_setting( $field, array(
				'label'			=> __('Return Value','acf'),
				'instructions'	=> __('Specify the returned value on front end','acf'),
				'type'			=> 'radio',
				'name'			=> 'return_format',
				'choices'		=> array(
					'value'			=> __('Value','acf'),
					'label'			=> __('Label','acf'),
					'array'			=> __('Both (Array)','acf')
				)
			));

			// default_value
			acf_render_field_setting( $field, array(
				'label'			=> __('Default Value','acf'),
				'instructions'	=> __('Appears when creating a new post','acf'),
				'type'			=> 'text',
				'name'			=> 'default_value',
			));

		}


		/*
		*  update_field()
		*
		*  This filter is appied to the $field before it is saved to the database
		*
		*  @type	filter
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$field - the field array holding all the field options
		*  @param	$post_id - the field group ID (post_type = acf)
		*
		*  @return	$field - the modified field
		*/

		function update_field( $field ) {

			// decode choices (convert to array)
			$field['choices'] = acf_decode_choices($field['choices']);


			// return
			return $field;
		}


		/*
		*  update_value()
		*
		*  This filter is appied to the $value before it is updated in the db
		*
		*  @type	filter
		*  @since	3.6
		*  @date	23/01/13
		*  @todo	Fix bug where $field was found via json and has no ID
		*
		*  @param	$value - the value which will be saved in the database
		*  @param	$post_id - the $post_id of which the value will be saved
		*  @param	$field - the field array holding all the field options
		*
		*  @return	$value - the modified value
		*/

		function update_value( $value, $post_id, $field ) {

			// bail early if no value (allow 0 to be saved)
			if( !$value && !is_numeric($value) ) return $value;


			// save_other_choice
			if( $field['save_other_choice'] ) {

				// value isn't in choices yet
				if( !isset($field['choices'][ $value ]) ) {

					// get raw $field (may have been changed via repeater field)
					// if field is local, it won't have an ID
					$selector = $field['ID'] ? $field['ID'] : $field['key'];
					$field = acf_get_field( $selector, true );


					// bail early if no ID (JSON only)
					if( !$field['ID'] ) return $value;


					// unslash (fixes serialize single quote issue)
					$value = wp_unslash($value);


					// sanitize (remove tags)
					$value = sanitize_text_field($value);


					// update $field
					$field['choices'][ $value ] = $value;


					// save
					acf_update_field( $field );

				}

			}


			// return
			return $value;
		}


		/*
		*  load_value()
		*
		*  This filter is appied to the $value after it is loaded from the db
		*
		*  @type	filter
		*  @since	5.2.9
		*  @date	23/01/13
		*
		*  @param	$value - the value found in the database
		*  @param	$post_id - the $post_id from which the value was loaded from
		*  @param	$field - the field array holding all the field options
		*
		*  @return	$value - the value to be saved in te database
		*/

		function load_value( $value, $post_id, $field ) {

			// must be single value
			if( is_array($value) ) {

				$value = array_pop($value);

			}


			// return
			return $value;

		}


		/*
		*  translate_field
		*
		*  This function will translate field settings
		*
		*  @type	function
		*  @date	8/03/2016
		*  @since	5.3.2
		*
		*  @param	$field (array)
		*  @return	$field
		*/

		function translate_field( $field ) {

			return acf_get_field_type('select')->translate_field( $field );

		}


		/*
		*  format_value()
		*
		*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
		*
		*  @type	filter
		*  @since	3.6
		*  @date	23/01/13
		*
		*  @param	$value (mixed) the value which was loaded from the database
		*  @param	$post_id (mixed) the $post_id from which the value was loaded
		*  @param	$field (array) the field array holding all the field options
		*
		*  @return	$value (mixed) the modified value
		*/

		function format_value( $value, $post_id, $field ) {

			return acf_get_field_type('select')->format_value( $value, $post_id, $field );

		}




}


// initialize
new acf_field_color_scheme( $this->settings );


// class_exists check
endif;

?>
