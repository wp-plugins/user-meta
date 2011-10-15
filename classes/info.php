<?php

if(!class_exists('userMetaInfo')){
    class userMetaInfo {
        
        function __construct() {
            add_action('admin_menu', array(__CLASS__, 'admin_menu')); 
        }

        function admin_menu(){
            add_menu_page( 'User Meta', 'User Meta', 'manage_options', 'usermeta', array(__CLASS__, info));
        }  
               
        function info(){
            global $userMeta;
            
            add_meta_box("user_meta", "User Meta Info", array(__CLASS__, 'infoTab'), "info_tab", 'side', 'high');
            add_meta_box("user_meta", "Donations", array($userMeta, 'donation'), "donation_tab", 'side', 'high');
            ?>
            
            <div class="wrap">	
                <?php if($showMessage) echo "<br /><div class='$showMessage[0]'>$showMessage[1]</div>" ?>	
            	<div id="icon-users" class="icon32"><br /></div>
            	<h2>User Meta</h2>    
                
                <div id="dashboard-widgets-wrap">
                    <div class="metabox-holder">
                        <div style="float:left; width:70%;" class="inner-sidebar1">
                            <?php do_meta_boxes('info_tab','side',''); ?>
                        </div>
                                            
                        <div style="float:right; width:28%;" class="inner-sidebar1">
                            <?php do_meta_boxes('donation_tab','side',''); ?>
                        </div>
                    </div>
                </div>                                   
            </div> 
            <?php
        }        
        
        function infoTab(){
            ?>
            <p>User Meta Plugin Version 1.0.3</p>
            <?php
        }
        
    }
}

$userMetaInfo = new userMetaInfo;


?>