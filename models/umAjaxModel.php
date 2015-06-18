<?php

if ( ! class_exists( 'umAjaxModel' ) ) :
class umAjaxModel {

    function postInsertUser() {
        global $userMeta; //$userMeta->dump($_REQUEST);die();
        $userMeta->verifyNonce();
        
        $umUserInsert = new umUserInsert();
        return $umUserInsert->postInsertUserProcess();
      
        
        $errors = new WP_Error();

        $user_ID = get_current_user_id();
        
        /// Determine $userID        
        $userID = $user_ID;
        if ( isset( $_REQUEST['user_id'] ) ) {
            $user   = new WP_User( $user_ID );
            if ( $user->has_cap( 'add_users' ) && $_REQUEST['user_id'] )
                $userID =  esc_attr( $_REQUEST['user_id'] );
        }
        
        $user   = new WP_User( $userID );
        
        /// $_REQUEST Validation
        $actionType = @$_REQUEST['action_type'];
        if ( empty( $actionType ) )
            $errors->add( 'empty_action_type', __( 'Action type is empty', $userMeta->name ) );
        if ( ! isset( $_REQUEST['form_key'] ) )
            $errors->add( 'empty_form_name', __( 'Form name is empty', $userMeta->name ) );

        /// Determine $actionType  
        $actionType = strtolower( $actionType );
        if ( $actionType == 'profile-registration' ) {
            if ( $user_ID )
                $actionType = 'profile';
            else
                $actionType = 'registration';
        }    
        
        $formName = $_REQUEST['form_key'];
        
        $formBuilder = new umFormGenerate( $formName, $actionType, $userID );

        if ( ! $formBuilder->isFound() )
            $errors->add( 'not_found', sprintf( __( 'Form "%s" is not found.', $userMeta->name ), $formName ) );
        
        /// filter valid key for update
        //$validFields = $userMeta->formValidInputField( @$_REQUEST['form_key'] );
        $validFields = $formBuilder->validInputFields();
        if ( ! $validFields )
            $errors->add( 'empty_field', __( 'No field to update', $userMeta->name ) );

        /// Showing error
        if ( $errors->get_error_code() )
            return $userMeta->ShowError( $errors );  
        
        // Free version limitation
        //if( ( $actionType <> 'profile' ) && ! ( $userMeta->isPro ) ) 
            //return $userMeta->showError( sprintf( __( 'type="%s" is not supported in free version', $userMeta->name ), $actionType ) );  
        
        /// Assign $fieldName,$field to $userData. Also validating required and unique
        foreach ( $validFields as $fieldName => $field ) {
            
            $field = apply_filters( 'user_meta_field_config', $field, $field['id'], $formName, $userID );
            
            if ( $actionType == 'profile' ) {
                if ( $fieldName == 'user_login' || ( $fieldName == 'user_pass' && empty( $_REQUEST['user_pass'] ) ) )
                    continue;
            }
            
            if ( $field[ 'field_type' ] == 'custom' && isset( $field['input_type'] ) && $field['input_type'] == 'password' ) {
                if ( empty( $_REQUEST[ $fieldName ] ) )
                    continue;
            }
                        
            /// Assigning data to $userData       
            $userData[ $fieldName ] = @$_REQUEST[ $fieldName ];
            
            if ( is_array( $userData[ $fieldName ] ) && count( $userData[ $fieldName ] ) == 1 && ! empty( $userData[ $fieldName ] ) )
                $userData[ $fieldName ] = $userData[ $fieldName ][0];
            
            
            /// Handle non-ajax file upload
            if ( in_array( $field[ 'field_type' ], array( 'user_avatar', 'file' ) ) ) {
                if ( isset( $_FILES[ $fieldName ] ) ) {
                    $extensions = ! empty( $field[ 'allowed_extension' ] ) ? $field[ 'allowed_extension' ] : "jpg,png,gif";
                    $maxSize    = ! empty( $field[ 'max_file_size' ] ) ? $field[ 'max_file_size' ] * 1024 : 1024 * 1024;
                    $file = $userMeta->fileUpload( $fieldName, $extensions, $maxSize );
                    if ( is_wp_error( $file ) ) {
                        if ( $file->get_error_code() <> 'no_file' )                       
                            $errors->add( $file->get_error_code(), $file->get_error_message() );
                    } else {
                        if ( is_string( $file ) ) {
                            $umFile = new umFile;
                            $umFile->initFile( $field );
                            $userData[ $fieldName ] = $file;
                        }   
                    }                       
                }
                
                $userMeta->removeFromFileCache( $userData[ $fieldName ] );
            }
            
            
            /*
             * Using umField Class
             */
            if ( ! isset( $field['field_value'] ) )
                $field['field_value'] = $userData[ $fieldName ];
            
            $umField = new umField( $field['id'], $field, array(
                'user_id'       => $userID,
                'insert_type'   => $actionType,
            ) );
            
            if ( $fieldName == 'user_pass' && $actionType == 'registration' )
                $umField->addRule( 'required' );
            
            if ( $fieldName == 'user_pass' && $actionType == 'profile'  ) {
                if ( ! empty( $field['required_current_password'] ) )
                    $umField->addRule( 'current_password' );
            }

            
            if ( isset( $_REQUEST[ $fieldName . "_retype" ] ) )
                $umField->addRule( 'equals' );
             
            if ( ! $umField->validate() ) {
                foreach ( $umField->getErrors() as $errKey => $errVal )
                    $errors->add( $errKey, $errVal );
            }
             
            /*if( isset($_REQUEST[ $fieldName . "_retype" ]) && !empty($_REQUEST[$fieldName]) ){
                if( $_REQUEST[ $fieldName . "_retype" ] != $_REQUEST[$fieldName] )
                    $errors->add( 'retype_required', sprintf( __( '%s field is required to retype', $userMeta->name ), $fieldData['field_title'] ) );
            }
            
            if( $fieldData[ 'unique' ] ){
                $available = $userMeta->isUserFieldAvailable( $fieldName, $userData[ $fieldName ], $userID );
                if( ! $available )
                    $errors->add( 'existing_' . $fieldName, sprintf( __( '%1$s: "%2$s" already taken', $userMeta->name ), $fieldData[ 'field_title' ], $userData[ $fieldName ] ) );								
            }*/
        }       

		// If add_user_to_blog set true in UserMeta settings panel
		if ( is_multisite() && ($actionType == 'registration') ) {
			$registrationSettings = $userMeta->getSettings('registration');
			if ( ! empty( $registrationSettings['add_user_to_blog'] ) ){
				if ( in_array( 'existing_user_login', $errors->get_error_codes() ) )
					unset( $errors->errors['existing_user_login'] );
				if ( in_array( 'existing_user_email', $errors->get_error_codes() ) )
					unset( $errors->errors['existing_user_email'] );				
			}				
		}
			
        if ( empty( $userData ) )
            return $userMeta->ShowError( __( 'No data to update', $userMeta->name ) );         
        
        // Showing error
        if ( $errors->get_error_code() )
            return $userMeta->ShowError( $errors ); 
       
        /// Run Captcha validation after completed all other validation     
        $captchaValidation = $userMeta->isInvalidateCaptcha();
        if ( $captchaValidation ) {
            $errors->add( 'invalid_captcha', $captchaValidation );  
            return $userMeta->ShowError( $errors );
        }
        
        
        /**
         * Check allowed role for security purpose
         */
        if ( isset( $userData['role'] ) ) {
            $ignoreRole = true;

            //$fieldData = $userMeta->getFieldData( @$_REQUEST['role_field_id'] );
            $field = $formBuilder->getField( @$_REQUEST['role_field_id'] );
            if ( is_array( @$field['allowed_roles'] ) ){
                if ( in_array( $userData['role'], $field['allowed_roles'] ) )
                        $ignoreRole = false;
            }
           
            if ( $ignoreRole )
                unset( $userData['role'] );
        }

        
        if ( $actionType == 'registration' )
            return $userMeta->registerUser( $userData, @$imageCache );
         
        $html = null;
        if ( $actionType == 'profile' ) {
            if ( ! $user_ID )
                return $userMeta->showError( __( 'User must be logged in to update profile', $userMeta->name ) );           

            $userData = apply_filters( 'user_meta_pre_user_update', $userData, $userID, $formName );
            if ( is_wp_error( $userData ) )
                return $userMeta->showError( $userData );
            
            /**
             * Profile Update modified data
             */
            $modifiedData = array();
            foreach ( $userData as $key => $val ) {
                if ( $user->$key != $val )
                    $modifiedData[ $key ] = $user->$key;
            }
            
            $response = $userMeta->insertUser( $userData, $userID );
            if ( is_wp_error( $response ) )
                return $userMeta->showError( $response );
            
            if ( ! empty( $modifiedData ) ) {
                $storedData = get_transient( $userMeta->prefix . 'user_modified_data' );
                if ( empty( $storedData ) )
                    $storedData = array();
                
                $storedData[ $userID ] = $modifiedData;

                set_transient( $userMeta->prefix . 'user_modified_data', $storedData, 30 );
            }
            /**
             * End Profile Update modified data
             */

            
            /// Allow to populate form data based on DB instead of $_REQUEST
            $userMeta->showDataFromDB = true;            
                
            // Commented since 1.1.5rc3
            //if( isset( $imageCache ) )
                //$userMeta->removeCache( 'image_cache', $imageCache, false );  
                              
            do_action( 'user_meta_after_user_update', (object) $response, $formName );
              
            $message    = $userMeta->getMsg( 'profile_updated' );
            $html = "<div action_type='$actionType'>" . $userMeta->showMessage( $message ) . "</div>";  
        }
        
        return $userMeta->printAjaxOutput( $html );
    }  
    
