<?php

if (!class_exists('umShortcodes')) :
    class umShortcodes {
        
        function __construct(){
            global $userMeta;
            
            add_shortcode( 'user-meta', array( $this, 'init' ) );
            
            // Shortcode for backword version: 1.0.3 suppoert
            add_shortcode( 'user-meta-profile', array( $this, 'backwordInit' ) );                       
            
            add_action( 'wp_ajax_um_insert_user',           array( $this, 'ajaxInsertUser' ) );
            add_action( 'wp_ajax_nopriv_um_insert_user',    array( $this, 'ajaxInsertUser' ) );
            add_action( 'wp_ajax_um_show_uploaded_file',              array( $this, 'ajaxShowUploadedFile' ) );
            add_action( 'wp_ajax_nopriv_um_show_uploaded_file',       array( $this, 'ajaxShowUploadedFile' ) );  
            add_action( 'wp_ajax_um_validate_unique_field', array( $this, 'ajaxValidateUniqueField' ) );
            add_action( 'wp_ajax_nopriv_um_validate_unique_field',array( $this, 'ajaxValidateUniqueField' ) );          
                        
            
            $userMeta->addScript( 'jquery',             'shortcode', 'user-meta' );
            $userMeta->addScript( 'jquery-ui-core',     'shortcode', 'user-meta' );
            $userMeta->addScript( 'jquery-ui-tabs',     'shortcode', 'user-meta' );
            //$userMeta->addScript( 'jquery-ui-widget',   'front' );
            //$userMeta->addScript( 'jquery-ui-mouse',   'front' );

            $userMeta->addScript( 'jquery.ui.widget.js',            'shortcode', 'user-meta', 'jqueryui' );
            $userMeta->addScript( 'jquery.ui.mouse.js',             'shortcode', 'user-meta', 'jqueryui' );
            $userMeta->addScript( 'jquery.ui.slider.js',            'shortcode', 'user-meta', 'jqueryui' );
            $userMeta->addScript( 'jquery.ui.datepicker.js',        'shortcode', 'user-meta', 'jqueryui' );
            $userMeta->addScript( 'jquery-ui-timepicker-addon.js',  'shortcode', 'user-meta', 'jqueryui' );            
            $userMeta->addScript( 'jquery.ui.all.css',              'shortcode', 'user-meta', 'jqueryui' );
            $userMeta->addScript( 'jquery.wysiwyg.js',              'shortcode', 'user-meta', 'jquery' );
            $userMeta->addScript( 'jquery.wysiwyg.css',             'shortcode', 'user-meta', 'jquery' );
            $userMeta->addScript( 'jquery.tools.min.js',            'shortcode', 'user-meta', 'jquery' );                        

            $userMeta->addScript( 'validationEngine-en.js',         'shortcode', 'user-meta', 'jquery' );
            $userMeta->addScript( 'validationEngine.js',            'shortcode', 'user-meta', 'jquery' );   
            $userMeta->addScript( 'validationEngine.css',           'shortcode', 'user-meta', 'jquery' );
            $userMeta->addScript( 'jquery.validate.js',             'shortcode', 'user-meta', 'jquery' );           
            $userMeta->addScript( 'jquery.password_strength.js',    'shortcode', 'user-meta', 'jquery' );
                                                           
            $userMeta->addScript( 'fileuploader.js',                'shortcode', 'user-meta', 'jquery' );
            $userMeta->addScript( 'fileuploader.css',               'shortcode', 'user-meta', 'jquery' );                        
                                               
            $userMeta->addScript( 'plugin-framework.js',    'shortcode', 'user-meta' );
            $userMeta->addScript( 'plugin-framework.css',   'shortcode', 'user-meta' );        
                
            $userMeta->addScript( 'user-meta.js',           'shortcode', 'user-meta' );
            $userMeta->addScript( 'user-meta.css',          'shortcode', 'user-meta' );
        
            
      
	/*wp_enqueue_script( 'jquery');
    //wp_localize_script( 'jquery', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
	wp_localize_script( 'jquery', 'front', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		//'nonce' => wp_create_nonce( 'ajax-example-nonce' )
	) );       */
    
   //wp_localize_script( 'jquery', 'ajaxurl', admin_url( 'admin-ajax.php' ) );   
            
        }
        
        function init( $atts ){
        	extract( shortcode_atts( array(
        		'form' => 'profile',
        		'type' => 'profile', // profile,registration,both,none
        	), $atts ) );
            
            return $this->generateForm( $form, $type );
        
        }
        
        function backwordInit(){
            return $this->generateForm( 'profile', 'profile' );
        }
        
        function generateForm( $formName, $actionType ){
            global $userMeta, $user_ID;
            
            $userID  = $user_ID;
            $isAdmin = $userMeta->isAdmin();
            
            // Determine Form type
            if( $actionType == 'both' )
                $actionType = $user_ID ? 'profile' : 'registration';        
                
            // Checking Permission
            if( $actionType == 'profile' OR $actionType == 'none' ){
                if( !$user_ID )
                    return "<div>You do not have permission to access this page.</div>";
            }elseif( $actionType == 'registration' ) {
                if( $user_ID AND !$isAdmin )
                    return "<div>You are already registered.</div>";
            }
            
            if( $actionType == 'registration' )
                return "Sorry. Registration is not supported.";
            
            // Loading $userID as admin request
            if( $isAdmin ){
                if( isset($_REQUEST['user_id']) )
                    $userID = $_REQUEST['user_id'];
            }            
            
            $fields     = get_option( $userMeta->options['fields'] );
            $forms      = get_option( $userMeta->options['forms'] );            
            $form       = isset( $forms[$formName] ) ? $forms[$formName] : null;
            $userData   = get_userdata( $userID );
            
            return $userMeta->render( 'generateForm', array( 
                'actionType'=> $actionType,
                'fields'    => $fields, 
                'form'      => $form, 
                'userID'    => $userID,
                'userData'  => $userData,
            ) );
                       
        }
 
        function ajaxInsertUser(){
            global $userMeta, $user_ID;
            $userMeta->verifyNonce();
            
            // Determine $userID
            $userID = $user_ID;
            if( isset( $_REQUEST['user_id']) ){
                if( $userMeta->isAdmin() && $_REQUEST['user_id'] )
                    $userID = $_REQUEST['user_id'];
            }
            
            // $_REQUEST Validation
            if( !isset( $_REQUEST['action_type'] ) ) die( 'Action type not set' );
            if( !isset( $_REQUEST['form_key'] ) ) die( 'Form Name not set' );
                    
            // Captcha validation                 
            $error = null;           
            $captchaValidation = $this->isInvalidateCaptcha();
            if( $captchaValidation )
                $error[] = $captchaValidation;
            
            // filter valid key for update
            $validFields = $userMeta->formValidInputField( $_REQUEST['form_key'] );
            if( !$validFields ) die('No Field to Update');
            
            // Assign $fieldName,$fieldData to $userData. Also validating required and unique
            foreach( $validFields as $fieldName => $fieldData ){
                if( !isset($_REQUEST[$fieldName]) ) continue;
                if( $fieldName == 'user_pass' ){
                    if( !$_REQUEST[$fieldName] )
                        continue;
                }        
                
                // Assigning data to $userData       
                $userData[$fieldName] = $_REQUEST[$fieldName];
                
                // For removing value for cache
                if( $fieldName == 'user_avatar' OR $fieldName == 'file' )
                    $imageCache[] = $userData[$fieldName];
                
                if( $fieldName == 'user_login' OR $fieldName == 'user_email' ){
                    $fieldData['required'] = true;
                    $fieldData['unique']   = true;
                }                    
                if( $fieldData['required'] ){
                    if( !$_REQUEST[$fieldName] ){
                        $error[] = $fieldData['field_title'] . " field is required";
                        continue;
                    }                        
                }
                if( $fieldData['unique'] ){
                    if( !$userMeta->isUserFieldAvailable( $fieldName, $_REQUEST[$fieldName] ) )
                        $error[] = "{$fieldData['field_title']}: '{$_REQUEST[$fieldName]}' already taken";
                }
            }
            
            // Determine $actionType
            $actionType = $_REQUEST['action_type'];
            if( $actionType == 'both' ){
                if( $user_ID )
                    $actionType = 'profile';
                else
                    $actionType = 'registration';
            }     

            // Do insert/update
            if( !$error AND isset($userData) ){
                if( $actionType == 'profile' ){
                    if( $user_ID )
                        $response = $userMeta->insertUser( $userData, $userID );
                    else
                        $error[] = "User must be loged in to update profile";
                }elseif( $actionType == 'registration' ){
                    $response = $userMeta->insertUser( $userData );
                }
                
                if( isset($response['error']) )
                    $error[] = $response['error'];
            }
                      
            // Showing message
            if($error)
                echo $userMeta->showError( $error, false );
            else{
                // Removing Cache
                if( isset( $imageCache ) )
                    $userMeta->removeCache( 'image_cache', $imageCache, false );
                
                if( $actionType == 'profile' )
                    $message = "Profile successfully updated.";
                elseif( $actionType == 'registration' )
                    $message = "Registration successfully completed.";
                echo $userMeta->showMessage( $message, 'success', false );
            }
                                               
            die();
        }
        
        
        function ajaxValidateUniqueField(){
            global $userMeta;
            $userMeta->verifyNonce();
            
            $status = false;               
            if( !isset($_REQUEST['fieldId']) OR !$_REQUEST['fieldValue'] ) return;
            
            $id     = ltrim( $_REQUEST['fieldId'], 'um_field_' );
            $fields = get_option( $userMeta->options['fields'] );
            
            if( isset( $fields[$id] ) ){
                $fieldData = $userMeta->getFieldData( $id, $fields[$id] );
                $status    = $userMeta->isUserFieldAvailable( $fieldData['field_name'], $_REQUEST['fieldValue'] );
                
                if( !$status ){
                    $msg = $_REQUEST['fieldValue'] . ' already taken';
                    if( isset($_REQUEST['customCheck']) ){
                         echo "error";
                         die();
                    }                        
                }
                                        
                $response[] = $_REQUEST['fieldId'];
                $response[] = isset($status) ? $status: true;
                $response[] = isset( $msg ) ? $msg : null;
                                
                echo json_encode($response);                                        
            }

            die();
        }
        
        
        function ajaxShowUploadedFile(){
            global $userMeta;     
            $userMeta->verifyNonce();     
            
            if( isset($_REQUEST['showimage']) ){
                if( isset($_REQUEST['imageurl']) )
                    echo "<img src='{$_REQUEST['imageurl']}' />";
                die();
            }
            

            // Update Cache
            if( isset( $_REQUEST['filepath'] ) ){
                if( $_REQUEST['filepath'] ){
                    $cache   = get_option( $userMeta->options['cache'] ); 
                    if( isset( $cache['image_cache'] ) ){
                        if( !in_array( $_REQUEST['filepath'], $cache['image_cache'] ) )
                            $cache['image_cache'][] = $_REQUEST['filepath'];
                    }else
                        $cache['image_cache'][] = $_REQUEST['filepath'];
                    update_option( $userMeta->options['cache'], $cache );
                }
            }
                      
            
            echo $userMeta->render( 'showFile', array(
                'filepath'  => isset($_REQUEST['filepath'])  ? $_REQUEST['filepath']  : null,
                'fieldname' => isset($_REQUEST['fieldname']) ? $_REQUEST['fieldname'] : null,
            ) );
            
            die();

        }
        
        function isInvalidateCaptcha(){
             global $userMeta;
             $userMeta->verifyNonce();
             
             // Checking existance of captcha field
             if( !isset($_POST["recaptcha_challenge_field"]) )
                return false;
             
            $settings = get_option( $userMeta->options['settings'] );
            require_once( $userMeta->pluginPath . '/framework/helper/recaptchalib.php');
            $privateKey = null;
            if( isset( $settings['recaptcha_private_key'] ) )
                $privateKey = $settings['recaptcha_private_key'];
            
            $resp = recaptcha_check_answer ($privateKey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);
            if (!$resp->is_valid) 
                return $resp->error;
            
            return false;         
        }
 
        
    }
endif;
?>