<?php

function show_messages( $msg, $class = 'updated' ){
    $html_update = "<br/><div class=$class>$msg</div>";
    user_meta_import_form($html_update); 
    exit;
}

function user_meta_install(){
    add_option('user_meta_field', '', '', 'no');
}

function user_meta_contactmethod($contactmethod){
    $contactmethod_new = get_option('user_meta_field');
    if(!$contactmethod_new)
        return $contactmethod;
    return array_merge($contactmethod, $contactmethod_new);
}

function user_meta_editor_update($args){    
    extract( $args );   
    $contactmethod = array_combine($meta_key,$meta_label);  
    
    foreach($contactmethod as $key => $value){
        if($key){
            if($value)
                $result[$key] = $value;
            else
                $result[$key] = $key;    
        }
    }    
    update_option( 'user_meta_field', $result );        
}


//function role_name_exists($role_name){
//    global $wp_roles;
//    $roles = $wp_roles->role_names;
//    if($roles[$role_name])
//        return true;
//}

?>