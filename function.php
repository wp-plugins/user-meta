<?php

$meta_group = get_option('user_meta_group');
$meta_data = get_option('user_meta_field');


function show_messages( $msg, $class = 'updated' ){
    $html_update = "<br/><div class=$class>$msg</div>";
    user_meta_import_form($html_update); 
    exit;
}








//function role_name_exists($role_name){
//    global $wp_roles;
//    $roles = $wp_roles->role_names;
//    if($roles[$role_name])
//        return true;
//}

?>