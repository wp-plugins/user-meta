<?php
/*
Plugin Name: User Meta
Plugin URI: http://wordpress.org/extend/plugins/user-meta
Description: User and usermeta data can be imported by CSV file, new user may be created and existing user data can be overwrite. This plugin also give option to add extra field to user profile page.
Author: Khaled Hossain Saikat
Version: 1.0.0
Author URI: http://thekhaled.info
*/



// always find line endings
ini_set('auto_detect_line_endings', true);

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    exit('Please don\'t access this file directly.');
}

require_once( ABSPATH . WPINC . '/registration.php');

require_once ('function.php');
require_once ('importer.php');
require_once ('user_meta_editor.php');

// add admin menu
add_action('admin_menu', 'user_meta_menu');

function user_meta_menu() {	
	add_submenu_page( 'users.php', 'User Import', 'User Import', 'manage_options', 'user-meta-import', 'user_meta_import_export');
    $page = add_submenu_page( 'users.php', 'Meta Editor', 'Meta Editor', 'manage_options', 'user-meta-editor', 'user_meta_editor');
    add_action("admin_print_scripts-$page", 'user_meta_scripts');
}

// show import form
function user_meta_import_export() {
    
    user_meta_importer();
}


register_activation_hook(__FILE__, "user_meta_install");

add_filter('user_contactmethods','user_meta_contactmethod',10,1);
    
function user_meta_scripts(  ) {
  wp_enqueue_script( "category-list1", path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ ) )."/js/script.js"), array( 'jquery' ) );
}

?>