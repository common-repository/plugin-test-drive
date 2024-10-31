<?php 

require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
$known_untestables = array ( 'wordpress-logger/wplogger.php', 'sitepress-multilingual-cms/sitepress.php' );//plugins known to be untestable
$ptd_plugins = apply_filters( 'all_plugins', get_plugins() );
if( !isset( $ptd_options['invalid_plugs'] ) )
	$ptd_options['invalid_plugs'] = array();
if( !isset( $ptd_options['untestable'] ) )
	$ptd_options['untestable'] = array();

function ptd_generate_options() {
	?>
	<div class="wrap">
		<h2><?php echo __( 'Plugin Test Drive Options', 'plugin_test_drive' );?></h2><a name="validIP"></a>
		<form method="post" action="options.php" id="ptd-form">
			<?php global $ptd_plugins, $ptd_options;settings_fields( 'ptd_settings' ); ?>
			<table class="form-table">
				<tr><th colspan="3" class="field-titles"><?php echo __( 'Choose the tester key (using this key, Plugin Test Drive will recognize you and load the plugins for you only):', 'plugin_test_drive' );?></th></tr>
				<tr>
					<td width="180"><?php echo __( 'Tester key:', 'plugin_test_drive' );?>
						<select name="plugin_test_drive[tester_key_method]" id="tester-key-method">
							<option value = "0" <?php if( $ptd_options[tester_key_method] == '0' ) echo ' selected="selected"';?>>By IP Address</option>
							<option value = "1" <?php if( $ptd_options[tester_key_method] == '1' ) echo ' selected="selected"';?>>By User Name</option>
						</select>
					</td>
					<td width="350">
						<input id="tester-key-val" type="text" name="plugin_test_drive[tester_key_val]" value="<?php echo $ptd_options['tester_key_val'];?>" />
						<a id="set-to-current"><?php echo __( 'set to your IP address', 'plugin_test_drive' );?></a>
						<span class="error-submit" id="method-error"></span>
					</td>
					<!--allow column split if more than 10 plugins-->
					<?php if( count( $ptd_plugins ) >= 10 ):?>
						<td><?php echo __( 'View preference:', 'plugin_test_drive' );?>
							<input type="checkbox" name="plugin_test_drive[split_view]" value="1" <?php checked( '1', $ptd_options['split_view'] );?> />
							<?php echo __( 'Split to columns', 'plugin_test_drive' );?>
						</td>
					<?php else:?>
						<td></td>
					<?php endif;?>
				</tr>
			</table>
			<table class="form-table" id="plugList" style="width:300px">
				<tr><th colspan="3" class="field-titles"><?php echo __( 'Select plugins for testing:', 'plugin_test_drive' );?></th></tr>
				<tr>
					<td>
						<input type="checkbox" id="cbToggle" name="plugin_test_drive[all_selected]" value="1" <?php checked( '1', $ptd_options['all_selected'] );?> />
						<em id="toggleSelect"><?php echo __( 'Click to check All', 'plugin_test_drive' );?></em>
					</td>
				</tr>
				<tr><td id="blankRow"></td></tr>
				<?php $position = 1;
					if( $ptd_options['split_view'] ) {
						$col_num = round( count( $ptd_plugins )/6 );
						if( $col_num < 1 )
							$col_num = 1;
						elseif( $col_num > 4 )
							$col_num = 4;
					} else
						$col_num = 1;
					foreach( $ptd_plugins as $ptd_file => $ptd_data ) {
						if( $col_num == 1 || ( $col_num == 2 && $position == 1 ) )
							echo '<tr valign="top">'; 
						if( $col_num > 1 ) {
							switch (  $position ) {
								case 1:
									$td_class='first';
									break;
								case 2:
									$td_class='second';
									break;
								case 3:
									$td_class='third';
									break;
								case 4:
									$td_class='forth';
									break;
							}
						} else
							$td_class='v-list';?>
						<td class="<?php echo $td_class;?>"><?php 
						if( !is_plugin_active( $ptd_file ) ):
							if( in_array( $ptd_file, $ptd_options['invalid_plugs'] ) )://invalid plugins?>
								<input type="checkbox" disabled="disabled" />
								<span class="ptd-invalid"><?php echo $ptd_data[Name]; ?></span> **
								<input name="plugin_test_drive[invalid_plugs][]" type="hidden" value="<?php echo $ptd_file;?>" /><?php 
								$reset_invalid_url = PTD_PLUGIN_SETTINGS_URL . '&reset_invalid=' . $ptd_file . '&updated=true';
								$reset_invalid_url = wp_nonce_url( $reset_invalid_url, 'reset_invalid' );?>
								<a href="<?php echo $reset_invalid_url;?>&updated=true"><?php echo __( 'Remove invalid flag', 'plugin_test_drive' );?></a><?php 
							elseif( in_array( $ptd_file, $ptd_options['untestable'] ) )://untestable plugins?>
								<input type="checkbox" disabled="disabled" />
								<span class="ptd-untestable"><?php echo $ptd_data[Name]; ?></span> ***
								<input name="plugin_test_drive[untestable][]" type="hidden" value="<?php echo $ptd_file;?>" /><?php 
								$untestable_url = PTD_PLUGIN_SETTINGS_URL . '&reset_untestable=' . $ptd_file . '&updated=true';
								$untestable_url = wp_nonce_url( $untestable_url, 'reset_untestable' );?>
								<a href="<?php echo $untestable_url;?>&updated=true"><?php echo __( 'Remove untestable flag', 'plugin_test_drive' );?></a><?php 
							else://these plugins are inactive and confirmed as valid, therefore can be tested?>
								<input name="plugin_test_drive[<?php echo $ptd_file; ?>][is_tested]" type="checkbox" class="pluginCheck" value="1" <?php checked( '1', $ptd_options[$ptd_file]['is_tested'] ); ?> /><?php 
								echo $ptd_data[Name];
								if( $ptd_options[$ptd_file]['is_tested'] == '1' ): ?>
									<input name="plugin_test_drive[<?php echo $ptd_file;?>][was_tested]" type="hidden" value="<?php echo $ptd_options[$ptd_file]['is_tested'];?>" /><?php 
								endif;
							endif;
						else://active plugins cannot be selected?>
							<input type="checkbox" disabled="disabled" /><span class="ptd-active-plug"><?php echo $ptd_data[Name]; ?></span> *<?php 
						endif;
						echo '</td>';
						if( $col_num == 1 || ( $col_num > 1 && $position == $col_num ) ) {
							echo '</tr>';
							$position = 1;
						}
						else
							$position++;
					}//end foreach loop?>
			</table>
			<p><b><?php echo __( 'Notes', 'plugin_test_drive' );?></b></p>
			<ol>
				<li>* <?php echo __( 'Active plugins cannot be selected for testing. Only inactive plugins can be tested', 'plugin_test_drive' );?>.</li>
				<li><?php echo __( 'Make sure to include "if_function_exists()" in any plugin function you add:', 'plugin_test_drive' );?>
				<p><span id="wrong"><?php echo __( 'Wrong', 'plugin_test_drive' );?></span><code>&lt;?php some_plugin_function(); ?&gt;</code></p>
				<p><span id="right"><?php echo __( 'Right', 'plugin_test_drive' );?></span><code>&lt;?php if( function_exists( "some_plugin_function" ) ) { some_plugin_function(); } ?&gt;</code></p></li>
				<li>** <?php echo __( 'Invalid plugins (plugins with malfunctioned code) cannot be tested.', 'plugin_test_drive' );?></li>
				<li>*** <?php echo __( 'Untestable plugins (rare, <a href="http://www.webtechwise.com/plugin-test-drive#untestable">read more</a>', 'plugin_test_drive' );?>).</li>
				<?php echo __( 'In case you upgraded/fixed the plugin, click on "Remove invalid flag"/"Remove untestable plugin" to enable plugin selection.', 'plugin_test_drive' );?>
				<p></p>
				<li><?php echo __( 'If you like Plugin Test Drive, <a href="http://wordpress.org/extend/plugins/plugin-test-drive/">support by voting here</a>', 'plugin_test_drive'  );?>.</li>
				<li><?php echo __( 'For any questions, requests or feeback refer to the <a href="http://www.webtechwise.com/plugin-test-drive/">plugin page</a>', 'plugin_test_drive'  );?>.</li> 
			</ol>
			<p class="submit">
				<input type="submit" id="ptd-submit" value="<?php _e( 'Save Changes' ); ?>" />
				<a class="error-submit" href="#validIP"><?php echo __( 'Please enter a valid IP address.', 'plugin_test_drive' );?></a>
			</p>
		</form>
	</div><?php 
}

