<?php

/*
* Add user-meta-profile shortcode for showing and updatting user profile
*/

if (!class_exists('userMetaProfilePage')){
    class userMetaProfileFrontend {

        //Constructor
        function __construct(){
            add_action('wp_head', array(__CLASS__, 'addCss'));
            add_shortcode('user-meta-profile', array($this, 'showProfile'));
            $this->addScript();
        }
        
        function addScript(){
            $um_plugin_url = userMeta::pluginUrl();
            if (!is_admin()) {
                wp_enqueue_script('validationEngine-en', $um_plugin_url."/js/jquery.validationEngine-en.js", array('jquery'),'',true);
                wp_enqueue_script('validationEngine', $um_plugin_url."/js/jquery.validationEngine.js", array('jquery'),'',true);
                wp_enqueue_script('user-meta-script', $um_plugin_url."/js/script.js", array('jquery'),'',true);
            }
        }
        
        //add css file while showing front end
        function addCss() {
            $um_plugin_url = userMeta::pluginUrl();
            if (!is_admin()) {
                echo "<link href='{$um_plugin_url}/css/validationEngine.jquery.css' rel='stylesheet' type='text/css' media='screen' />";
                echo "<link href='{$um_plugin_url}/css/template.css' rel='stylesheet' type='text/css' media='screen' />";
            }            
        }

        
        function showProfile(){            
            if(is_user_logged_in()){
                if(isset($_POST)){                                
                    if(isset($_POST['profile_update'])){
                        $this->updateProfile();
                    }        
                }               
                $this->inputForm();                
            }else{
                echo 'please login to view this page';
            }            
        }
        
        //will call while after checking error
        function updateProfile(){
            global $user_ID;                   

            $userdata = $_POST;
            $userdata[ 'ID' ] = $user_ID;
            //Update user profile data
            if(wp_update_user( $userdata )){
                //update_usermeta data send by $_POST
                userMetaProfileBackend::profileUpdate( $user_ID );
                //user_meta_profile_fields_save( $user_id );
            }
                        
            //Upload avatar
            if ( !empty( $_FILES['user_meta_avatar']['name'] ) ){
    			$mimes = array(
    				'jpg|jpeg|jpe' => 'image/jpeg',
    				'gif' => 'image/gif',
    				'png' => 'image/png',
    				'bmp' => 'image/bmp',
    				'tif|tiff' => 'image/tiff'
    			);
    		
    			// front end (theme my profile etc) support
    			if ( ! function_exists( 'wp_handle_upload' ) )
    				require_once( ABSPATH . 'wp-admin/includes/file.php' );
    		
    			$avatar = wp_handle_upload( $_FILES['user_meta_avatar'], array( 'mimes' => $mimes, 'test_form' => false ) );                 
                if(!update_user_meta( $user_ID, 'user_meta_avatar', $avatar['url'] ))
                    add_user_meta( $user_ID, 'user_meta_avatar', $avatar['url'], true ) ;         
            }                                                
        }
        
        function showField($meta){
            
        }
        
        //HTML input form
        function inputForm($args=array()){            
            global $um_groups, $um_fields, $um_field_checked, $user_ID;                                    
            $user_info = get_userdata($user_ID);
            
            echo "<h3>Contact Details</h3>";
                            
            echo "<form action='' method='post' enctype='multipart/form-data' id='um-frontend-profile-form' class='formular'>";        
            echo "<input type='hidden' name='user_id' value='$current_user->ID' />";
            
            //Showing default fields 
            $default_userfields = userMeta::getDefaultUserFields();   
            if($um_field_checked){        
                foreach($um_field_checked as $key => $val){
                    
                    if($key <> 'avatar'){
                        if($key == 'user_email'){
                            $class = 'validate[required,custom[email]] text-input';
                            echo "<label for='$key' >{$default_userfields[$key]} <span style='color:red'>*</span></label>";
                            echo userMeta::createInput($key, $user_info->$key, 'text', array('id' => $key, 'class' => $class));
                            echo "<br />";                                                 
                        }   
                        else{
                            $class = 'text-input';
                            echo "<label for='$key' >{$default_userfields[$key]}</label>";
                            echo userMeta::createInput($key, $user_info->$key, 'text', array('id' => $key, 'class' => $class));
                            echo "<br />";                            
                        }        
                    }           
                }
             }   
            
            //Combind all groups and initiate empty veriable for each groups
            if($um_groups){
                foreach($um_groups as $group){
                    $group_name[$group["group_name"]] = $group["group_title"];
                    $var_name = $group["group_name"];
                    $$var_name = '';
                }                   
            }
            
            if($um_fields){
                foreach($um_fields as $meta){
                    $meta_key = $meta['meta_key'];                        
                    $meta_position = $meta['meta_position'];
                    $meta_option = isset($meta['meta_option']) ? unserialize($meta['meta_option']) : array();
                    //$validate = $meta['meta_validate']
                    $class = ($meta['meta_required'] == 'yes') ? "validate[required] text-input" : "text-input";
                    $label = ($meta['meta_required'] == 'yes') ? $meta['meta_label']."<span style='color:red'>*</span>" : $meta['meta_label'];
                    $fields = "<label for='$meta_key' >$label</label>" . userMeta::createInput($meta_key, $user_info->$meta_key, $meta['meta_type'], array('id' => $meta_key, 'class' => $class), $meta_option) . "<span class='description'>{$meta['meta_description']}</span><br />";                      
                    
                    //if(in_array($meta_position, $group_name))
                    if(isset($group_name[$meta_position]))
                        $$meta_position .= $fields;
                    else
                        $contact .= $fields;                                                                     
                }
             }   
            
            
            //Showing fields
            echo $contact;
            if($group_name){
                foreach($group_name as $key => $val){
                    if($$key){
                        echo "<h3>{$val}</h3>";
                        echo $$key;                        
                    }                    
                }                   
            }            
                    
            
            //echo $this->createInput('$meta_key', 'val2', 'select', '', array('val1','val2','val3'));
            //<label for="call"></label><input value="" class="validate[required,custom[phone]] text-input" type="text" name="call" id="call" />
            
            if(isset($um_field_checked['avatar'])){
                echo get_avatar( $user_ID, '', $user_info->user_meta_avatar);
                echo "<input type='file' name='user_meta_avatar' id='user_avater' />";                    
            }
            
            echo "<br /><br /><input type='submit' name='profile_update' value='Update' />";
         
            echo "</form>";
                 
        }        
    }
}

$userMetaProfileFrontend = new userMetaProfileFrontend;

?>