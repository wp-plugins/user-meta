<?php

if(!class_exists('umPreloads')){
    class umPreloads {
        
        function __construct(){   
            global $userMeta;
            add_filter( 'get_avatar', array( $this, 'getAvatar' ), 10, 5 );
            
            add_filter( 'manage_users_columns', array( $this, 'profileLinkHeader' ) );
            add_filter( 'manage_users_custom_column', array( $this, 'profileLink' ), 10, 3 ); 
              
            add_filter( 'user_meta_running_upgrade', array( $userMeta, 'runningUpgrade' ) );
             
            add_action( 'wp_ajax_um_common_request',      array($userMeta, 'ajaxUmCommonRequest' ) );       
        }
        
        function getAvatar( $avatar = '', $id_or_email, $size = '96', $default = '', $alt = false ){
            if ( is_numeric($id_or_email) )
    	       $user_id = (int) $id_or_email;
            elseif( is_string($id_or_email) )
                $user_id = email_exists($id_or_email);
                
            if( !isset($user_id) ) return $avatar;
                
            $uploads = wp_upload_dir();
            $umAvatar = get_user_meta( $user_id, 'user_avatar', true );
            if($umAvatar){
                $avatarPath = $uploads['basedir'] . $umAvatar;
                $image_sized = image_resize( $avatarPath, $size, $size, true );
                if( is_wp_error($image_sized) )
                    print_r( $image_sized );
                
                $avatarUrl = str_replace( $uploads['basedir'], $uploads['baseurl'], $image_sized );
                $avatar = "<img src='$avatarUrl' />";
            }
                            
            return $avatar;            
    	}
        
        function profileLinkHeader( $columnHeaders ){
            global $userMeta;
            if( isset($userMeta->settings['profile_in_admin']) AND isset($userMeta->settings['profile_page'])  ){
                $columnHeaders['front_profile'] = 'Front Profile';
            }           
            return $columnHeaders;            
        }
        
        function profileLink( $columnValue, $columnName, $userID ){
            global $userMeta;
            if ($columnName == 'front_profile' ) {
                $permalink = get_permalink( $userMeta->settings['profile_page'] );    
                $separator = strstr( $permalink, '?' ) ? '&' : '?'; 
                $columnValue = $permalink ? "<a href='{$permalink}{$separator}user_id=$userID' target='_blank'>Profile</a>" : "No Profile Page";
            }
            return $columnValue;            
        }       
               
    }
}       
  

?>