    function ajaxValidateUniqueField() {
        global $userMeta;
        $userMeta->verifyNonce( false );
        
        $status = false;               
        if ( ! isset($_REQUEST['fieldId']) OR ! $_REQUEST['fieldValue'] ) return;
        
        $id     = ltrim( $_REQUEST['fieldId'], 'um_field_' );
        $fields = $userMeta->getData( 'fields' );
        
        if ( isset( $fields[$id] ) ) {
            $fieldData = $userMeta->getFieldData( $id, $fields[$id] );
            $status    = $userMeta->isUserFieldAvailable( $fieldData['field_name'], $_REQUEST['fieldValue'] );
            
            if ( !$status ) {
                $msg = sprintf( __( '%s already taken', $userMeta->name ), $_REQUEST[ 'fieldValue' ] );
                if ( isset($_REQUEST['customCheck']) ) {
                     echo "error";
                     die();
                }                        
            }
                                    
            $response[] = $_REQUEST['fieldId'];
            $response[] = isset( $status ) ? $status: true;
            $response[] = isset( $msg ) ? $msg : null;
                            
            echo json_encode($response);                                        
        }

        die();
    }   
    
    function ajaxFileUploader() {
        global $userMeta;
        $userMeta->verifyNonce();

        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array( 'jpg','jpeg','png','gif' );
        // max file size in bytes
        $sizeLimit = 1 * 1024 * 1024;
        $replaceOldFile = FALSE;

        $allowedExtensions  = apply_filters( 'pf_file_upload_allowed_extensions', $allowedExtensions );
        $sizeLimit          = apply_filters( 'pf_file_upload_size_limit', $sizeLimit );
        $replaceOldFile     = apply_filters( 'pf_file_upload_is_overwrite', $replaceOldFile );



        $uploader = new qqFileUploader( $allowedExtensions, $sizeLimit );
        $result = $uploader->handleUpload( $replaceOldFile );
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars( json_encode( $result ), ENT_NOQUOTES );
        die();
    }
    