function ptd_options_sanitize( $ptd_form_options ) {
	global $ptd_options, $known_untestables;
	if( isset( $ptd_options['split_view'] ) && !isset( $ptd_form_options['split_view'] ) ) {
		unset( $ptd_options['split_view'] );
		ptd_update_option();
	}
	foreach( $ptd_form_options as $ptd_option => $ptd_val ) {
		if( substr( $ptd_option,-4 ) == '.php' && count( $ptd_form_options[$ptd_option] ) == 1 ) {
			if( $ptd_form_options[$ptd_option]['was_tested'] == '1' )
				do_action( 'deactivate_' . trim( $ptd_option ) );
			elseif( !isset( $ptd_options[$ptd_option] ) || ( $ptd_options[$ptd_option]['was_tested'] == '1' && count( $ptd_form_options[$ptd_option] ) == 1 ) ) {//this first part of the condition is necessary for cases where multiple plugins are marked for testing and one of them raises and error
				$ptd_result = ptd_validate_plugin( $ptd_option );
				ptd_further_validation( $ptd_result, $ptd_option );
			}
		} elseif( $ptd_option === 'tester_key_method' ) {
			$ptd_form_options[$ptd_option] = ( $ptd_form_options[$ptd_option] == 1 ? 1 : 0 );
		} elseif( $ptd_option === 'tester_key_val' ) {
			$ptd_form_options['tester_key_val'] = wp_filter_nohtml_kses( $ptd_form_options['tester_key_val'] );
		}

		//update db in case a selected plugin fails validation and crashes the loop. updates will only apply to selections up to that plugin (alphabetically).
		$ptd_options[$ptd_option] = $ptd_form_options[$ptd_option];
		ptd_update_option();
	}
	return $ptd_form_options;
}

