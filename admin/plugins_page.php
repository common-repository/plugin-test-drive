<?php 

//add notes below tested plugins
function tp_plugin_notes( $tp_file, $tp_data ) {
	global $ptd_options, $tester_key_confirmed, $curr_ip, $curr_user;
	$remove_test_url = admin_url ( 'plugins.php?remove_test=' . $tp_file );
	$remove_test_url = wp_nonce_url( $remove_test_url, 'remove_test' );
	if( $ptd_options['tester_key_method'] == 0 ) {
		$tester_key_method = __( 'IP address', 'plugin_test_drive' );
		$curr_key_val = $curr_ip;
	} else {
		$tester_key_method = __( 'user name', 'plugin_test_drive' );
		$curr_key_val = $curr_user;
	}
	$tester_key_text = $tester_key_confirmed ? (__( '(<u>your</u> ', 'plugin_test_drive' ) . $tester_key_method . ')') : (__( 'However, <u>your</u> ', 'plugin_test_drive' ) . $tester_key_method . __( ' is ', 'plugin_test_drive' ) . $curr_key_val . '.');
	echo '<tr class="plugin-tested-tr"><td colspan="3" class="plugin-tested">
	<div class="plugin-tested-message">"' . $tp_data[Name] . '"' . __( ' is loaded by ', 'plugin_test_drive' ) . '<a href="' . PTD_PLUGIN_SETTINGS_URL . '">Plugin Test Drive</a>' . __( ' for ', 'plugin_test_drive') . $tester_key_method . ' <span class="name">' . $ptd_options['tester_key_val'] . '</span> ' . __( 'only. ', 'plugin_test_drive') . $tester_key_text . '<a href="' . $remove_test_url . '"><br /><br />' . __( 'Stop testing ', 'plugin_test_drive' ) . $tp_data[Name] . '</a></div></td></tr>';
}

//remove activate/delete/edit links for tested plugins
function tp_action_links( $tp_links, $tp_file ) {
	global $ptd_options;
	if( isset( $ptd_options[$tp_file] ) && $ptd_options[$tp_file]['is_tested'] == '1' ) {
		foreach ( $tp_links as $key => $value ) {//for backwards compatibility (<3), search substr in values and not by keys
		   if( substr_count($value, 'Activate') > 0 || substr_count($value, 'Delete') > 0 || substr_count($value, 'Edit') > 0 )
				unset( $tp_links[$key] );
		}
		$tp_links = array_values( $tp_links );
		add_action( "after_plugin_row_$tp_file", 'tp_plugin_notes', 10, 2 );
	}
	if( $tp_file == PTD_PLUGIN_FILE ) {
		$settings_link = __( '<a href="' . PTD_PLUGIN_SETTINGS_URL . '">Settings</a>', 'plugin_test_drive' );
		array_unshift( $tp_links, $settings_link );
	}
	return $tp_links;
}

//prevent updates for tested plugins - for wp 3.0 +
function ptd_prevent_tested_plugin_updates( $r, $url ) {
	global $ptd_options;
	if( 0 === strpos( $url, 'http://api.wordpress.org/plugins/update-check/' ) ) {
		$tp_objects = unserialize( $r['body']['plugins'] );
		foreach( $ptd_options as $ptd_option => $ptd_val ) {
			if( substr( $ptd_option,-4 ) == '.php' && isset( $ptd_options[$ptd_option]['is_tested'] ) )
				unset( $tp_objects->plugins[$ptd_option] );
		}
		$r['body']['plugins'] = serialize( $tp_objects );
	}
	return $r;
}













if( intval( $wp_version ) >= 3 ) {
	set_site_transient( 'update_plugins','' );// for wp 3.0 +. reset plugin database information
	add_filter( 'http_request_args', 'ptd_prevent_tested_plugin_updates', 10, 2 );


}

//stop testing a plugin
function tp_stop_testing( $tp_remove ) {
	global $ptd_options;
	do_action( 'deactivate_' . trim( $tp_remove ) );
	unset( $ptd_options[$tp_remove] );
	ptd_update_option();
	wp_redirect( admin_url( 'plugins.php?deactivate=true' ) );
}
function ptd_check_test_removal() {
	if( isset( $_GET['remove_test'] ) ) {
		check_admin_referer( 'remove_test' );
		tp_stop_testing( $_GET['remove_test'] );
	}
}

add_action( 'admin_init', 'ptd_check_test_removal' );
add_filter( 'plugin_action_links', 'tp_action_links', 10, 2 );
?>