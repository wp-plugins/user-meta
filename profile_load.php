<?php

add_filter('user_contactmethods','user_meta_contactmethod',10,1);

function user_meta_contactmethod($contactmethod){
    global $meta_data;
    
    if($meta_data){
        foreach($meta_data as $meta){
            $meta_key = $meta['meta_key'];
            $meta_position = $meta['meta_position'];
            if($meta_position == 'contact' or !$meta_position){
                $contactmethod[$meta_key] = $meta['meta_label'];
            }            
        }
    }    
    
	unset($contactmethod['aim']);
	unset($contactmethod['yim']);
	unset($contactmethod['jabber']);    
    
    return $contactmethod;
}


add_action( 'show_user_profile', 'user_meta_profile_fields' );
add_action( 'edit_user_profile', 'user_meta_profile_fields' );
 
function user_meta_profile_fields( $user ) {
    global $meta_group, $meta_data;
    
    if($meta_group){
        foreach($meta_group as $group){
            echo "<h3>" . $group["group_title"] . "</h3> <table class='form-table'>";
            
            foreach($meta_data as $meta){
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
 
add_action( 'personal_options_update', 'user_meta_profile_fields_save' );
add_action( 'edit_user_profile_update', 'user_meta_profile_fields_save' );
 
function user_meta_profile_fields_save( $user_id ) {
    global $meta_data;
    //if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
       
    if($meta_data){
        foreach($meta_data as $meta){
            $meta_key = $meta['meta_key'];
            $meta_position = $meta['meta_position'];
            if(!($meta_position == 'contact' or !$meta_position)){
                update_user_meta( $user_id, $meta_key, $_POST[$meta_key] );
            }            
        }
    }   
}
?>