function ptd_validate_plugin( $ptd_option ) {
	global $ptd_options, $known_untestables;
	$ptd_option = plugin_basename( trim( $ptd_option ) );
	$valid = validate_plugin( $ptd_option );

	if( is_wp_error( $valid ) )
		return $valid;
	wp_redirect( add_query_arg( '_error_nonce', wp_create_nonce( 'plugin-activation-error_' . $ptd_option ), PTD_PLUGIN_SETTINGS_URL . '&error=true&plugin=' . $ptd_option ) );
	ob_start();
	include( WP_PLUGIN_DIR . '/' . $ptd_option );
	do_action( 'activate_plugin', trim( $ptd_option ) );
	//originally db update occurs right here, however, updating here will cause version incompatible plugins to be saved to db and tested anyway.
	do_action( 'activate_' . trim( $ptd_option ) );//version incompatible plugins usually fail validation here.
	do_action( 'activated_plugin', trim( $ptd_option ) );
	$active_plugs = get_option( 'active_plugins' );
	if( in_array( $ptd_option,  $active_plugs ) || in_array( $ptd_option,  $known_untestables ) ) {
		array_push( $ptd_options['untestable'], $ptd_option );
		update_option( 'plugin_test_drive', $ptd_options );
		wp_redirect( add_query_arg( '_error_nonce', wp_create_nonce( 'plugin-activation-error_' . $ptd_option ), PTD_PLUGIN_SETTINGS_URL . '&error=true&plugin=' . $ptd_option . '&untestable=round1' ) );
		exit;
		// $untestable_url = PTD_PLUGIN_SETTINGS_URL . '&untestable=' . $ptd_option . '&updated=true';
		// $untestable_url = wp_nonce_url( $untestable_url, 'untestable' );
	}
	if( ob_get_length() > 0 ) {
		$output = ob_get_clean();
		return new WP_Error( 'unexpected_output', __( 'The plugin generated unexpected output.' ), $output );
	}
	ob_end_clean();
	return null;
}

function ptd_further_validation( $ptd_result, $ptd_option ) {
	if( is_wp_error( $ptd_result ) ) {
		if( 'unexpected_output' == $ptd_result->get_error_code() ) {
			$redirect = PTD_PLUGIN_SETTINGS_URL . '&error=true&charsout=' . strlen( $ptd_result->get_error_data()) . '&plugin=' . $ptd_option;
			wp_redirect( add_query_arg( '_error_nonce', wp_create_nonce( 'plugin-activation-error_' . $ptd_option ), $redirect ) );
			exit;
		} else
			wp_die( $ptd_result );
	}
}

function ptd_notice() {
	echo __( '<div class="ptd-message updated"><p>Plugin Test Drive has updated your tested plugins list. You can also check their status in <a href="plugins.php">plugins page</a></p></div>', 'plugin_test_drive' );
}

#URL Requests

if( isset( $_GET['test_after_install'] ) ) {
	global $ptd_options;
	check_admin_referer( 'test_first' );
	$ptd_option = $_GET['test_after_install'];
	$ptd_result = ptd_validate_plugin( $ptd_option );
	ptd_further_validation( $ptd_result, $ptd_option );
	$ptd_options[$ptd_option]['is_tested'] = '1';
	update_option( 'plugin_test_drive', $ptd_options );
	wp_redirect( admin_url( 'plugins.php' ) );
}

if( isset( $_GET['updated'] ) )
	add_action( 'admin_notices', 'ptd_notice' );

