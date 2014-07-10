<?php
global $userMeta;

switch ( $field['field_type'] ) {

    case 'multiselect' :
        $fieldType              = 'multiselect';

        if ( ! empty( $field['placeholder'] ) )
            $field['field_options']['placeholder'] = $field['placeholder'];

        //$json['single'] = true;
        //$json['filter'] = true;
        //$json['multiple'] = true;

        $json = ! empty( $field['field_options'] ) ? json_encode( $field['field_options'] ) : '';
        $moreContent = '<script type="text/javascript">jQuery(document).ready(function(){jQuery("#'.$inputID.'").multipleSelect('.$json.');});</script>';
    break;
        
    case 'blogname' :
        if( $actionType <> 'registration' ){
            $showInputField=false; 
            return;
        }	

        $active_signup = get_site_option( 'registration' );
        if ( !$active_signup )
            $active_signup = 'all';

        $active_signup = apply_filters( 'wpmu_active_signup', $active_signup ); // return "all", "none", "blog" or "user"
        if ( ! ( $active_signup == 'all' || $active_signup == 'blog' ) ){
            $showInputField = false; 
            $html = $userMeta->showMessage( __('Site registration has been disabled.', $userMeta->name), 'info' );
            return $html;		
        }

            global $current_site;

            $html .= $userMeta->wp_nonce_field( 'blogname', 'um_newblog', false, false );

            $field['field_name'] = 'blogname';
            $fieldTitle = ! is_subdomain_install() ? __('Site Name',$userMeta->name) : __('Site Domain',$userMeta->name);
            if ( !is_subdomain_install() )
                $field['before']	=  '<span class="prefix_address">' . $current_site->domain . $current_site->path . '</span><br />';
            else
                $field['after']		= '<span class="suffix_address">.' . ( $site_domain = preg_replace( '|^www\.|', '', $current_site->domain ) ) . '</span><br />';

            $field2['field_name']	= 'blog_title'; 
            $field2['fieldTitle']   = __( 'Site Title ', $userMeta->name ) ;

    break;
        
    //case 'name' :

        
    case 'email' :
        $validation .= "custom[email],";
    break;
        
    case 'url' :
       $validation .= "custom[url],";
    break;
        
    case 'phone' :
        $validation    .= "custom[phone],";
    break;
        
    case 'custom' :
        if ( ! empty( $field['regex'] ) )
            $validation    .= "custom[umCustomField_{$field['field_id']}],";
    break;
        
    case 'country' :
        $fieldType      = 'select';
        if( isset($field['country_selection_type']) ) {
            $by_key = ($field['country_selection_type'] == 'by_country_code') ? true : false;
        }
        $fieldOptions   = $userMeta->countryArray();
        //array_unshift( $fieldOptions, '' );
        $fieldOptions= array_merge(array(''=>''),$fieldOptions);
    break;
        
    case 'number' :
        $validation     .= empty( $field['integer_only'] ) ? "custom[number]," : "custom[integer],";

        if( isset( $field['min_number'] ) ) :
            $validation .= "min[{$field['min_number']}],";
        endif;
        if( isset( $field['max_number'] ) ) :
            $validation .= "max[{$field['max_number']}],";
        endif;     
    break;
        
    case 'datetime' :
        if( $fieldReadOnly == 'readonly' )
            $isDisabled = true;

        if( empty($field['allow_custom']) )
            $fieldReadOnly = 'readonly';

        $dateFormat = !empty( $field['date_format'] ) ? $field['date_format'] : 'yy-mm-dd';

        if( $field['datetime_selection'] == 'date' ) :
            if( empty( $field['field_options']['dateFormat'] ) )
                $field['field_options']['dateFormat'] = $dateFormat;
            if( !isset( $field['field_options']['changeYear'] ) )
                $field['field_options']['changeYear'] = true;
            $jsMethod = '.datepicker(' . json_encode( $field['field_options'] ) . ');';
        elseif( $field['datetime_selection'] == 'datetime' ) :
            if( empty( $field['field_options']['dateFormat'] ) )
                $field['field_options']['dateFormat'] = $dateFormat;
            if( empty( $field['field_options']['timeFormat'] ) )
                $field['field_options']['timeFormat'] = 'hh:mm:ss';        
            if( !isset( $field['field_options']['changeYear'] ) )
                $field['field_options']['changeYear'] = true;
            $jsMethod = '.datetimepicker(' . json_encode( $field['field_options'] ) . ');'; 
        elseif( $field['datetime_selection'] == 'time' ) :
            if( empty( $field['field_options']['timeFormat'] ) )
                $field['field_options']['timeFormat'] = 'hh:mm:ss';  
            $jsMethod = '.timepicker(' . json_encode( $field['field_options'] ) . ');';               
        endif;

        $moreContent = '<script type="text/javascript">jQuery(document).ready(function(){jQuery("#'.$inputID.'")'.$jsMethod.'});</script>';
    break;
        
    case 'image_url' :
        if( $field['field_value'] ){
            $fieldResultContent = "<img src =\"{$field['field_value']}\" />";
        }

        $validation .= "custom[url],";
        $fieldResultDiv = true;
        $onBlur     = "umShowImage(this)";
    break;
        
    //case 'scale' :

    
    // Formatting Fields
    
    case 'page_heading' :
        // Need to copy some code to generateForm
        if( $inSection )
             $html .= "</div>";               
        $previousPage = $currentPage - 2;
        if( $isPrevious ){
            //$html .= "<input type='button' onclick='umPageNavi($previousPage,false)' value='" . __( 'Previous', $userMeta->name ) . "'>"; 
            $html .= $userMeta->createInput( "", "button", array(
                "value"     =>  __( 'Previous', $userMeta->name ),
                "onclick"   => "umPageNavi($previousPage, false, this)",
                "class"     => "previous_button " . !empty( $form['button_class'] ) ? $form['button_class'] : "",
            ) ); 
        }
        if( $isNext ){
            //$html .= "<input type='button' onclick='umPageNavi($currentPage,true)' value='" . __( 'Next', $userMeta->name ) . "'>";               
            $html .= $userMeta->createInput( "", "button", array(
                "value"     =>  __( 'Next', $userMeta->name ),
                "onclick"   => "umPageNavi($currentPage, true, this)",
                "class"     => "next_button " . !empty( $form['button_class'] ) ? $form['button_class'] : "",
            ) );             
        }
        if( $inPage )
             $html .= "</div>";    

        $divStyle = $divStyle ? "style=\"$divStyle\"" : null;       
        $html .= "<div id=\"um_page_segment_$currentPage\" class=\"um_page_segment $divClass\" $divStyle>";      
        if( $fieldTitle )
            $html .= "<h3>$fieldTitle</h3>";        
        if( isset( $field['description'] ) )
            $html .= "<div class=\"um_description\">{$field['description']}</div>"; 
        if( isset( $field['show_divider'] ) )
            $html .= "<div class=\"pf_divider\"></div>";    

        $noMore = true;
        return $html;
    break;
        
    case 'section_heading' :
        if( $inSection )
             $html .= "</div>";

        $divStyle = $divStyle ? "style=\"$divStyle\"" : null;
        $html .= "<div class=\"um_group_segment $divClass\" $divStyle>";
        if( $fieldTitle )
            $html .= "<h4>$fieldTitle</h4>";

        if( isset( $field['description'] ) )
            $html .= "<div class=\"um_description\">{$field['description']}</div>";  
        if( isset( $field['show_divider'] ) )
            $html .= "<div class=\"pf_divider\"></div>";  

        $noMore = true;
        return $html;
    break;
        
    case 'html' :
        if( $fieldTitle )
            $html .= "<label class=\"pf_label\">$fieldTitle</label>";    
        if( isset( $field['description'] ) )
            $html .= "<div class=\"um_description\">{$field['description']}</div>";  
        $html .= isset($field['field_value']) ? html_entity_decode( $field['field_value'] ) : null;   

        $noMore = true; 
        return $html;  
    break;
        
    case 'captcha' :
        $general    = $userMeta->getSettings( 'general' );
        $pass = true;
        if( !empty( $field['non_admin_only'] ) )
            if( $userMeta->isAdmin() ) $pass = false;
        if( !empty( $field['registration_only'] ) )
            if( $actionType <> 'registration' ) $pass = false;    

        if( $pass ):
            if( ! function_exists( 'recaptcha_get_html' ) )
                require_once( $userMeta->pluginPath . '/framework/helper/recaptchalib.php');

            $publicKey = '6Lc5iMsSAAAAADBfS_8V5mX_t9qC6b4R_KSHJVcd';
            if( isset( $general['recaptcha_public_key'] ) )
                $publicKey = $general['recaptcha_public_key'];
            else
                $html .= "<span style='color:red'>". __( 'Please set public and private keys in User Meta >> Settings Page', $userMeta->name ) ."</span>";
            $html .= "<label id=\"$labelID\" class=\"$label_class\" for=\"$inputID\">$fieldTitle</label>";
            $leftMarginClass = @$field['title_position'] == 'left' ? 'um_left_margin' : '';

            if( empty( $field['field_options']['captcha_theme'] ) ){
                if( !empty( $field['captcha_theme'] ) )
                    $field['field_options']['theme'] = $field['captcha_theme'];
            }

            if( !empty( $field['field_options'] ) )
                $html .= "<script type=\"text/javascript\">var RecaptchaOptions = " . json_encode( $field['field_options'] ) . ";</script>";

            $html .= "<div class=\"$leftMarginClass\">" . recaptcha_get_html( $publicKey ) ."</div>";  

            $descriptionClass = ! empty( $field['description_class'] ) ? $field['description_class'] : 'um_description';
            if( @$field['title_position'] == 'left' )
                $descriptionClass .= ' um_left_margin';

            if( isset( $field['description'] ) ) $html .= "<div class=\"$descriptionClass\">{$field['description']}</div>";
        endif;

        $noMore = true;  
        return @$html;
}