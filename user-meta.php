<?php
/*
Plugin Name: User Meta
Plugin URI: http://wordpress.org/extend/plugins/user-meta
Description: Frontend user profile with extra fields. .
Author: Khaled Hossain Saikat
Version: 1.0.5
Author URI: http://khaledsaikat.com
*/

if (realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
    exit('Please don\'t access this file directly.');
}

require_once ( 'framework/init.php' );



if (!class_exists( 'userMeta' )){
    class userMeta extends pluginFramework {
        public $name    = 'User Meta';
        public $version = '1.0.5';
        public $prefix  = 'um_';       
        
        public $pluginPath;
        public $modelsPath;
        public $controllersPath;
        public $viewsPath;
        public $pluginUrl;
        public $assetsUrl;

        //public $objects;
        //public $scripts = array();  //$scripts[$handle] = $url
        
        public $fields;
        public $forms;
        public $settings;
        public $options;
        
        function __construct(){     
            $this->pluginPath       = dirname( __FILE__ );
            $this->modelsPath       = $this->pluginPath . '/models/';
            $this->controllersPath  = $this->pluginPath . '/controllers/';
            $this->viewsPath        = $this->pluginPath . '/views/';
            
            $this->pluginUrl        = plugins_url( '' , __FILE__ ); 
            $this->assetsUrl        = $this->pluginUrl  . '/assets/';            
                      
            //Load Plugins & Framework modal classes
            global $pluginFramework;
            $this->loadModels( $this->modelsPath );
            $this->loadModels( $pluginFramework->modelsPath );         
                        
            $this->options = array( 
                'fields'   => 'user_meta_fields',
                'forms'    => 'user_meta_forms',
                'settings' => 'user_meta_settings',
                'cache'    => 'user_meta_cache',
            );    
            $this->setOptions();                    
        }
        
        
        function setOptions(){
            //$this->fields   = get_option( $this->options['fields'] );
            //$this->forms    = get_option( $this->options['forms'] );
            $this->settings = get_option( $this->options['settings'] );            
        }
                     
    }
}

global $userMeta;
$userMeta = new userMeta;
    
$userMeta->loadDirectory( $userMeta->controllersPath );

register_activation_hook( __FILE__, array($userMeta, 'onPluginActivation') );
register_deactivation_hook( __FILE__, array($userMeta, 'onPluginDeactivation') );

?>