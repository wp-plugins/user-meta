<?php

class UserMetaModelSample {
    
    function umFields(){                
        $fieldsList = array(
        
            //WP default fields
            'user_login' => array(
                'title'         => 'Username',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),
            'user_email' => array(
                'title'         => 'Email',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'user_pass' => array(
                'title'         => 'Password',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            /*'user_nicename' => array(
                'title'         => 'Nicename',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ), */            
            'user_url' => array(
                'title'         => 'Website',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'display_name' => array(
                'title'         => 'Display Name',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'nickname' => array(
                'title'         => 'Nickname',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'first_name' => array(
                'title'         => 'First Nmae',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'last_name' => array(
                'title'         => 'Last Name',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'description' => array(
                'title'         => 'Description',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'user_registered' => array(
                'title'         => 'Registration Date',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'role' => array(
                'title'         => 'Role',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'jabber' => array(
                'title'         => 'Jabber',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'aim' => array(
                'title'         => 'Aim',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'yim' => array(
                'title'         => 'Yim',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),   
            'user_avatar' => array(
                'title'         => 'Avatar',
                'box_group'     => 'wp_default',
                'field_group'   => 'wp_default',   
            ),             
            
         
            //Standard Fields
            'text' => array(
                'title'         => 'Textbox',
                'box_group'     => 'standard',
                'field_group'   => 'textbox',   
            ),   
            'textarea' => array(
                'title'         => 'Paragraph',
                'box_group'     => 'standard',
                'field_group'   => 'textbox',   
            ),   
            'rich_text' => array(
                'title'         => 'Rich Text',
                'box_group'     => 'standard',
                'field_group'   => 'textbox',   
            ),  
            'hidden' => array(
                'title'         => 'Hidden Field',
                'box_group'     => 'standard',
                'field_group'   => 'textbox',   
            ),                       
            'select' => array(
                'title'         => 'Drop Down',
                'box_group'     => 'standard',
                'field_group'   => 'options',   
            ),   
            'checkbox' => array(
                'title'         => 'Checkbox',
                'box_group'     => 'standard',
                'field_group'   => 'options',   
            ),   
            'radio' => array(
                'title'         => 'Select One (radio)',
                'box_group'     => 'standard',
                'field_group'   => 'options',   
            ),     
               
                                                   
        );        
        return $fieldsList;                    
    }
    
    /**
     * Get fields by 
     * @param $by: key, box_group, field_group
     * @param $value: 
     */    
    function getFields( $by=null, $param=null, $get=null ){
        $fieldsList = self::umFields();
        
        if( !$by )
            return $fieldsList;
        
        //$result = array();
        if( $param ){
            if( $by == 'key' ){
                if( key_exists( $param, $fieldsList ) )
                    return $fieldsList[$param];
            }else{
                foreach( $fieldsList as $key => $fieldData ){
                    if( $fieldData[$by] == $param ){
                        if( !$get )
                            $result[$key] = $fieldData;
                        else    
                            $result[$key] = $fieldData[$get];
                    }
                }
            }
        }      
        
        return isset( $result ) ? $result : false;
    }    
    
    
    /**
     * Extract fielddata from fieldID
     * @param $fieldID
     * @param $fieldData : if $fieldData not set the it will search for field option for fielddata
     * @return array: field Data
     */
    function getFieldData( $fieldID, $fieldData=null ){
        global $userMeta;       
        
        if( !$fieldData ){
            $fields = get_option( $userMeta->options['fields'] );
            if( !isset($fields[$fieldID]) ) return;
            $fieldData = $fields[$fieldID];
        }
        
        //Setting Field Group
        $field_type_data   = $userMeta->getFields( 'key', $fieldData['field_type'] );
        $box_group        = $field_type_data['box_group'];                
                
        //Setting Field Name
        $fieldName = null;
        if( $box_group == 'wp_default' ){
            $fieldName = $fieldData['field_type'];
        }else{
           if( isset($fields[$fieldID]['meta_key']) )
                $fieldName = $fieldData['meta_key'];
        }              
        
        $returnData = $fieldData;
        $returnData['field_id']     = $fieldID;
        $returnData['box_group']    = $box_group;
        $returnData['field_name']   = $fieldName;
        $returnData['meta_key']     = isset($fieldData['meta_key']) ? $fieldData['meta_key'] : null;
        $returnData['field_title']  = isset($fieldData['field_title']) ? $fieldData['field_title'] : null;
        $returnData['required']     = isset($fieldData['required']) ? true : false;
        $returnData['unique']       = isset($fieldData['unique']) ? true : false;
        
        return $returnData;
    }


    /**
     * Validate input field from a form
     * @param $form_key
     * @return array: key=field_name 
     */
    function formValidInputField( $form_key ){
        global $userMeta;
        $forms  = get_option( $userMeta->options['forms'] );        
        if( !isset($forms[$form_key]['fields']) ) return;
        
        foreach( $forms[$form_key]['fields'] as $fieldID ){
            $fieldData  = $this->getFieldData( $fieldID );
            if( $fieldData['box_group'] == 'wp_default' OR $fieldData['box_group'] == 'standard' ){
                if( $fieldData['box_group'] == 'standard' AND !isset($fieldData['meta_key']) )
                    continue;
                $validField[ $fieldData['field_name'] ]['field_title'] = $fieldData['field_title'];
                $validField[ $fieldData['field_name'] ]['required']    = $fieldData['required'];
                $validField[ $fieldData['field_name'] ]['unique']      = $fieldData['unique'];              
            }        
        }
        
        return isset($validField) ? $validField : null;
    }
    
    function removeCache( $cacheType, $cacheValue, $byKey=true ){
        global $userMeta;
        
        $cache   = get_option( $userMeta->options['cache'] );
        if( isset($cache[$cacheType]) ){            
            if( !is_array( $cacheValue ) )
                $cacheValue = array($cacheValue);
                
            foreach( $cacheValue as $key => $val ){
                $cacheKey = $val;
                if( !$byKey )
                    $cacheKey = array_search( $val, $cache[$cacheType] );   
                unset( $cache[$cacheType][$cacheKey] );             
            }
            update_option( $userMeta->options['cache'], $cache );
        }           
    }
    
    
    function upgradeFrom_1_0_3(){
        global $userMeta;     
        
        $cache = get_option( $userMeta->options['cache'] ); 
        if( isset( $cache['upgrade']['1.0.3']['fields_upgraded'] ) )
            return;        
           
        // Check if upgrade is needed
        $fields = get_option( $userMeta->options['fields'] );
        $exists = false;
        if( $fields ){
            if( is_array($fields) ){
                foreach( $fields as $value ){
                    if( isset($value['field_type']) )
                        $exists = true;
                }
            }
        }
        if($exists) return;
        
        $i = 0;        
        // get Default fields
        $prevDefaultFields  = get_option( 'user_meta_field_checked' );
        if( $prevDefaultFields ){
            foreach( $prevDefaultFields as $fieldName => $noData  ){   
                if( $fieldName == 'avatar' ) $fieldName = 'user_avatar';
                $fieldData = $this->getFields( 'key', $fieldName );
                if( !$fieldData ) continue;
                $i++;
                $newField[$i]['field_title']    = isset($fieldData['title']) ? $fieldData['title'] : null;
                $newField[$i]['field_type']     = $fieldName;
                $newField[$i]['title_position'] = 'top';
            }
        }        
        
        // get meta key
        $prevFields         = get_option( 'user_meta_field' );
        if( $prevDefaultFields ){
            foreach( $prevFields as $fieldData  ){                
                if( !$fieldData ) continue;
                $i++;
                $fieldType = $fieldData['meta_type'] == 'dropdown' ? 'select' : 'text';
                $newField[$i]['field_title']    = isset($fieldData['meta_label']) ? $fieldData['meta_label'] : null;
                $newField[$i]['field_type']     = $fieldType;
                $newField[$i]['title_position'] = 'top';
                $newField[$i]['description']    = isset($fieldData['meta_description']) ? $fieldData['meta_description'] : null;
                $newField[$i]['meta_key']       = isset($fieldData['meta_key']) ? $fieldData['meta_key'] : null;
                $newField[$i]['required']       = $fieldData['meta_required'] == 'yes' ? 'on' : null;
                if( isset($fieldData['meta_option']) ){
                    if( $fieldData['meta_option'] AND is_string($fieldData['meta_option']) ){
                        $options = $userMeta->arrayRemoveEmptyValue( unserialize($fieldData['meta_option'] ) );
                        if( $options )
                            $newField[$i]['options'] = implode( ',', $options );
                    }
                }  
                $newField[$i] = $userMeta->arrayRemoveEmptyValue( $newField[$i] );            
            }
        }       
        
        // Defining Form data
        $newForm['profile']['form_key'] = 'profile';
        $n = 0;
        while( $n < $i ){
            $n++;
            $newForm['profile']['fields'][] = $n;
        }
        
        if( isset($newField) ){
            update_option( $userMeta->options['fields'], $newField );
            update_option( $userMeta->options['forms'], $newForm );
            $cache['upgrade']['1.0.3']['fields_upgraded'] = true; 
            update_option( $userMeta->options['cache'], $cache);                       
        }
        
        return true;       
    }
    
    function upgradeAvatarFrom_1_0_3(){
        global $userMeta;
        
        $cache = get_option( $userMeta->options['cache'] ); 
        if( isset( $cache['upgrade']['1.0.3']['avatar_upgraded'] ) )
            return;
        
        $users = get_users( array(
            'meta_key' => 'user_meta_avatar',
        ) );       
        if( !$users ) return;
        
        $uploads = wp_upload_dir();
        foreach( $users as $user ){
            $oldUrl = get_user_meta( $user->ID, 'user_meta_avatar', true );
            if( $oldUrl ){
                $newPath = str_replace( $uploads['baseurl'], '', $oldUrl );
                update_user_meta( $user->ID, 'user_avatar', $newPath );
            }
        }
                     
        $cache['upgrade']['1.0.3']['avatar_upgraded'] = true; 
        update_option( $userMeta->options['cache'], $cache);
    
        return true;        
    }
    
    function runningUpgrade(){
        $this->upgradeFrom_1_0_3();
        $this->upgradeAvatarFrom_1_0_3();
    }
    
    function isUpgradationNeeded(){
        global $userMeta;
        
        // check upgrade flug
        $cache = get_option( $userMeta->options['cache'] );
        if( isset( $cache['upgrade']['1.0.3']['fields_upgraded'] ) )
            return false;        
           
        // Check data exists in new version
        $fields = get_option( $userMeta->options['fields'] );
        $exists = false;
        if( $fields ){
            if( is_array($fields) ){
                foreach( $fields as $value ){
                    if( isset($value['field_type']) )
                        $exists = true;
                }
            }
        }
        if($exists) return false;   
        
        $prevDefaultFields  = get_option( 'user_meta_field_checked' ); 
        $prevFields         = get_option( 'user_meta_field' );
        if( $prevDefaultFields or $prevFields )
            return true;             
    }
    
    function onPluginActivation(){
        global $userMeta;
        
        $userMeta->upgradeFrom_1_0_3();
        
        $cache = get_option( $userMeta->options['cache'] ); 
        $cache['version']       = $userMeta->version; 
        $cache['version_type']  = 'free';  
        update_option( $userMeta->options['cache'], $cache );     
        
        wp_schedule_single_event(time()+10, 'user_meta_running_upgrade');     
    }
    
    function onPluginDeactivation(){
         
    }
    
    function howToUse(){
        $html = null;
        $html .= "<h4>3 steps to getting started</h4>";
        $html .= "<p><b>Step 1. </b>Create Field from User Meta >> Fields.</p>";
        $html .= "<p><b>Step 2. </b>Go to User Meta >> Forms. Drag and drop fields from right to left and save the form.</p>";
        $html .= "<p><b>Step 3. </b>write shortcode to your page or post. Shortcode: [user-meta type='profile' form='profile']</p>";
        $html .= "<p></p>";
        $html .= "<li>You may use type='none' for hide update button.</li>";
        $html .= "<li>You may create more then one form. Use form name in shortcode. e.g. [user-meta type='profile' form='your_form_name']</li>";
        $html .= "<li>Admin user can see all others frontend profile from User Administration screen. To enable this feature, go to User Meta >> User Meta, select profile page from Profile Page Selection and enable right sided checkbox.</li>";
        $html .= "<li>In Case of extra field, you need to define unique meta_key. That meta_key will be use to save extra data in usermeta table. Without defining meta_key, extra data won't save.</li>";
        return $html;
    }
    
    function showNotice(){
        global $userMeta;

        if( $this->isUpgradationNeeded() ){
            echo $userMeta->showError( "We found that, you have previous data which you may need to import. <span class='button' onclick='umUpgradeFromPrevious(this)'>Click Here</span> to import"  );            
        }
    }
    
    function ajaxUmCommonRequest(){
        global $userMeta;
        $userMeta->verifyNonce();        
        $this->runningUpgrade();
        die();
    }
    

}

?>