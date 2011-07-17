<?php

function user_meta_importer(){
    
	global $wpdb;
         
  	if (!current_user_can('manage_options')) {
    	wp_die( __('You do not have sufficient permissions to access this page.') );
  	}

	// if the form is submitted
	if ($_POST['mode'] == "submit") {
	        
        //reading csv file as array
        if($_FILES['csv_file']['tmp_name'])                       
            $arr_rows = file($_FILES['csv_file']['tmp_name']);
        else
             show_messages( 'It seems the file was not uploaded correctly.', 'error' ); 
             

		// loop around
		if (is_array($arr_rows)) {
		 		  
            //detrmining field name from first rows of CSV
            $first_row = $arr_rows[0];
            $meta_keys = split(",", $first_row);
                
            if( !in_array('username',$meta_keys) ) 
                show_messages( 'username field should be appear at first row in CSV file.', 'error' );
//            if(!( in_array('username',$meta_keys) AND in_array('email',$meta_keys) ))        
//                show_messages( 'username and email field should be appear at first row in CSV file.', 'error' );   
                                      
            $user_added = 0;    $user_updated = 0;  $user_skipped = 0;          
			foreach ($arr_rows as $row_id => $row) {
			 
                //Skip first row, because first row is being used as field_name/meta_key
                if($row_id>0){
                    
    				// split into values
    				$arr_values = split(",", $row);
                    
                    //fields, which are part of user table
                    $default_keys = array('username', 'email', 'password', 'nicename', 'website', 'display_name', 'nickname', 'first_name', 'last_name', 'description', 'reg_date_time', 'role', 'jabber', 'aim', 'yim' ); 
                                  
                    //assigning each field name as variable
                    foreach($meta_keys as $key_order => $key_name){
                        $key_name = trim($key_name);
                        $key_value = trim($arr_values[$key_order]);
                        $$key_name = $key_value;
                        
                        //populate user_meta
                        if(!in_array($key_name, $default_keys)){
                            $arr_usermeta[$key_name] = $key_value;
                        }
                    }     
                                        
        			//retrieve user_id if user exists                    
                    $user_exists = false;
                    if(username_exists($username)){
                        $user_id = username_exists($username);
                        $user_exists = true;
                    }elseif(email_exists($email)){
                        $user_id = username_exists($email);
                        $user_exists = true;
                    }

                    //assign value to trigger, for makaing decession for next action
                    $trigger = '';
                    if( ($_POST['overwrite']==true AND $user_exists==true) ){
                        $trigger = 'update_user';
                    }elseif($user_exists==false){
                        $trigger = 'add_user';
                    }                     

                    //Overwrite role if any role are assigned by form
                    if($_POST['user_role'])
                        $role = $_POST['user_role'];
                        
                    //Apply some filter    
                    $username = sanitize_title($username);   
                    if(!$username){ $trigger = 'skip_user'; }
                    if(isset($password)){
                        if(!$password)
                            $password = '123456';
                    }else
                        $password = '123456';
                                       
                    $email =  $email ? $email : $username . '@donotreplay.com' ;


                    //Set Default data
                    if(isset($nicename))    { $userdata[ 'user_nicename' ]  = $nicename; }
                    if(isset($website))     { $userdata[ 'user_url' ]       = $website; }
                    if(isset($display_name))    { $userdata[ 'display_name' ]  = $display_name; }
                    if(isset($nickname))    { $userdata[ 'nickname' ]  = $nickname; }
                    if(isset($first_name))    { $userdata[ 'first_name' ]  = $first_name; }
                    if(isset($last_name))    { $userdata[ 'last_name' ]  = $last_name; }
                    if(isset($description))    { $userdata[ 'description' ]  = $description; }
                    if(isset($reg_date_time))    { $userdata[ 'user_registered' ]  = $reg_date_time; }
                    if(isset($role))    { $userdata[ 'role' ]  = $role; }
                    if(isset($jabber))    { $userdata[ 'jabber' ]  = $jabber; }
                    if(isset($aim))    { $userdata[ 'aim' ]  = $aim; }
                    if(isset($yim))    { $userdata[ 'yim' ]  = $yim; }

                    //Implementation user add/update action
				    if( $trigger == 'add_user' ){
				        $user_id = wp_create_user( $username, $password, $email );
                        $userdata[ 'ID' ] = $user_id;
                        wp_update_user( $userdata );
                        $user_added++;
				    }                            
                    elseif( $trigger == 'update_user' ){
                        $userdata[ 'ID' ] = $user_id;
                        $userdata[ 'user_login' ] = $username;
                        $userdata[ 'user_email' ] = $email;
                        $userdata[ 'user_pass' ] = $password;
                        wp_update_user( $userdata );
                        $user_updated++;
                    }else{
                        $user_skipped++;
                    }
                        
                    //Add/Update user meta    
    				foreach ($arr_usermeta as $key => $value) {
    				    if( $trigger == 'add_user' )
                            add_user_meta( $user_id, $key, $value );
                        elseif( $trigger == 'update_user' )
                            update_user_meta( $user_id, $key, $value );
                    }                                
                 }   //end of if($row_id>0)

			}	// end of 'for each around arr_rows'
            show_messages( "CSV file have been imported successfully. User added: $user_added, User updated: $user_updated, User skipped: $user_skipped" );			
		} // end of 'if arr_rows is array'
		else {
            show_messages( 'It seems the file was not uploaded correctly.', 'error' );		
		}
	} 	// end of 'if mode is submit'

    user_meta_import_form($html_update);    
    
    
}

function user_meta_import_form($html_update = ''){
    global $wp_roles;
    $roles = $wp_roles->role_names;
    ?>
    <div class="wrap">	
    	<?php echo $html_update; ?>	
    	<div id="icon-users" class="icon32"><br /></div>
    	<h2>User Meta Import</h2>
    			
        <p>The CSV file should be in the following format:</p>
    	<ul> 	
            <li>1. First row of CSV file should contain feld_name/meta_key, seperated by comma(,)</li>
            <li>2. username should be appear in first row to pass validation</li>
            <li>3. Default Wordpress recognized fields are: username, email, password, nicename, website, display_name, nickname, first_name, last_name, description, reg_date_time</li>
            <li>4. All other fields are treated as meta_key and will be populated to usermeta table</li>
            <li>5. Use plain text as password, wordpress will encrypt it. If password is not appear in CSV default password will be 123456</li>
            <li>6. If nicename,nickname,display_name are not set, those field will be populated by username</li>
            <li>7. Currant date-time will be used if, reg_date_time is not set. Use 'Y-m-d H:i:s' date format while importing</li>
        </ul>
        
        <br /><br />
        
    	<form action="users.php?page=user-meta-import" method="post" enctype="multipart/form-data">
    		<input type="hidden" name="mode" value="submit">           
            <p>Select Role : <select name="user_role">
                <option></option>
                <?php foreach ($roles as $key => $value){ echo "<option value='$key'>$value</option>"; } ?>                            
            </select> (Leave blank if you want to use role mention by csv)</p>   
            <p><input type="checkbox" name="overwrite"/> Overwrite existing users</p>                     
            <p>Please select the CSV file you want to import below.</p>
    		<input type="file" name="csv_file" />		
    		<input type="submit" value="Import" />
    	</form>    
    	
    	<p style="color: red">Please back up your database before proceeding!</p>	
    </div>        
    <?php
}

?>