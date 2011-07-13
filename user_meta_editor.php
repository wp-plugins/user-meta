<?php


function user_meta_editor(){
     
    if(isset($_POST)){
        if($_POST['mode']){
            user_meta_editor_update($_POST);
            echo "<br /><div class='updated'>Meta Data Successfully updated</div>";    
        }        
    }
    
    user_meta_editor_form();
//    $meta_data = get_option('user_meta_field');
//    var_dump($meta_data);    
}


function user_meta_editor_populate(){
    $meta_data = get_option('user_meta_field');
    if($meta_data){
        foreach($meta_data as $key => $value)
            user_meta_editor_form_field($key, $value);        
    }
}

function user_meta_editor_form_field($meta_key, $meta_label,$remove_button=true){
    $remove_button = $remove_button ? "<a href='#' class='remove-meta add-new-h2'>Remove</a>" : "";
    echo "<p class='meta-field'>
            Meta Key: <input name='meta_key[]' value='$meta_key'/> 
            Meta Label : <input name='meta_label[]' value='$meta_label'/>          
            $remove_button
            </p>";
}


function user_meta_editor_form($html_update = ''){

    ?>
    <div class="wrap">	
   	 <?php echo $html_update; ?>	
    	<div id="icon-users" class="icon32"><br /></div>
    	<h2>User Meta Editor</h2>
        <h3>Add extra fields to user profile</h3>
        
    	<form action="users.php?page=user-meta-editor" method="post">
    		<input type="hidden" name="mode" value="submit">                       
            <div id="meta-container"><?php user_meta_editor_populate(); ?></div>   
            <div class="new-meta-field" style="display:none"><?php user_meta_editor_form_field('', '', false); ?></div>   
            <p id="add-meta"><a href="#" class="add-new-h2">Add New Meta</a></p>
            <p><input type="submit" value="Save" /></p>   		
    	</form>    
    	
    </div>        
    <?php
}

?>