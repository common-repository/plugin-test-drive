<?php 

/*
Plugin Name: Plugin Test Drive
Description: Test any wordpress plugin before activating it
Author: Omer Greenwald
Version: 1.3

variable prefix reference : tp=Tested Plugin
*/

define( 'PTD_PLUGIN_FOLDER_NAME', plugin_basename( dirname( __FILE__ ) ) . '/' );
define( 'PTD_PLUGIN_FILE', PTD_PLUGIN_FOLDER_NAME . basename( __FILE__ ) );
define( 'PTD_ADMIN_DIR', ABSPATH . 'wp-content/plugins/' . PTD_PLUGIN_FOLDER_NAME . 'admin' . '/' );
define( 'PTD_ADMIN_URL', WP_PLUGIN_URL . '/' . PTD_PLUGIN_FOLDER_NAME . 'admin' . '/' );
define( 'PTD_PLUGIN_SETTINGS_PAGE', 'options-general.php?page=ptd_options_page' );
define( 'PTD_PLUGIN_SETTINGS_URL', admin_url( PTD_PLUGIN_SETTINGS_PAGE ) );

global $ptd_options, $curr_ip, $curr_user, $tester_key_confirmed;
$ptd_options = get_option( 'plugin_test_drive' );
require_once( ABSPATH . '/wp-includes/pluggable.php' );

load_plugin_textdomain( 'plugin_test_drive', false, PTD_PLUGIN_FOLDER_NAME . 'admin/languages/' );

function ptd_reset() {//must clear tested plugins list in db in case user edits tested plugins before reactivation of ptd
	if( strpos( $_SERVER["REQUEST_URI"], 'update.php' ) === false ) {//clear only if ptd is de/activated by the user and not by auto upgrade process
		global $ptd_options, $curr_ip;
		$ptd_options = array(); 
		$ptd_options['tester_key_val'] = $curr_ip;//create initial option
		update_option( 'plugin_test_drive', $ptd_options );
	}
}

function ptd_activate() {
	global $ptd_options, $wp_version;
	ptd_reset();
	if( version_compare( $wp_version, '2.8', '<' ) ) {
		deactivate_plugins( PTD_PLUGIN_FILE );
		wp_die( 'Sorry, this plugin requires WordPress 2.8 or above. Please make sure to deactivate it' );
	}
}
register_activation_hook( PTD_PLUGIN_FILE , 'ptd_activate' );

function ptd_deactivate() {
	global $ptd_options;
	foreach( array_keys( $ptd_options ) as $tp_file ) {
		if( substr( $tp_file, -4 ) == '.php' && $ptd_options[$tp_file]['is_tested'] == '1' )
			do_action( 'deactivate_' . trim( $tp_file ) );
	}
	ptd_reset();
}
register_deactivation_hook( PTD_PLUGIN_FILE ,'ptd_deactivate' );

function ptd_init() {
	register_setting( 'ptd_settings', 'plugin_test_drive', 'ptd_options_sanitize' );
}

function ptd_add_options_page() {	
	add_options_page( 'plugin_test_drive', 'Plugin Test Drive', 'manage_options', 'ptd_options_page', 'ptd_generate_options' );
}

function get_curr_user() { 	
	global $current_user;	
	if( !$current_user ) 
	wp_get_current_user();
	
	return $current_user->user_login;
}


$curr_ip = get_ip_address();
$curr_user =  get_curr_user();


if( is_admin() ) {
	if( $pagenow != 'plugins.php' )
		add_action( 'admin_init', 'ptd_init' );
	add_action( 'admin_menu', 'ptd_add_options_page' );
	if( ( isset( $_GET['page'] ) && $_GET['page'] == 'ptd_options_page' ) || $pagenow == 'options.php' || $pagenow == 'plugins.php' || $pagenow == 'update.php' )
		require_once( PTD_ADMIN_DIR . 'main.php' );
}

function get_ip_address() {
	if( !empty( $_SERVER['HTTP_CLIENT_IP'] ) )
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	elseif( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else
		$ip = $_SERVER['REMOTE_ADDR'];
	return $ip;
}

$tester_key_confirmed = ( $ptd_options['tester_key_method'] == '0' && $ptd_options['tester_key_val'] == $curr_ip ) || ( $ptd_options['tester_key_method'] == '1' && $ptd_options['tester_key_val'] == $curr_user ) ? true : false;

//load the plugins
if( $tester_key_confirmed ) {
	foreach( $ptd_options as $ptd_option => $ptd_val ) {
		if( '.php' === substr( $ptd_option, -4, 4 ) && $ptd_options[$ptd_option]['is_tested'] == '1' )
		{
			$ptd_plugin_path = WP_PLUGIN_DIR . '/' . $ptd_option;		
			if( !file_exists ( $ptd_plugin_path ) ) {//if plugin is selected for testing and then deleted (outside of wp), remove it from db option
				unset( $ptd_options[$ptd_option] );
				update_option( 'plugin_test_drive', $ptd_options );
				continue;
			}
			require_once( $ptd_plugin_path );//this must not be wrapped inside a function
		}
	}
}

?>