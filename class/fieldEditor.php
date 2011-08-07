<?php

/*
* Editor for backend and frontend user profile
* Save value to user_meta_group, user_meta_field and user_meta_field_checked wp-option
*/

if(!class_exists('userMetaFieldEditor')){
    class userMetaFieldEditor {
        
        function __construct(){
            add_action('admin_menu', array(__CLASS__, 'menuItem'));
        }
        
        function menuItem(){
            $page = add_submenu_page( 'users.php', 'Meta Editor', 'Meta Editor', 'manage_options', 'user-meta-editor', array(__CLASS__, 'metaEditor'));
            add_action("admin_print_scripts-$page", array(__CLASS__, 'user_meta_scripts'));
        }
        
        function user_meta_scripts(  ) {
            wp_enqueue_script( "user-meta-js", userMeta::pluginUrl()."/js/script.js", array( 'jquery' ) );
        }        
                
        function metaEditor(){
             
            if(isset($_POST)){
                if($_POST['action'] == 'meta_update'){
                    self::user_meta_editor_update($_POST);
                    echo "<br /><div class='updated'>Meta Data Successfully updated</div>";    
                }elseif($_POST['action'] == 'group_update'){
                    self::user_meta_group_editor_update($_POST);
                    echo "<br /><div class='updated'>Meta Group Successfully updated</div>";               
                }       
            }
                
            self::inputForm(); 
        }
        
        
        function user_meta_editor_populate(){
            $meta_data = get_option('user_meta_field');
            $meta_group = get_option('user_meta_group');
                
            if($meta_data){
                foreach($meta_data as $meta)
                    self::user_meta_editor_form_field($meta, $meta_group);      
            }
        }
        
        function user_meta_group_editor_populate(){
            $meta_group = get_option('user_meta_group');
            if($meta_group){
                foreach($meta_group as $group)
                    self::user_meta_group_editor_form_field($group['group_name'], $group['group_title']);        
            }
        }
        
        function user_meta_editor_update($args){    
            extract( $args );    
            
            foreach ($meta_key as $key => $value){       
                if($value){
                    $meta_label[$key] = $meta_label[$key] ? $meta_label[$key] : $meta_key[$key];
                    $result[] = array(
                        'meta_key' => $meta_key[$key],
                        'meta_label' => $meta_label[$key],
                        'meta_description' => $meta_description[$key],
                        'meta_type' => $meta_type[$key],
                        'meta_position' => $meta_position[$key],
                        'meta_required' => $meta_required[$key],
                        //'meta_validation' => $meta_validation[$key],
                        //'meta_class' => $meta_class[$key],
                        'meta_option' => serialize(explode(",", $meta_option[$key])),
                    );
                }        
            }    
              
            update_option( 'user_meta_field_checked', $show);  
            return update_option( 'user_meta_field', $result ) ? true: false;         
        }
        
        function user_meta_group_editor_update($args){    
            extract( $args );   
            
            foreach ($group_name as $key => $value){       
                if($value){
                    $group_title[$key] = $group_title[$key] ? $group_title[$key] : $group_name[$key];
                    $result[] = array('group_name' => $group_name[$key], 'group_title' => $group_title[$key]);
                }        
            }
              
            return update_option( 'user_meta_group', $result ) ? true: false;        
        }
        
        
        
        function user_meta_editor_form_field($meta=array(), $meta_group, $remove_button=true){   
            extract($meta);
            //expected meta to extract: $meta_key, $meta_label, $meta_description, meta_type, $meta_position, $meta_required, $meta_validation, $meta_class $meta_option
            
            //add label 'contact' as first value of group
            $group_name[] = 'contact';
            if($meta_group){
                foreach($meta_group as $group){
                    $group_name[] = $group['group_name'];
                }
            }
                            
            $remove_button = $remove_button ? "<a href='#' class='remove-meta add-new-h2'>Remove</a>" : "";
            $meta_option = $meta_option ? implode(',', unserialize($meta_option)) : '';
            echo "<p class='meta-field'>
                    
                    Meta Key : ". userMeta::createInput('meta_key[]', $meta_key, 'text', array('style' => '20%'))."
                    Label : ". userMeta::createInput('meta_label[]', $meta_label, 'text', array('style' => '15%'))."
                    Description : ". userMeta::createInput('meta_description[]', $meta_description, 'text', array('style' => '15%'))."
                    <br />
                    Type : ". userMeta::createInput('meta_type[]', $meta_type, 'select', '', array('textbox','dropdown'))."    
                    Group : ". userMeta::createInput('meta_position[]', $meta_position, 'select', '', $group_name)."
                    Required : ". userMeta::createInput('meta_required[]', $meta_required, 'select', '', array('no','yes')).  /*"
                    Validation : ". userMeta::createInput('meta_validation[]', $meta_validation, 'select', '', array('','email','url','phone'))." 
                    CSS Class : ". userMeta::createInput('meta_class[]', $meta_class) . */"
                    <br />
                    Options : ". userMeta::createInput('meta_option[]', $meta_option)." (Separated by comma)
                    <br /><br />
                    $remove_button
                    </p>";
        }
        
        function user_meta_group_editor_form_field($group_name, $group_title,$remove_button=true){
            $remove_button = $remove_button ? "<a href='#' class='remove-meta add-new-h2'>Remove</a>" : "";
            echo "<p class='meta-group'>
                    Group Name: <input name='group_name[]' value='$group_name'/> 
                    Group Title : <input name='group_title[]' value='$group_title'/>     
                    $remove_button
                    </p>";
        }
        
        
        function inputForm($html_update = ''){
            $meta_group = get_option('user_meta_group');
            ?>
            <div class="wrap">	
           	 <?php echo $html_update; ?>	
            	<div id="icon-users" class="icon32"><br /></div>
            	<h2>User Meta Editor</h2>   
                
                <h3>Profile field Group</h3>        
            	<form action="users.php?page=user-meta-editor" method="post">
            		<input type="hidden" name="action" value="group_update">                       
                    <div id="group-container"><?php self::user_meta_group_editor_populate(); ?></div>   
                    <div class="new-meta-group" style="display:none"><?php self::user_meta_group_editor_form_field('', '', false); ?></div>   
                    <p id="add-meta-group"><a href="#" class="add-new-h2">Add New Group</a></p>
                    <p><input type="submit" value="Save" /></p>   		
            	</form>          
        
                <h3>Add extra fields to user profile</h3>        
            	<form action="users.php?page=user-meta-editor" method="post">
            		<input type="hidden" name="action" value="meta_update">                       
                    <div id="meta-container"><?php self::user_meta_editor_populate(); ?></div>   
                    <div class="new-meta-field" style="display:none"><?php self::user_meta_editor_form_field(array(), $meta_group, false); ?></div>   
                    <p id="add-meta"><a href="#" class="add-new-h2">Add New Meta</a></p>
                    
                    <?php 
                    $field_checked = get_option('user_meta_field_checked');
                    $default_userfields = userMeta::getDefaultUserFields();
                    $default_userfields['avatar'] = 'Avatar';
                    foreach($default_userfields as $key => $val){
                        if(!($key == 'user_login' OR $key == 'user_registered' OR $key == 'role' OR $key == 'user_pass')){
                            if(isset($field_checked[$key]))
                                echo "<p><input type='checkbox' name='show[$key]' checked='true' /> Show $val<p>";
                            else
                                echo "<p><input type='checkbox' name='show[$key]' /> Show $val<p>";   
                        }                
                    }
                                        
                    ?>
                                        
                    <p><input type="submit" value="Save" /></p>   		
            	</form> 
                
                <h3>Shortcode</h3>
                <p><b>[user-meta-profile] </b> : Frontend user profile</p>
            	
            </div>        
            <?php
        }               
    }
}       


$userMetaFieldEditor = new userMetaFieldEditor;     

?>