<?php

if(!class_exists('userMetaProfileBackend')){
    class userMetaProfileBackend {
        
        function __construct(){
            add_filter( 'user_contactmethods', array(__CLASS__, 'contactUpdate'), 10, 1 );
            add_action( 'show_user_profile', array(__CLASS__, 'profileField') );
            add_action( 'edit_user_profile', array(__CLASS__, 'profileField') );
            add_action( 'personal_options_update', array(__CLASS__, 'profileUpdate') );
            add_action( 'edit_user_profile_update', array(__CLASS__, 'profileUpdate') );
        }
        
        function contactUpdate($contactmethod){
            global $um_fields;
            
            if($um_fields){
                foreach($um_fields as $meta){
                    $meta_key = $meta['meta_key'];
                    $meta_position = $meta['meta_position'];
                    if($meta_position == 'contact' or !$meta_position){
                        $contactmethod[$meta_key] = $meta['meta_label'];
                    }            
                }
            }    
            
        	/*unset($contactmethod['aim']);
        	unset($contactmethod['yim']);
        	unset($contactmethod['jabber']);  */  
            
            return $contactmethod;
        }
                 
                 
        function profileField( $user ) {
            global $um_groups, $um_fields;
            
            if($um_groups){
                foreach($um_groups as $group){
                    echo "<h3>" . $group["group_title"] . "</h3> <table class='form-table'>";
                    
                    foreach($um_fields as $meta){
                        $meta_key = $meta['meta_key'];
                        $meta_position = $meta['meta_position'];
                        if($meta_position == $group["group_name"]){
                            ?>
                            <tr>
                            <th><label for="<?php echo $meta_key; ?>"><?php _e($meta['meta_label']); ?></label></th>
                            <td>
                            <input type="text" name="<?php echo $meta_key; ?>" id="<?php echo $meta_key; ?>" value="<?php echo esc_attr( get_the_author_meta( $meta_key, $user->ID ) ); ?>" class="regular-text" /><br />
                            <span class="description"><?php _e($meta['meta_description']); ?></span>
                            </td>
                            </tr>
                            <?php            
                        }            
                    }
                    echo "</table>";
                }        
            }
                
        }

        //update profile by post data 
        function profileUpdate( $user_id ) {
            global $um_fields;
            //if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
               
            if($um_fields){
                foreach($um_fields as $meta){
                    $meta_key = $meta['meta_key'];
                    $meta_position = $meta['meta_position'];
                    if(!($meta_position == 'contact' or !$meta_position)){
                        update_user_meta( $user_id, $meta_key, $_POST[$meta_key] );
                    }            
                }
            }   
        }
        
    }
}       

$userMetaProfileBackend = new userMetaProfileBackend;

?>