    function ajaxShowUploadedFile() {
        global $userMeta;
        $userMeta->verifyNonce();     
                
        if ( isset( $_REQUEST['showimage'] ) ) {
            if ( isset( $_REQUEST['imageurl'] ) )
                echo "<img src='{$_REQUEST['imageurl']}' />";
            die();
        }
        
        $file = new umFile();
        $file->ajaxUpload();
        die();
        
        /**
         * Commented since 1.1.7beta1
         * 
        // Showing Image
        $fieldID    = trim( str_replace( 'um_field_', '', @$_REQUEST['field_id'] ) );
        $fields     = $userMeta->getData( 'fields' );
        $field      = @$fields[@$fieldID];          
        if ( @$field['field_type'] == 'user_avatar' ) {
            if ( ! empty( $field['image_size'] ) ) {
                $field['image_width']   = $field['image_size'];
                $field['image_height']  = $field['image_size'];
            } else {
                $field['image_width']   = 96;
                $field['image_height']  = 96;
            }          
        }  
        
        if ( ! empty( $field ) ) {
            echo $userMeta->renderPro( 'showFile', array(
                'filepath'      => @$_REQUEST['filepath'],
                'field_name'    => @$_REQUEST['field_name'],
                'width'         => @$field['image_width'],
                'height'        => @$field['image_height'],
                'crop'          => ! empty( $field['crop_image'] ) ? true : false,
                //'readonly'  => @$fieldReadOnly,   // implementation of read-only is not needed.
            ) );                 
        }
         */
                
        die();
    }    
    
    function ajaxWithdrawLicense() {
        global $userMeta;
        $userMeta->verifyNonce();
        
        $status = $userMeta->withdrawLicense();
        if ( is_wp_error( $status ) )
            echo $userMeta->showError( $status );
        elseif ( $status === true ) {
            echo $userMeta->showMessage( __( 'License has been withdrawn', $userMeta->name ) );
            echo $userMeta->jsRedirect( $userMeta->adminPageUrl( 'settings', false ) );            
        } else
            echo $userMeta->showError( __('Something went wrong!', $userMeta->name) );
        
        die();
    }
    
    function ajaxSaveAdvancedSettings() {
        global $userMeta;
        $userMeta->checkAdminReferer( __FUNCTION__ );
        
        if ( ! isset( $_REQUEST ) )
            $userMeta->showError( __( 'Error occurred while updating', $userMeta->name ) );
        
        $data = $userMeta->arrayRemoveEmptyValue( $_REQUEST );  
        $data = $userMeta->removeNonArray( $data );
        
        $userMeta->updateData( 'advanced', stripslashes_deep( $data ) );
        echo $userMeta->showMessage( __( 'Successfully saved.', $userMeta->name ) );
        
        die();
    }
    
    function ajaxTestMethod() {
        global $userMeta;
        echo 'Working...';
        $userMeta->dump( $_REQUEST );
        die();
    }
      
    
}
endif;