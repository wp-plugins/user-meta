<?php

if ( ! class_exists( 'umBackendProfileController' ) ) :
class umBackendProfileController {
    
    function __construct() {
        add_action( 'show_user_profile',        array( $this, 'profileField') );
        add_action( 'edit_user_profile',        array( $this, 'profileField') );
        add_action( 'personal_options_update',  array( $this, 'profileUpdate') );
        add_action( 'edit_user_profile_update', array( $this, 'profileUpdate') );        
    }
    
                
    function profileField( $user ) {
        global $userMeta, $pagenow;
        
        $userMeta->loadAllScripts();
        
        $this->_hideBackendFields();
        
        if ( $pagenow == 'profile.php' )
            $userID = $userMeta->userID();
        elseif ( $pagenow == 'user-edit.php' )
            $userID = esc_attr( @$_REQUEST['user_id'] );
        
        if ( empty($userID) ) return;
                            
        $user = new WP_User( $userID );

        $settings       = $userMeta->getData( 'settings' );
        $fields         = $userMeta->getData( 'fields' );                      
        $backendFields  = @$settings['backend_profile']['fields'];
                    
        if ( ! is_array( $backendFields ) ) return;
        
        $formKey = 'um_backend_profile';
        
        $i = 0;
        foreach ( $backendFields as $fieldID ) {
            if ( empty( $fields[ $fieldID ] ) )
                continue;
            
            if ( ! empty( $fields[ $fieldID ]['admin_only'] ) ) {
                if ( ! $userMeta->isAdmin() ) continue;
            }
                
            $i++;
            
            // if first rows is not section heading then initiate html table
            if ( ( $i == 1 ) || ( @$fields[ $fieldID ]['field_type'] <> 'section_heading' ) ) {
                echo "<table class=\"form-table\"><tbody>"; 
                $inTable = true;
            }
                                              
            if ( $fields[ $fieldID ]['field_type'] == 'section_heading' ) {
                if ( @$inTable ) {
                    echo "</tbody></table>";
                    $inTable = false;
                }                                           
                echo "<h3>" . $fields[ $fieldID ]['field_title'] . "</h3> <table class='form-table'><tbody>";
                $inTable = true;
                continue;
            }
            
                                
            $fieldName = @$fields[ $fieldID ]['meta_key'];    
            if ( ! $fieldName )
                $fieldName = $fields[ $fieldID ]['field_type'];
            
            $fields[ $fieldID ]['field_id']    = $fieldID;
            $fields[ $fieldID ]['field_name']  = $fieldName;
            $fields[ $fieldID ]['field_value'] = @$user->$fieldName;
            $fields[ $fieldID ]['title_position'] = 'hidden';      
            
            $field = $fields[ $fieldID ];
            
            $field = apply_filters( 'user_meta_field_config', $field, $fieldID, $formKey );

            $fieldDisplay = $userMeta->renderPro( 'generateField', array( 
                'field'         => $field,
                'form'          => null,
                'actionType'    => null,
                'userID'        => $userID,
                'inPage'        => null,
                'inSection'     => null,
                'isNext'        => null,
                'isPrevious'    => null,
                'currentPage'   => null,
                'uniqueID'      => 'profile',
            ) );
     
            $html = apply_filters( 'user_meta_field_display', $fieldDisplay, $fieldID, $formKey, $field );
            
            if ( $fields[ $fieldID ]['field_type'] == 'hidden' )
                echo $html;
            else
                echo "<tr><th><label for=\"um_field_$fieldID\">{$fields[ $fieldID ]['field_title']}</label></th><td>$html</td></tr>";
            //echo "<td>$html <span class=\"description\"></span></td></tr>";                                                              
        }
        
        
        if ( @$inTable )
            echo "</tbody></table>";
        
         ?>          
        <script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery("#your-profile").validationEngine();
                jQuery(".um_rich_text").wysiwyg({initialContent:" "});  
  
                umFileUploader( '<?php  echo $userMeta->pluginUrl . '/framework/helper/uploader.php' ?>' );    

                var form = document.getElementById( 'your-profile' );
                form.encoding = 'multipart/form-data';
                form.setAttribute('enctype', 'multipart/form-data');  
            });
        </script>
        <?php                        
    }
    

    function profileUpdate( $user_id ) {
        global $userMeta;
        
        $errors = new WP_Error;
                
        $fields         = $userMeta->getData( 'fields' );   
        $backendProfile = $userMeta->getSettings( 'backend_profile' );                  
        $backendFields  = @$backendProfile[ 'fields' ];
        
        if ( ! is_array( $backendFields ) ) return;
        
        $userData = array();
        foreach ( $backendFields as $fieldID ) {
            if ( empty( $fields[ $fieldID ] ) ) continue;
                          
            $fieldData = $fields[ $fieldID ];
            
            if ( ! empty( $fieldData['admin_only'] ) ) {
                if ( ! $userMeta->isAdmin() ) continue;
            }
            
            if ( ! empty( $fieldData[ 'meta_key' ] ) )
                $fieldName  = $fieldData[ 'meta_key' ];
            else {
                if ( in_array( @$fieldData[ 'field_type' ], array( 'user_registered', 'user_avatar' ) ) )
                    $fieldName = $fieldData[ 'field_type' ];
            }
            
            if ( empty( $fieldName ) ) continue;
            
            $userData[ $fieldName ] = @$_POST[ $fieldName ];
            
            /**
             * Only js validation. No message is shown under this hook. 
             */
            /*if( $fieldData[ 'required' ] ){
                if( !$userData[ $fieldName ] ){
                    $errors->add( 'required', sprintf( __( '%s field is required', $userMeta->name ), $fieldData['field_title'] ) );
                    continue;
                }
            }
            if( $fieldData[ 'unique' ] ){
                if( !$userMeta->isUserFieldAvailable( $fieldName, $userData[ $fieldName ], $user_id ) ){
                    $errors->add( 'taken', sprintf( __( '%1$s: "%2$s" already taken', $userMeta->name ), $fieldData[ 'field_title' ], $userData[ $fieldName ] ) );
                    continue;
                }
            }*/           
            
            /// Handle non-ajax file upload and remove filepath from file cache for ajax
            if ( in_array( $fieldData['field_type'], array( 'user_avatar', 'file' ) ) ) {
                if ( isset( $_FILES[ $fieldName ] ) ) {
                    $extensions = @$fieldData['allowed_extension'] ? $fieldData['allowed_extension'] : "jpg, png, gif";
                    $maxSize    = @$fieldData['max_file_size'] ? $fieldData['max_file_size'] * 1024 : 1024 * 1024;
                    $file = $userMeta->fileUpload( $fieldName, $extensions, $maxSize );
                    if ( is_wp_error( $file ) ) {
                        if( $file->get_error_code() <> 'no_file' )                       
                            $errors->add( $file->get_error_code(), $file->get_error_message() );
                    } else {
                        if ( is_string( $file ) )
                            $userData[ $fieldName ] = $file;
                    }                     
                }
                
                $userMeta->removeFromFileCache( $userData[ $fieldName ] );
            }       
          
        }   
        
        $userMeta->insertUser( $userData, $user_id );
        
        /*if( !empty( $userData[ 'user_registered' ] ) ){
            wp_update_user( array(
                'ID' => $user_id,
                'user_registered' => $userData[ 'user_registered' ],
            ) );
        }        
        //if( $errors->get_error_code() )
            //return $userMeta->ShowError( $errors ); 
                            
        if( @$userData && is_array( @$userData ) ){
            foreach( $userData as $key => $val )
                update_user_meta( $user_id, $key, $val );
        }*/
    }
    

    function _hideBackendFields() {
        global $userMeta;
        $backend_profile    = $userMeta->getSettings( 'backend_profile' );
        $hide_fields        = @$backend_profile[ 'hide_fields' ];
        
        if ( ! is_array( $hide_fields ) )
            return;
            
        foreach ( $hide_fields as $id => $field )
            $userMeta->disableAdminRow( $id );
    }
    
}
endif;