//remove plugin invalid flag
if( isset( $_GET['reset_invalid'] ) && ( $invalid_plug = array_search( $_GET['reset_invalid'], $ptd_options['invalid_plugs'] ) ) !== false ) {
	check_admin_referer( 'reset_invalid' );
	unset( $ptd_options['invalid_plugs'][$invalid_plug] );
	update_option( 'plugin_test_drive', $ptd_options );
	wp_redirect( PTD_PLUGIN_SETTINGS_URL . '&updated=true' );
}

//remove plugin untestable flag
if( isset( $_GET['reset_untestable'] ) && ( $untestable = array_search( $_GET['reset_untestable'], $ptd_options['untestable'] ) ) !== false ) {
	check_admin_referer( 'reset_untestable' );
	unset( $ptd_options['untestable'][$untestable] );
	update_option( 'plugin_test_drive', $ptd_options );
	wp_redirect( PTD_PLUGIN_SETTINGS_URL . '&updated=true' );
}

$ptd_plugin = $_GET['plugin'];
if( $_GET['action'] == 'error_scrape' && $_GET['page'] == 'ptd_options_page' ) {//similar to wp-admin/plugins.php with a few changes	
	check_admin_referer( 'plugin-activation-error_' . $ptd_plugin );
	$valid = validate_plugin( $ptd_plugin );
	if( is_wp_error( $valid ) )
		wp_die( $valid );
	if( ! WP_DEBUG ) {
		if( defined( 'E_RECOVERABLE_ERROR' ) )
			error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
		else
			error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING );
	}
	@ini_set( 'display_errors', true ); //Ensure that Fatal errors are displayed.
	// Go back to "sandbox" scope so we get the same errors as before

	function plugin_sandbox_scrape( $ptd_plugin ) {
		echo '<div style="font-size:14px">';include( WP_PLUGIN_DIR . '/' . $ptd_plugin );echo '</div>';
	}
	plugin_sandbox_scrape( $ptd_plugin );
	do_action( 'activate_' . $ptd_plugin );
	exit;
	break;
}

if( isset( $_GET['error'] ) && $_GET['page'] == 'ptd_options_page' ) {
	if( isset( $_GET['updated'] ) ) {
		wp_redirect( PTD_PLUGIN_SETTINGS_URL . '&updated=true' );
	} else {
		if( isset( $_GET['charsout'] ) ) {
			$ptd_err_msg = sprintf( __( 'Just to let you know, Plugin Test Drive has detected that ', 'plugin_test_drive' ) . '<strong>' . $ptd_plugins[$ptd_plugin]['Name'] . '</strong> ' . __( ' has generated %d characters of <strong>unexpected output</strong> during activation.<br />However, this is a minor problem, therefore, PTD will flag it as valid and test it anyway.', 'plugin_test_drive' ), $_GET['charsout'] );
			$ptd_options[$ptd_plugin]['is_tested'] = '1';
			update_option( 'plugin_test_drive', $ptd_options );
		} elseif( isset( $_GET['untestable'] ) ) {
			$untestable = $_GET['untestable'];
			if( $untestable == 'round1' ) {		
				deactivate_plugins( $ptd_plugin );
				wp_redirect( PTD_PLUGIN_SETTINGS_URL . '&error=true&plugin=' . $ptd_plugin . '&untestable=round2' );
			} else {
				$ptd_err_msg = '<strong>' . $ptd_plugins[$ptd_plugin]['Name'] . '</strong> ' . __( 'has been flagged as untestable by Plugin Test Drive.  <a href="http://www.webtechwise.com/plugin-test-drive#untestable">click here</a> to read more.', 'plugin_test_drive' );
			}
		} elseif( !in_array( $ptd_plugin, $ptd_options['invalid_plugs'] ) ) {
			array_push( $ptd_options['invalid_plugs'], $ptd_plugin );
			update_option( 'plugin_test_drive', $ptd_options );
			$ptd_err_msg = __( 'Plugin Test Drive has detected an error in ', 'plugin_test_drive' ) . '<strong>' . $ptd_plugins[$ptd_plugin]['Name'] . '</strong>, ' . __( 'therefore, it cannot be tested.', 'plugin_test_drive' );
		}
	}
	?>
	<div class="ptd-message updated"><p><?php echo $ptd_err_msg; ?></p>
		<?php 
			if( !isset( $_GET['charsout'] ) && wp_verify_nonce( $_GET['_error_nonce'], 'plugin-activation-error_' . $ptd_plugin ) ) {
				//put error text in iframe and "error_scrape" in url ?>
			<iframe style="border:0;" width="100%" height="70px" src="<?php echo admin_url( PTD_PLUGIN_SETTINGS_PAGE . '&action=error_scrape&amp;plugin=' . esc_attr( $ptd_plugin ) . '&amp;_wpnonce=' . esc_attr( $_GET['_error_nonce'] ) ); ?>"></iframe>
			<?php 
			}
		?>
	</div>
	<?php 
}
?>