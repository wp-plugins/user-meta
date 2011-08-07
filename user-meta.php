<?php
/*
Plugin Name: User Meta
Plugin URI: http://wordpress.org/extend/plugins/user-meta
Description: User and usermeta data can be imported by CSV file. This plugin also give option to add extra field to user profile page. Provide shortcode for frontend user profile update.
Author: Khaled Hossain Saikat
Version: 1.0.2
Author URI: http://thekhaled.info
*/


// always find line endings
ini_set('auto_detect_line_endings', true);



if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    exit('Please don\'t access this file directly.');
}


include_once('class/function.php');
if (!class_exists('userMeta')){
    class userMeta extends userMetaFunction {
                        
        //Constructor
        function __construct(){       
            register_activation_hook(__FILE__, array(__CLASS__, 'pluginInstall'));  
            add_action('admin_head', array(__CLASS__, 'addCss'));
            add_action('wp_head', array(__CLASS__, 'addCss'));     
            self::getGlobal();     
        }        
                        
        //retrieve saved settings and assign them as global, so that dont need to call several times
        function getGlobal(){
            global $um_groups, $um_fields, $um_field_checked;
            $um_groups = get_option('user_meta_group');
            $um_fields = get_option('user_meta_field');
            $um_field_checked = get_option('user_meta_field_checked');         
        }

        function addCss() {
            $um_plugin_url = self::pluginUrl();
            echo "<link href='{$um_plugin_url}/css/style.css' rel='stylesheet' type='text/css' media='screen' />";            
        }
        
        function getDefaultUserFields(){
            $defaultFields = array('user_login' => 'Username', 'user_email' => 'Email', 'user_pass' => 'Password', 'user_nicename' => 'Nicename', 'user_url' => 'Website', 'display_name' => 'Display Name', 'nickname' => 'Nickname', 'first_name' => 'First Nmae', 'last_name' => 'Last Name', 'description' => 'Description', 'user_registered' => 'Registration Date', 'role' => 'Role', 'jabber' => 'Jabber', 'aim' => 'Aim', 'yim' => 'Yim' ); 
            return $defaultFields;
        }

        function pluginUrl(){
            return path_join(WP_PLUGIN_URL, basename( dirname( __FILE__ )));
        }
                
        function pluginInstall(){
            //add_option('user_meta_field', '', '', 'no');
            //add_option('user_meta_group', '', '', 'no');
            add_option('user_meta_field_checked', array('user_email' => 'on', 'display_name' => 'on'));
        }        
     
            
    }
}


$userMeta = new userMeta;
include_once('class/importer.php');
include_once('class/fieldEditor.php');
include_once('class/profileFrontend.php');
include_once('class/profileBackend.php');


?>