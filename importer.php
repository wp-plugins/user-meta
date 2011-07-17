<?php

function user_meta_importer(){
    
	global $wpdb;
         
  	if (!current_user_can('manage_options')) {
    	wp_die( __('You do not have sufficient permissions to access this page.') );
  	}

	// if the form is submitted
	if ($_POST['mode'] == "submit") {
	                    
        if(!$_FILES['csv_file']['tmp_name'])                       
             show_messages( 'It seems the file was not uploaded correctly.', 'error' );              
             
        $default_keys = array('username', 'email', 'password', 'nicename', 'website', 'display_name', 'nickname', 'first_name', 'last_name', 'description', 'reg_date_time', 'role', 'jabber', 'aim', 'yim' ); 

        $file = fopen($_FILES['csv_file']['tmp_name'],"r");
        //Read first line as array
        $meta_keys = fgetcsv($file);        
 
        if( !in_array('username',$meta_keys) ) 
            show_messages( 'username field should be appear at first row in CSV file.', 'error' );        
            
        $user_added = 0;    $user_updated = 0;  $user_skipped = 0;
        while(! feof($file)){
            
            //Read line as array
            $arr_values = fgetcsv($file);
                       
            //assigning each field name as variable
            foreach($meta_keys as $key_order => $key_name){
                $key_name = trim($key_name);
                $key_value = trim($arr_values[$key_order]);
                //$key_value = esc_sql($key_value);
                $$key_name = $key_value;
                
                //populate user_meta
                if(!in_array($key_name, $default_keys)){
                    $arr_usermeta[$key_name] = $key_value;
                }
            } 

			//retrieve user_id if user exists                    
            $user_exists = false;
            
            //$username = sanitize_title($username);  
            $email =  $email ? $email : $username . '@donotreplay.com' ; 

            if(email_exists($email)){
                $user_id = email_exists($email);
                $user_exists = true;
            }
            elseif(username_exists($username)){
                $user_id = username_exists($username);
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
            if(!$username){ $trigger = 'skip_user'; }
            if(isset($password)){
                if(!$password)
                    $password = '123456';
            }else
                $password = '123456';
                               
            $userdata[ 'user_login' ] = $username;
            $userdata[ 'user_email' ] = $email;
            $userdata[ 'user_pass' ] = $password;
                
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
                unset($userdata[ 'ID' ]);
                unset($user_id);                
                $user_id = wp_insert_user( $userdata );
                if($user_id){
                    echo $username."<br />";
                    $user_added++;
                }                    
		    }                            
            elseif( $trigger == 'update_user' ){
                $userdata[ 'ID' ] = $user_id;
                if(wp_update_user( $userdata )){
                    echo $username."<br />";
                    $user_updated++;
                }                
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
            
            //Unset all value            
            foreach($meta_keys as $key_name){
                $key_name = trim($key_name);
                unset($$key_name);
            }             
                                                    
        }
        fclose($file);
        show_messages( "CSV file have been imported successfully. User added: $user_added, User updated: $user_updated, User skipped: $user_skipped" );			
	
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
            <li>1. Field of CSV file are separated by comma(,) and encloused by (" ")</li>
            <li>2. First row of CSV file should contain feld_name/meta_key, and username should be appear in first row to pass validation</li>
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