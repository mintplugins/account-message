<?php
/*
Plugin Name: Account Message
Plugin URI: http://mintplugins.com
Description: This plugin gives you a template tag which you can put on any archive page isotopes functionality 
Version: 1.0
Author: Phil Johnston
Author URI: http://mintplugins.com
License: GPL2
*/

/*  Copyright 2012  Phil Johnston  (email : phil@mintplugins.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* To use this function put the following on any archive page
if ( function_exists( 'mintthemes_account_msg' ) ): 
	mintthemes_account_msg(); 
endif;
*/


/**
 * Enqueue scripts and styles
 */
if ( ! function_exists( 'mintthemes_account_msg_scripts' ) ):
	function mintthemes_account_msg_scripts() {
		wp_enqueue_style( 'mintthemes_account_msg_css', plugins_url() . '/account-message/css/style.css' );
		wp_enqueue_script( 'mintthemes_account_msg_js', plugins_url( '/js/account_message.js', __FILE__ ) );
	}
endif; //mintthemes_account_msg_scripts
add_action( 'wp_enqueue_scripts', 'mintthemes_account_msg_scripts' );

/**
 * Hook Function for Account Message
 */
if ( ! function_exists( 'mintthemes_account_msg' ) ):
	function mintthemes_account_msg(){
		if (mintthemes_account_msg_get_plugin_option( 'show-hide' ) != "1"){//plugin option to show or hide
			
			//compare dates
			$users_close_date = strtotime(get_user_option( 'mintthemes_account_msg_date', get_current_user_id() ));
			$plugin_message_date = strtotime(mintthemes_account_msg_get_plugin_option( 'mintthemes_account_msg_newest_message_date' ));
			
			if ( $users_close_date < $plugin_message_date){//if there is a new message
				global $current_user;
				get_currentuserinfo();
								
				echo ('<div id="mintthemes_account_msg_box" class="box">
					<div class="above">
						<h2>' . mintthemes_account_msg_get_plugin_option( 'the_title' ) .'</h2>
						<p>' . mintthemes_account_msg_get_plugin_option( 'the_message' ) . '</p>
						
						
						
						<form id="mintthemes_account_msg_close_form">
							<input type="hidden" name="userid" value="' . $current_user->ID .'"/>
							<input type="hidden" name="date" value="' . date('Y\-m\-d') . '"/>
							<input type="submit" id="mintthemes_account_msg_close" class="close ss-icon" value="Close" /> 
						</form>
					</div>
					<span class="ss-icon ss-alert icon-background"></span>
				</div>');
			}
		}
	}
endif; //mintthemes_account_msg

/**
 * Admin Page and options
 */ 

function mintthemes_account_msg_plugin_options_init() {
	register_setting(
		'mintthemes_account_msg_options',
		'mintthemes_account_msg_options',
		'mintthemes_account_msg_plugin_options_validate'
	);
	//
	add_settings_section(
		'settings',
		__( 'Settings', 'mintthemes_account_msg' ),
		'__return_false',
		'mintthemes_account_msg_options'
	);
	
	//
	add_settings_field(
		'the_title',
		__( 'Account Message Title', 'mintthemes_account_msg' ), 
		'mintthemes_account_msg_settings_field_textbox',
		'mintthemes_account_msg_options',
		'settings',
		array(
			'name'        => 'the_title',
			'value'       => mintthemes_account_msg_get_plugin_option( 'the_title' ),
			'description' => __( 'The title you would like to display', 'mintthemes_account_msg' )
		)
	);
	
	//
	add_settings_field(
		'the_message',
		__( 'Account Message', 'mintthemes_account_msg' ), 
		'mintthemes_account_msg_settings_field_textarea',
		'mintthemes_account_msg_options',
		'settings',
		array(
			'name'        => 'the_message',
			'value'       => mintthemes_account_msg_get_plugin_option( 'the_message' ),
			'description' => __( 'The message you would like to display', 'mintthemes_account_msg' )
		)
	);
	
	//
	add_settings_field(
		'mintthemes_account_msg_newest_message_date',
		__( 'Message Date', 'mintthemes_account_msg' ), 
		'mintthemes_account_msg_settings_field_textbox',
		'mintthemes_account_msg_options',
		'settings',
		array(
			'name'        => 'mintthemes_account_msg_newest_message_date',
			'value'       => mintthemes_account_msg_get_plugin_option( 'mintthemes_account_msg_newest_message_date' ),
			'description' => __( 'Date Format: YEAR-MM-DD (EG: 2012-08-28) The date of the message you would like to display. If you make it for the present date, it will show up for any users that had closed the message previously to the present.', 'mintthemes_account_msg' )
		)
	);
	
	//
	add_settings_field(
		'show-hide',
		__( 'Show or Hide', 'mintthemes_account_msg' ), 
		'mintthemes_account_msg_settings_field_select',
		'mintthemes_account_msg_options',
		'settings',
		array(
			'name'        => 'show-hide',
			'value'       => mintthemes_account_msg_get_plugin_option( 'show-hide' ),
			'options'     => array('show','hide'),
			'description' => __( 'Show or Hide the Account Message', 'mintthemes_account_msg' )
		)
	);
	

	
}
add_action( 'admin_init', 'mintthemes_account_msg_plugin_options_init' );

/**
 * Change the capability required to save the 'mintthemes_account_msg_options' options group.
 *
 * @see mintthemes_account_msg_plugin_options_init() First parameter to register_setting() is the name of the options group.
 * @see mintthemes_account_msg_plugin_options_add_page() The manage_options capability is used for viewing the page.
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function mintthemes_account_msg_option_page_capability( $capability ) {
	return 'manage_options';
}
add_filter( 'option_page_capability_mintthemes_account_msg_options', 'mintthemes_account_msg_option_page_capability' );

/**
 * Add our plugin options page to the admin menu.
 *
 * This function is attached to the admin_menu action hook.
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_plugin_options_add_page() {
	 add_options_page(
		__( 'Account Message Options', 'mintthemes_account_msg' ),
		__( 'Account Message Options', 'mintthemes_account_msg' ),
		'manage_options',
		'mintthemes_account_msg_options',
		'mintthemes_account_msg_plugin_options_render_page'
	);
	
}
add_action( 'admin_menu', 'mintthemes_account_msg_plugin_options_add_page' );

/**
 * Returns the options array for Account Message.
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_get_plugin_options() {
	$saved = (array) get_option( 'mintthemes_account_msg_options' );
	
	$defaults = array(
		'the_title' 	=> '',
		'the_message' 	=> '',
		'show-hide' 	=> '',
		'mintthemes_account_msg_newest_message_date' => ''
	);

	$defaults = apply_filters( 'mintthemes_account_msg_default_plugin_options', $defaults );

	$options = wp_parse_args( $saved, $defaults );
	$options = array_intersect_key( $options, $defaults );

	return $options;
}

/**
 * Get a single plugin option
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_get_plugin_option( $key ) {
	$options = mintthemes_account_msg_get_plugin_options();
	
	if ( isset( $options[ $key ] ) )
		return $options[ $key ];
		
	return false;
}

/**
 * Renders the Theme Options administration screen.
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_plugin_options_render_page() {
	
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf( __( 'Account Message Options', 'mintthemes_account_msg' ), 'mintthemes_account_msg' ); ?></h2>
		<?php settings_errors(); ?>

		<form action="options.php" method="post">
			<?php
				settings_fields( 'mintthemes_account_msg_options' );
				do_settings_sections( 'mintthemes_account_msg_options' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see mintthemes_account_msg_plugin_options_init()
 * @todo set up Reset Options action
 *
 * @param array $input Unknown values.
 * @return array Sanitized plugin options ready to be stored in the database.
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_plugin_options_validate( $input ) {
	$output = array();
		
	if ( isset ( $input[ 'the_title' ] ) )
		$output[ 'the_title' ] = esc_attr( $input[ 'the_title' ] );
		
	if ( isset ( $input[ 'the_message' ] ) )
		$output[ 'the_message' ] = esc_attr( $input[ 'the_message' ] );
		
	if ( isset ( $input[ 'mintthemes_account_msg_newest_message_date' ] ) )
		$output[ 'mintthemes_account_msg_newest_message_date' ] = esc_attr( $input[ 'mintthemes_account_msg_newest_message_date' ] );
		
	if ( $input[ 'show-hide' ] == 0 || array_key_exists( $input[ 'show-hide' ], mintthemes_account_msg_get_categories() ) )
		$output[ 'show-hide' ] = $input[ 'show-hide' ];
		
		
	
	$output = wp_parse_args( $output, mintthemes_account_msg_get_plugin_options() );	
		
	return apply_filters( 'mintthemes_account_msg_plugin_options_validate', $output, $input );
}

/* Fields ***************************************************************/
 
/**
 * Number Field
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_settings_field_number( $args = array() ) {
	$defaults = array(
		'menu'        => '', 
		'min'         => 1,
		'max'         => 100,
		'step'        => 1,
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_account_msg_options[%s]', $name ) );
?>
	<label for="<?php echo esc_attr( $id ); ?>">
		<input type="number" min="<?php echo absint( $min ); ?>" max="<?php echo absint( $max ); ?>" step="<?php echo absint( $step ); ?>" name="<?php echo $name; ?>" id="<?php echo $id ?>" value="<?php echo esc_attr( $value ); ?>" />
		<?php echo $description; ?>
	</label>
<?php
} 

/**
 * Textarea Field
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_settings_field_textarea( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_account_msg_options[%s]', $name ) );
?>
	<label for="<?php echo $id; ?>">
		<textarea name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="code large-text" rows="3" cols="30"><?php echo esc_textarea( $value ); ?></textarea>
		<br />
		<?php echo $description; ?>
	</label>
<?php
} 

/**
 * Image Upload Field
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_settings_field_image_upload( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_account_msg_options[%s]', $name ) );
?>
	<label for="<?php echo $id; ?>">
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>">
        <input id="upload_image_button" type="button" value="<?php echo __( 'Upload Image', 'mintthemes_account_msg' ); ?>" />
		<br /><?php echo $description; ?>
	</label>
<?php
} 

/**
 * Textbox Field
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_settings_field_textbox( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_account_msg_options[%s]', $name ) );
?>
	<label for="<?php echo $id; ?>">
		<input type="text" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>">
		<br /><?php echo $description; ?>
	</label>
<?php
} 

/**
 * Single Checkbox Field
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_settings_field_checkbox_single( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'compare'     => 'on',
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_account_msg_options[%s]', $name ) );
?>
	<label for="<?php echo esc_attr( $id ); ?>">
		<input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $compare, $value ); ?>>
		<?php echo $description; ?>
	</label>
<?php
} 

/**
 * Radio Field
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_settings_field_radio( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'options'     => array(),
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_account_msg_options[%s]', $name ) );
?>
	<?php foreach ( $options as $option_id => $option_label ) : ?>
	<label title="<?php echo esc_attr( $option_label ); ?>">
		<input type="radio" name="<?php echo $name; ?>" value="<?php echo $option_id; ?>" <?php checked( $option_id, $value ); ?>>
		<?php echo esc_attr( $option_label ); ?>
	</label>
		<br />
	<?php endforeach; ?>
<?php
}

/**
 * Select Field
 *
 * @since Account Message 1.0
 */
function mintthemes_account_msg_settings_field_select( $args = array() ) {
	$defaults = array(
		'name'        => '',
		'value'       => '',
		'options'     => array(),
		'description' => ''
	);
	
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	
	$id   = esc_attr( $name );
	$name = esc_attr( sprintf( 'mintthemes_account_msg_options[%s]', $name ) );
?>
	<label for="<?php echo $id; ?>">
		<select name="<?php echo $name; ?>">
			<?php foreach ( $options as $option_id => $option_label ) : ?>
			<option value="<?php echo esc_attr( $option_id ); ?>" <?php selected( $option_id, $value ); ?>>
				<?php echo esc_attr( $option_label ); ?>
			</option>
			<?php endforeach; ?>
		</select>
		<?php echo $description; ?>
	</label>
<?php
}

/* Helpers ***************************************************************/

function mintthemes_account_msg_get_categories() {
	$output = array();
	$terms  = get_terms( array( 'category' ), array( 'hide_empty' => 0 ) );
	
	foreach ( $terms as $term ) {
		$output[ $term->term_id ] = $term->name;
	}
	
	return $output;
}
