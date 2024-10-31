<?php
/*
This will remove any traces of the plugin from the database
*/

if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}

delete_option('plugin_test_drive');

