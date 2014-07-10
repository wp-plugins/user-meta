<?php

if ( ! class_exists( 'umPreloadsProController' ) ) :
class umPreloadsProController {
    
    function __construct() {
        global $userMeta;
        
        // Deprecated from 1.1.3rc3
        //add_action( 'wp',           array( $this, 'loginPageExtraExecution' ) );
        //add_filter( 'the_title',    array( $this, 'loginPageExtraExecutionTitle' ), 10, 2 );
        //add_filter( 'the_content',  array( $this, 'loginPageExtraExecutionContent' ) );
        
        add_filter( 'pre_set_site_transient_update_plugins',array( $this, 'checkForUpdate' ) );
          
        add_filter( 'login_url',    array( $this, 'loginUrl' ),  10, 2 );  
        
        add_action( 'login_init',   array( $this, 'disableDefaultLoginPage' ) );
        add_filter( 'authenticate', array( $this, 'changeLoginErrorMessage' ), 50, 3 );
        
        add_action( 'admin_notices',      array( $this, 'adminNotices' ) );
    }
    
    
    function checkForUpdate( $transient ) {
        global $userMeta;
        
        if ( empty($transient->checked) || !$userMeta->isPro )
            return $transient;
        
        if ( !$userMeta->isPro() ) {
            unset( $transient->response[$userMeta->pluginSlug] );
            return $transient;
        }
        
        include_once( ABSPATH . 'wp-includes/class-IXR.php' );
        $client = new IXR_Client( $userMeta->website . '/xmlrpc.php' );
        $latestVersion = $client->query( 'ump.checkUpdate', 'latest_version' ) ? $client->getResponse() : 0;
    
        if ( version_compare( $userMeta->version, $latestVersion, '<' ) ) {
            $slug = explode( '/', $userMeta->pluginSlug );
                      
            $obj = new stdClass();
            $obj->slug          = str_replace('.php', '', $slug[1]);   
            $obj->new_version   = $latestVersion;
            $obj->url           = $userMeta->website;
            $obj->package       = $userMeta->generateProUrl( 'download', $latestVersion );
            
            $transient->response[$userMeta->pluginSlug] = $obj;
        } else
            unset( $transient->response[$userMeta->pluginSlug] );

        return $transient;
    }
    
    
    // Deprecated from 1.1.3rc3
    function loginPageExtraExecution(){
        global $userMeta, $post;      

        $action = @$_REQUEST[ 'action' ]; 
        $login = $userMeta->getSettings( 'login' );
        if( @$post->ID <> @$login[ 'login_page' ] )
            return;
                 
        switch ($action) {
            
            case 'logout':
            	check_admin_referer('log-out');
                    
                $user = wp_get_current_user();
                if( empty( $user->ID ) )
                        return false;
                               
            	wp_logout();
            
                $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;
                if( $userMeta->isFilterEnable( 'logout_redirect' ) )
                    $redirect_to = apply_filters('logout_redirect', $redirect_to, $redirect_to, $user);            
                
                if( !$redirect_to )
                    $redirect_to = get_permalink( $login[ 'login_page' ] );
                
            	wp_redirect( $redirect_to );
            	exit();                
            break;                
           
        }
    }
    
    // Deprecated from 1.1.3rc3
    function loginPageExtraExecutionTitle( $title, $id = 0 ){                        
        if( is_page() ){
            global $userMeta;
            
            $login = $userMeta->getSettings( 'login' );
            if( @$login[ 'login_page' ] == $id ){
                $action = @$_REQUEST[ 'action' ];
                if( in_array( $action, array( 'lostpassword', 'rp', 'email_verification' ) ))
                    $title = null;
            }                 
        }
    
        return $title;
    }
    
    // Deprecated from 1.1.3rc3
    function loginPageExtraExecutionContent( $content ){
        if( is_page() ){
            global $userMeta, $post;
            
            $login = $userMeta->getSettings( 'login' );
            if( @$login[ 'login_page' ] == $post->ID ){
                
                $userMeta->enqueueScripts( array( 
                    'user-meta',           
                    'validationEngine',
                    'password_strength',
                ) );                      
                $userMeta->runLocalization();               
                
                
                $action = @$_REQUEST[ 'action' ];
                switch ($action) {         
        
                    case 'lostpassword':
                    case 'retrievepassword' :
                        $content = $userMeta->lostPasswordForm( 'show' );
                    break;
                    
                    case 'resetpass' :
                    case 'rp' :
                        $content = $userMeta->resetPassword();
                    break; 
                    
                    case 'email_verification' :
                        $content = $userMeta->emailVerification();
                    break;                         
                                 
                }   
            }                 
        }
    
        return $content;
    }
    
