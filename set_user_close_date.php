<?php
//to finish this off correctly, the solution may be found here: 
//http://codex.wordpress.org/AJAX_in_Plugins


/**
 * Template Name: Account MSG Plugin
 *
 * @package mintthemes
 * @since mintthemes 1.0
 */
 
//get user info
$current_user = $_POST['userid'];

//set newdate variable to be the current date:
$newdate = date('Y\-m\-d');

//set the close date for the account message in this user's table
update_user_option( $current_user, 'mintthemes_account_msg_date', $newdate, $global ); 

$users_close_date = strtotime(get_user_option( 'mintthemes_account_msg_date', get_current_user_id() ));

echo $users_close_date;
?>