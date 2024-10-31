<?php 

if( $pagenow == 'plugins.php' )
	require_once( PTD_ADMIN_DIR . 'plugins_page.php' );
elseif( $pagenow == 'update.php' )
	add_filter('install_plugin_complete_actions', 'add_test_link_after_install', 10, 3 );
else
	require_once( PTD_ADMIN_DIR . 'options.php' );

function add_test_link_after_install( $install_actions, $ptdVar, $ptd_option ) {
	global $ptd_options;
	if( isset( $install_actions['activate_plugin'] ) ) {//enable testing only if plugin is installed properly (if it's not installed properly, the "Activate Plugin" link will not be displayed).
		$test_link = PTD_PLUGIN_SETTINGS_URL . '&test_after_install=' . $ptd_option . '&updated=true&' . '_wpnonce=' . wp_create_nonce ('test_first');
		$test_link_txt = __( 'Test Plugin', 'plugin_test_drive' );
		$test_link = '<a href="' . $test_link . '" style="color:#D54E21">' . $test_link_txt . '</a>';
		array_unshift( $install_actions, $test_link );
	}
	return $install_actions;
}

//update option without calling sanitize function
function ptd_update_option() {
	global $wpdb, $ptd_options;
	$wpdb->update( $wpdb->options, array( 'option_value' => serialize( $ptd_options ) ), array( 'option_name' => 'plugin_test_drive' ) );
}




add_action( 'admin_init', 'my_plugin_admin_init' );
function my_plugin_admin_init(){
	wp_enqueue_style ( 'ptd_style', PTD_ADMIN_URL . 'css/style.css' );
}


// 
wp_enqueue_script ( 'ptd_script', PTD_ADMIN_URL . 'js/script.js','jquery','1.0.0', true );
wp_localize_script( 'ptd_script', '_ptdL10n', array(
		'ip' => __( 'IP address', 'plugin_test_drive' ),
		'user' => __( 'user name', 'plugin_test_drive' ),
		'set' => __( 'set to your', 'plugin_test_drive' ),
		'valid' => __( 'Please Enter a valid IP Address', 'plugin_test_drive' ),		
		'mandatory' => __( 'Tester key is mandatory', 'plugin_test_drive' ),
		'check' => __( 'Click to check all', 'plugin_test_drive' ),
		'uncheck' => __( 'Click to uncheck all', 'plugin_test_drive' ),
		'CurrentUserName' => $curr_user,
		'CurrentIP' => $curr_ip ) );
?>