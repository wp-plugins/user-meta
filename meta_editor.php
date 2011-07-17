<?php

function user_meta_editor(){
     
    if(isset($_POST)){
        if($_POST['action'] == 'meta_update'){
            user_meta_editor_update($_POST);
            echo "<br /><div class='updated'>Meta Data Successfully updated</div>";    
        }elseif($_POST['action'] == 'group_update'){
            user_meta_group_editor_update($_POST);
            echo "<br /><div class='updated'>Meta Group Successfully updated</div>";               
        }       
    }
        
    user_meta_editor_form(); 
}


function user_meta_editor_populate(){
    $meta_data = get_option('user_meta_field');
    $meta_group = get_option('user_meta_group');
        
    if($meta_data){
        foreach($meta_data as $meta)
            user_meta_editor_form_field($meta['meta_key'], $meta['meta_label'], $meta['meta_description'], $meta['meta_position'], $meta_group);        
    }
}

function user_meta_group_editor_populate(){
    $meta_group = get_option('user_meta_group');
    if($meta_group){
        foreach($meta_group as $group)
            user_meta_group_editor_form_field($group['group_name'], $group['group_title']);        
    }
}

function user_meta_editor_update($args){    
    extract( $args );    
    
    foreach ($meta_key as $key => $value){       
        if($value){
            $meta_label[$key] = $meta_label[$key] ? $meta_label[$key] : $meta_key[$key];
            $result[] = array('meta_key' => $meta_key[$key], 'meta_label' => $meta_label[$key], 'meta_description' => $meta_description[$key], 'meta_position' => $meta_position[$key]);
        }        
    }    
      
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



function user_meta_editor_form_field($meta_key, $meta_label, $meta_description, $meta_position, $meta_group, $remove_button=true){    
    $meta_option_field = '<option>contact</option>';
    if($meta_group){
        foreach($meta_group as $group){
            $group_name = $group['group_name'];
            if($meta_position == $group_name)
                $meta_option_field .= '<option value="'.$group_name.'" selected="true">' . $group_name . '</option>';
            else    
                $meta_option_field .= '<option value="'.$group_name.'">' . $group_name . '</option>'; 
        }          
    }
        
    $remove_button = $remove_button ? "<a href='#' class='remove-meta add-new-h2'>Remove</a>" : "";
    echo "<p class='meta-field'>
            Meta Key: <input name='meta_key[]' value='$meta_key' style='width:20%;'/> 
            Meta Label : <input name='meta_label[]' value='$meta_label' style='width:15%;'/>   
            Description : <input name='meta_description[]' value='$meta_description'/>   
            Group : <select name='meta_position[]'>$meta_option_field</select>        
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


function user_meta_editor_form($html_update = ''){
    $meta_group = get_option('user_meta_group');
    ?>
    <div class="wrap">	
   	 <?php echo $html_update; ?>	
    	<div id="icon-users" class="icon32"><br /></div>
    	<h2>User Meta Editor</h2>   
        
        <h3>Profile field Group</h3>        
    	<form action="users.php?page=user-meta-editor" method="post">
    		<input type="hidden" name="action" value="group_update">                       
            <div id="group-container"><?php user_meta_group_editor_populate(); ?></div>   
            <div class="new-meta-group" style="display:none"><?php user_meta_group_editor_form_field('', '', false); ?></div>   
            <p id="add-meta-group"><a href="#" class="add-new-h2">Add New Group</a></p>
            <p><input type="submit" value="Save" /></p>   		
    	</form>          

        <h3>Add extra fields to user profile</h3>        
    	<form action="users.php?page=user-meta-editor" method="post">
    		<input type="hidden" name="action" value="meta_update">                       
            <div id="meta-container"><?php user_meta_editor_populate(); ?></div>   
            <div class="new-meta-field" style="display:none"><?php user_meta_editor_form_field('', '', '', '', $meta_group, false); ?></div>   
            <p id="add-meta"><a href="#" class="add-new-h2">Add New Meta</a></p>
            <p><input type="submit" value="Save" /></p>   		
    	</form> 
    	
    </div>        
    <?php
}

?>