    // Sleep
    function pluginUpdateNotice(){
        global $userMeta;
        $cache = get_transient( $userMeta->transient['cache'] );  
        if( @$cache['new_version'] ){
            $plugin = $userMeta->pluginSlug;
            $url = wp_nonce_url( "update.php?action=upgrade-plugin&plugin=$plugin", "upgrade-plugin_$plugin" );                
            $url = admin_url( $url );                                               
            echo $userMeta->showMessage( sprint( 'There is a new version <strong>%1$s</strong> of <strong>%2$s</strong> available. <a href="%3$s">update automatically.</a>', $cache['new_version'], $userMeta->title, $url ) );
        }  
    }        
    

    function loginUrl( $login_url, $redirect ) {
        global $userMeta;            
        $login = $userMeta->getSettings( 'login' );
        
        $loginPage = @$login[ 'login_page' ];
        if ( $loginPage ){
        	$login_url = get_permalink( $loginPage );            
        	if ( ! empty($redirect) )
        		$login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );           
        	//if ( $force_reauth )
        		//$login_url = add_query_arg('reauth', '1', $login_url);                
        }
                    
        return $login_url;
    }
    
    function disableDefaultLoginPage() {
        global $userMeta;  
        
        if ( isset( $_REQUEST['log'] ) && isset( $_REQUEST['pwd'] ) ) {
            $user = wp_authenticate( $_REQUEST['log'], $_REQUEST['pwd'] );
            if ( !is_wp_error( $user ) )
                return;
        }
        
        if ( ! empty( $_REQUEST['action'] ) && ( 'logout' == $_REQUEST['action'] ) )
            return;
        
        $login = $userMeta->getSettings( 'login' );
        
        if ( ! empty( $login[ 'login_page' ] ) && ! empty( $login[ 'disable_wp_login_php' ] ) ) {
            wp_redirect( get_permalink( $login[ 'login_page' ] ) );
            exit();
        }
    }
    
    function changeLoginErrorMessage( $user, $username, $password ) {
        global $userMeta;
        
        if ( ! is_wp_error( $user ) )
                return $user;  
            
        if ( ! in_array( $user->get_error_code(), array( 'invalid_username', 'incorrect_password' ) ) )   
            return $user;  
            
        $login = $userMeta->getSettings( 'login' );
        if ( in_array( @$login[ 'login_by' ], array( 'user_email', 'user_login_or_email' ) ) ) {
            $title  = $userMeta->loginByArray();
            
            // can be commented from 1.1.3rc2
            if ( $user->get_error_code() == 'invalid_username' )
                return new WP_Error( 'invalid_username', sprintf(__('<strong>ERROR</strong>: Invalid %s.', $userMeta->name), @$title[ $login[ 'login_by' ] ]));

            if ( $user->get_error_code() == 'incorrect_password' )
                return new WP_Error('incorrect_password', sprintf(__('<strong>ERROR</strong>: Incorrect Password. <a href="%s" title="Password Lost and Found">Lost your password</a>?', $userMeta->name), wp_lostpassword_url()));
        }

    } 
    
    function adminNotices() {
        global $userMeta;
        
        $settings = $userMeta->getData( 'settings' );
        if ( ! empty( $settings['login']['disable_wp_login_php'] ) && empty( $settings['login']['resetpass_page'] ) )
            echo '<div class="error"><p>' . sprintf( __('Please set %s!',$userMeta->name), "<a href='" . $userMeta->adminPageUrl( 'settings', false ) . '#um_settings_login'. "'>". __( 'Reset Password Page', $userMeta->name ) ."</a>" ) .'</p></div>';
        
        if ( in_array( @$settings['registration']['user_activation'], array( 'email_verification', 'both_email_admin' ) ) && empty( $settings['registration']['email_verification_page'] ) )
            echo '<div class="error"><p>' . sprintf( __('Please set %s!',$userMeta->name), "<a href='" . $userMeta->adminPageUrl( 'settings', false ) . '#um_settings_registration'. "'>". __( 'Email verification page', $userMeta->name ) ."</a>" ) .'</p></div>'; 
    }
    
}
endif;