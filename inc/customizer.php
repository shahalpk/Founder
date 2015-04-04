<?php

/* Add customizer panels, sections, settings, and controls */
add_action( 'customize_register', 'ct_founder_add_customizer_content' );

function ct_founder_add_customizer_content( $wp_customize ) {

	/***** Add PostMessage Support *****/
	
	// Add postMessage support for site title and description.
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	
	/***** Add Custom Controls *****/

	// create url input control
	class ct_founder_url_input_control extends WP_Customize_Control {
		// create new type called 'url'
		public $type = 'url';
		// the content to be output in the Customizer
		public function render_content() {
			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<input type="url" <?php $this->link(); ?> value="<?php echo esc_url_raw( $this->value() ); ?>" />
			</label>
		<?php
		}
	}

	// create multi-checkbox/select control
	class ct_founder_multi_checkbox_control extends WP_Customize_Control {
		public $type = 'multi-checkbox';

		public function render_content() {

			if ( empty( $this->choices ) )
				return;
			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<select id="comment-display-control" <?php $this->link(); ?> multiple="multiple" style="height: 100%;">
					<?php
					foreach ( $this->choices as $value => $label ) {
						$selected = ( in_array( $value, $this->value() ) ) ? selected( 1, 1, false ) : '';
						echo '<option value="' . esc_attr( $value ) . '"' . $selected . '>' . $label . '</option>';
					}
					?>
				</select>
			</label>
		<?php }
	}

	// create textarea control
	class ct_founder_textarea_control extends WP_Customize_Control {
		public $type = 'textarea';

		public function render_content() {
			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<textarea rows="8" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
			</label>
		<?php
		}
	}

	/***** Logo Upload *****/

	// section
	$wp_customize->add_section( 'ct_founder_logo_upload', array(
		'title'      => __( 'Logo Upload', 'founder' ),
		'priority'   => 30,
		'capability' => 'edit_theme_options'
	) );
	// setting
	$wp_customize->add_setting( 'logo_upload', array(
		'default'           => '',
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'postMessage'
	) );
	// control
	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize, 'logo_image', array(
			'label'    => __( 'Upload custom logo.', 'founder' ),
			'section'  => 'ct_founder_logo_upload',
			'settings' => 'logo_upload',
		)
	) );

	/***** Social Media Icons *****/

	// get the social sites array
	$social_sites = ct_founder_social_array();

	// set a priority used to order the social sites
	$priority = 5;

	// section
	$wp_customize->add_section( 'ct_founder_social_media_icons', array(
		'title'          => __('Social Media Icons', 'founder'),
		'priority'       => 25,
	) );

	// create a setting and control for each social site
	foreach( $social_sites as $social_site => $value ) {
		// if email icon
		if( $social_site == 'email' ) {
			// setting
			$wp_customize->add_setting( "$social_site", array(
				'type'              => 'theme_mod',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'ct_founder_sanitize_email',
				'transport'         => 'postMessage'
			) );
			// control
			$wp_customize->add_control( $social_site, array(
				'label'   => $social_site . ' ' . __('address:', 'founder' ),
				'section' => 'ct_founder_social_media_icons',
				'priority'=> $priority,
			) );
		} else {
			// setting
			$wp_customize->add_setting( $social_site, array(
				'type'              => 'theme_mod',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
				'transport'         => 'postMessage'
			) );
			// control
			$wp_customize->add_control( new ct_founder_url_input_control(
				$wp_customize, $social_site, array(
					'label'   => $social_site . ' ' . __('url:', 'founder' ),
					'section' => 'ct_founder_social_media_icons',
					'priority'=> $priority,
				)
			) );
		}
		// increment the priority for next site
		$priority = $priority + 5;
	}

	/***** Search Bar *****/

	// section
	$wp_customize->add_section( 'ct_founder_search_bar', array(
		'title'      => __( 'Search Bar', 'founder' ),
		'priority'   => 40,
		'capability' => 'edit_theme_options'
	) );
	// setting
	$wp_customize->add_setting( 'search_bar', array(
		'default'           => 'show',
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ct_founder_sanitize_all_show_hide_settings'
	) );
	// control
	$wp_customize->add_control( 'search_bar', array(
		'type' => 'radio',
		'label' => __('Show search bar at top of site?', 'founder'),
		'section' => 'ct_founder_search_bar',
		'setting' => 'search_bar',
		'choices' => array(
			'show' => __('Show', 'founder'),
			'hide' => __('Hide', 'founder')
		),
	) );

	/***** Comment Display *****/

	// section
	$wp_customize->add_section( 'ct_founder_comments_display', array(
		'title'      => __( 'Comment Display', 'founder' ),
		'priority'   => 65,
		'capability' => 'edit_theme_options'
	) );
	// setting
	$wp_customize->add_setting( 'comments_display', array(
		'default'           => array('post','page','attachment','none'),
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'ct_founder_sanitize_comments_setting',
	) );
	// control
	$wp_customize->add_control( new ct_founder_Multi_Checkbox_Control(
		$wp_customize, 'comments_display', array(
			'label'          => __( 'Show comments on:', 'founder' ),
			'section'        => 'ct_founder_comments_display',
			'settings'       => 'comments_display',
			'type'           => 'multi-checkbox',
			'choices'        => array(
				'post'   => __('Posts', 'founder'),
				'page'  => __('Pages', 'founder'),
				'attachment'  => __('Attachments', 'founder'),
				'none'  => __('Do not show', 'founder')
			)
		)
	) );

	/***** Custom CSS *****/

	// section
	$wp_customize->add_section( 'founder_custom_css', array(
		'title'      => __( 'Custom CSS', 'founder' ),
		'priority'   => 70,
		'capability' => 'edit_theme_options'
	) );
	// setting
	$wp_customize->add_setting( 'custom_css', array(
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'wp_filter_nohtml_kses',
	) );
	// control
	$wp_customize->add_control( new ct_founder_textarea_control(
		$wp_customize, 'custom_css', array(
			'label'          => __( 'Add Custom CSS Here:', 'founder' ),
			'section'        => 'founder_custom_css',
			'settings'       => 'custom_css',
		)
	) );
}

