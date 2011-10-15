<?php

// always find line endings
ini_set('auto_detect_line_endings', true);

if(!class_exists('userMetaImport')){
    class userMetaImport {
        
        function __construct(){
            add_action('admin_menu', array(__CLASS__, 'admin_menu'));        
        }
        
        function admin_menu(){
       	    add_submenu_page( 'usermeta', 'Import User', 'Import User', 'manage_options', 'user-meta-import', array( __CLASS__, 'userImport'));             
        }
        
        function userImport(){           
                 
          	if (!current_user_can('manage_options')) {
            	wp_die( __('You do not have sufficient permissions to access this page.') );
          	}
        
            if(isset($_POST)){
                global $showMessage, $uploaded_file, $step;
                
                if(isset($_POST['stepOne'])){
                    $uploaded_file = userMeta::fileUpload('csv_file', array('csv' => 'text / csv'));
                    if(!$uploaded_file):
                        $showMessage = array('error', 'It seems the file was not uploaded correctly.');
                    elseif(isset($uploaded_file['error'])):
                        $showMessage = array('error', $uploaded_file['error']);  
                    else:
                        $step = 'stepTwo';
                    endif;    
                    self::inputForm();     
                                       
                                       
                }elseif(isset($_POST['stepTwo'])){
                    
                    $csv_header = $_POST['csv_header'];
                    $selected_field = $_POST['selected_field'];
                    $custom_field = $_POST['custom_field'];
                    $file_path = $_POST['file_path'];    
                    $uploaded_file['file'] = $file_path;


                    if( $_POST['uniqueField'] == 'email'){
                        if(!in_array('user_email', $selected_field)){
                            $showMessage = array("error", "Email field should be selected as any field.");
                            $step = 'stepTwo'; self::inputForm(); return;                                               
                        }                            
                    }elseif( $_POST['uniqueField'] == 'username'){
                        if(!in_array('user_login', $selected_field)){
                            $showMessage = array("error", "Username field should be selected as any field.");
                            $step = 'stepTwo'; self::inputForm(); return;
                        }                                                   
                    }elseif( $_POST['uniqueField'] == 'both'){
                        if( !in_array('user_email', $selected_field) OR !in_array('user_login', $selected_field) ){
                            $showMessage = array("error", "Email and Username field should be selected as any field.");
                            $step = 'stepTwo'; self::inputForm(); return;
                        }                          
                    }
                                                                          
                    $file = fopen($file_path,"r");
                    $first_row =fgetcsv($file);
                    set_time_limit(36000);         
                        
                    $n = 0;   $user_added = 0;    $user_updated = 0;  $user_skipped = 0;                    
                    while(!feof($file)){
                        $rows = fgetcsv($file);
                        if(!$rows) continue;
                        $n++;
                        
                        //assign row data to as its header variable
                        foreach($first_row as $key => $val){
                            $key_name = trim($val);
                            $key_value = trim($rows[$key]);
                            //$key_value = esc_sql($key_value);
                            $$key_name = $key_value;                            
                        }
                    
                        if($_POST['user_role'])
                            $userdata['role'] = $_POST['user_role'];
                        
                        //Populate userdata and metadata array
                        foreach($selected_field as $key => $val){
                            if(!($val == 'none' OR $val == 'custom_field')){
                                $userdata[$val] = $$csv_header[$key];
                            }elseif($val == 'custom_field'){
                                if($custom_field[$key])
                                    $metadata[$custom_field[$key]] = $$csv_header[$key];
                                else    
                                    $metadata[$csv_header[$key]] = $$csv_header[$key];
                            }
                        }
                        
                                  
                        $userdata['user_email'] = sanitize_email($userdata['user_email']);
                        $userdata['user_login'] = sanitize_user($userdata['user_login'], true);
                        
                        if( $_POST['uniqueField'] == 'email'){
                            if(!$userdata['user_email']) $trigger = 'skip_user';
                            $user_id = email_exists($userdata['user_email']);
                            if(!$user_id){
                                $loginFound = username_exists($userdata['user_login']); 
                                if($loginFound)
                                    $userdata['user_login'] = sanitize_user($userdata['user_email']);
                            }                            
                        }elseif( $_POST['uniqueField'] == 'username'){
                            if(!$userdata['user_login']) $trigger = 'skip_user';
                            $user_id = username_exists($userdata['user_login']);
                            if(!$user_id){
                                $emailFound = email_exists($userdata['user_email']); 
                                if($emailFound)
                                    $userdata['user_email'] = sanitize_email( $userdata['user_login'] . '@donotreplay.com' );
                            }                                                        
                        }elseif( $_POST['uniqueField'] == 'both'){
                            if(!$userdata['user_email']) $trigger = 'skip_user';
                            if(!$userdata['user_login']) $trigger = 'skip_user';
                            $user_id = email_exists($userdata['user_email']);
                            if(!$user_id)
                                $user_id = username_exists($userdata['user_login']);                            
                        }                     
                            
                 
                        //assign value to trigger, for makaing decession for next action
                        if( ($_POST['overwrite'] AND $user_id) )
                            $trigger = 'update_user';
                        elseif(($userdata['user_email'] OR $userdata['user_login']) AND !$user_id)
                            $trigger = 'add_user';
                        else
                            $trigger = 'skip_user';
                                    

                        //Implementation user add/update action
            		    if( $trigger == 'add_user' ){
                            unset($userdata[ 'ID' ]);
                            unset($user_id);                
                            $user_id = wp_insert_user( $userdata );
                            if($user_id){
                                echo "<span style='color:green'>$n. {$userdata['user_login']} <b>(Created)</b></span><br />";
                                $user_added++;
                            }                    
            		    }                            
                        elseif( $trigger == 'update_user' ){
                            $userdata[ 'ID' ] = $user_id;
                            if(wp_update_user( $userdata )){
                                echo "<span style='color:blue'>$n. {$userdata['user_login']} <b>(Updated)</b></span><br />";
                                $user_updated++;
                            }                
                        }else{
                            echo "<span style='color:red'>$n. {$userdata['user_login']} <b>(Skipped)</b></span><br />";
                            $user_skipped++;
                        }
                            
                        //Add/Update user meta   
                        if($metadata){
                			foreach ($metadata as $key => $value) {
                			    if( $trigger == 'add_user' )
                                    add_user_meta( $user_id, $key, $value );
                                elseif( $trigger == 'update_user' )
                                    update_user_meta( $user_id, $key, $value );
                            }                            
                        } 
         
                        
                        //Unset all value            
                        foreach($first_row as $key_name){
                            $key_name = trim($key_name);
                            unset($$key_name);
                        }  
                        unset($userdata);
                        unset($metadata);                        

                    }  
                    
                    fclose($file);
                    $showMessage = array("updated", "CSV file have been imported successfully. User added: $user_added, User updated: $user_updated, User skipped: $user_skipped");
                    $step = 'stepTwo'; self::inputForm(); return;

                }else{
                    self::inputForm();
                }
            }
        }
        
        function importTab(){
            global $wp_roles, $step, $uploaded_file;
            $roles = $wp_roles->role_names;
            $file_path = $uploaded_file['file'];
            
            ?>
            	<form action="" method="post" enctype="multipart/form-data">     
                 
                <!-- Srep Two -->                               
                <?php if($step == 'stepTwo'): ?>
                    <input type='hidden' name='stepTwo' value='ok' />
                    <p>Identify Uniquely :
                        <input type="radio" name="uniqueField" value="email" <?php if($_POST['uniqueField'] == 'email' OR !isset($_POST['uniqueField'])){echo "checked='checked'";} ?> /> By Email
                        <input type="radio" name="uniqueField" value="username" <?php if($_POST['uniqueField'] == 'username'){echo "checked='checked'";} ?> /> By Username
                        <input type="radio" name="uniqueField" value="both" <?php if($_POST['uniqueField'] == 'both'){echo "checked='checked'";} ?> /> By Both Email and Username
                    </p>
                    <?php
                    if($file_path){
                        $file = fopen($file_path,"r");
                        echo "<input type='hidden' name='file_path' value='$file_path' />";
                        $csvHeader = fgetcsv($file);      
                        echo "<table><tr><td>CSV Header</td><td>Related Field</td><td>Custom Field Name</td></tr>";                       
                        foreach($csvHeader as $header){
                            echo "<input type='hidden' name='csv_header[]' value='$header' />";
                            $field_list = userMeta::getDefaultUserFields();
                            $field_added = array('none' => '', 'custom_field' => 'Custom Field');
                            $field_list = array_merge($field_added, $field_list);
                            $dropdown = userMeta::createInput('selected_field[]', 'none', 'select', array('have_key' => true), $field_list);                    
                            $customField = userMeta::createInput('custom_field[]');
                            echo "<tr><td>$header</td><td>$dropdown</td><td>$customField</td></tr>";
                        }
                        fclose($file);                
                        echo "</table>";
                        echo "<br /><br /><p>User Role : " . userMeta::createInput('user_role', 'none', 'select', array('have_key' => true), $roles) . "</p>";
                        echo '<p><input type="checkbox" name="overwrite"/> Overwrite existing users</p>';
                        echo "<p><input type='submit' value=' Import ' /></p>";                        
                    }
       
                    ?>   
                    
                <!-- Srep One -->                   
                <?php else: ?>
                    <p>Please select the CSV file you want to import below.</p>
                    <input type='hidden' name='stepOne' value='ok' /> 
                    <p><?php echo userMeta::createInput('csv_file', '', 'file') ?></p> 
                    <p><input type='submit' value=' Next ' /></p>                                                        
                <?php endif; ?>
                
            	</form>             
            <?php
        }
        
        
        function inputForm(){
            global $showMessage, $userMeta;
            add_meta_box("user_meta", "User Import", array(__CLASS__, 'importTab'), "import_tab", 'side', 'high');
            add_meta_box("user_meta", "Donations", array($userMeta, 'donation'), "donation_tab", 'side', 'high');
            
            ?>
            <div class="wrap">	
                <?php if($showMessage) echo "<br /><div class='$showMessage[0]'>$showMessage[1]</div>" ?>	
            	<div id="icon-users" class="icon32"><br /></div>
            	<h2>User Meta Import</h2>    
                
                <div id="dashboard-widgets-wrap">
                    <div class="metabox-holder">
                        <div style="float:left; width:70%;" class="inner-sidebar1">
                            <?php do_meta_boxes('import_tab','side',''); ?>
                        </div>
                                            
                        <div style="float:right; width:28%;" class="inner-sidebar1">
                            <?php do_meta_boxes('donation_tab','side',''); ?>
                        </div>
                    </div>
                </div>                                   
            </div>                			          
            <?php
        }        
    }
}



$user_profile = new userMetaImport;

?>