/***** Custom Sanitization Functions *****/

/*
 * Sanitize settings with show/hide as options
 * Used in: search bar
 */
function ct_founder_sanitize_all_show_hide_settings($input){
	// create array of valid values
	$valid = array(
		'show' => __('Show', 'founder'),
		'hide' => __('Hide', 'founder')
	);
	// if returned data is in array use it, else return nothing
	if ( array_key_exists( $input, $valid ) ) {
		return $input;
	} else {
		return '';
	}
}

/*
 * sanitize email address
 * Used in: Social Media Icons
 */
function ct_founder_sanitize_email( $input ) {

	return sanitize_email( $input );
}

// sanitize comment display multi-check
function ct_founder_sanitize_comments_setting($input){

	// valid data
	$valid = array(
		'post'   => __('Posts', 'founder'),
		'page'  => __('Pages', 'founder'),
		'attachment'  => __('Attachments', 'founder'),
		'none'  => __('Do not show', 'founder')
	);

	// loop through array
	foreach( $input as $selection ) {

		// if it's in the valid data, return it
		if ( array_key_exists( $selection, $valid ) ) {
			return $input;
		} else {
			return '';
		}
	}
}

/***** Helper Functions *****/

function ct_founder_customize_preview_js() {

	$content = "<script>jQuery('#customize-info').append('<div class=\"upgrades-ad\"><a href=\"https://www.competethemes.com/founder-pro/\" target=\"_blank\">View the Founder Pro Upgrade <span>&rarr;</span></a></div>')</script>";
	echo apply_filters('ct_founder_customizer_ad', $content);
}
add_action('customize_controls_print_footer_scripts', 'ct_founder_customize_preview_js');