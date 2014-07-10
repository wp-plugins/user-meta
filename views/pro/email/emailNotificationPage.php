<?php
global $userMeta; 
// Expected: $data, $roles
?>

<div class="wrap">
    <?php screen_icon( 'options-general' ); ?>
    <h2><?php _e( 'E-mail Notification', $userMeta->name ); ?></h2>   
    <?php do_action( 'um_admin_notice' ); ?>
    <div id="dashboard-widgets-wrap">
        <div class="metabox-holder">
            <div id="um_admin_content">
                <form method="post" onsubmit="pfAjaxRequest(this); return false;" />
                <?php
                
                /**
                 * User Registration Email
                 */
                $html  = null;    
                
                $html .= '<p>'. __( 'This e-mail will be sent to new user after registration.', $userMeta->name ) .'</p>';
                $html .= '<h3>'. __( 'User Notification', $userMeta->name ) . '</h3>';              
                $html .= $userMeta->buildRolesEmailTabs( array( 'registration', 'user_email' ), $data );     
                $html .= '<p><i>' . sprintf( __( 'Use placeholder %s if needed.', $userMeta->name ), '%email_verification_url%' ) . '</i></p>';          
                                  
                $html .= '<div class="clear"></div>'; 
                $html .= '<div class="pf_divider"></div>';            

                $html .= '<p>'. __( 'This e-mail will be sent to admin after new user registration.', $userMeta->name ) .'</p>';                                                  
                $html .= '<h3>'. __( 'Admin Notification', $userMeta->name ) . '</h3>';    
                $html .= $userMeta->buildRolesEmailTabs( array( 'registration', 'admin_email' ), $data );
                $html .= '<p><i>' . sprintf( __( 'Use placeholder %s if needed.', $userMeta->name ), '%activation_url%' ) . '</i></p>'; 
                
                echo $userMeta->metaBox( __( 'User Registration E-mail', $userMeta->name ), $html, false, false );

                
                 /**
                 * User Email Validation Form
                 */
                $html   = null; 
                   
                $html .= '<p>'. __( 'This email will be sent to user after email is verified.', $userMeta->name ) .'</p>';
                $html .= '<h3>'. __( 'User Notification', $userMeta->name ) . '</h3>';           
                $html .= $userMeta->buildRolesEmailTabs( array( 'email_verification', 'user_email' ), $data );               

                $html .= '<div class="clear"></div>'; 
                $html .= '<div class="pf_divider"></div>';  
                          
                $html .= '<p>'. __( 'This email will be sent to admin after user email is verified.', $userMeta->name ) .'</p>';                                                  
                $html .= '<h3>'. __( 'Admin Notification', $userMeta->name ) . '</h3>';
                $html .= $userMeta->buildRolesEmailTabs( array( 'email_verification', 'admin_email' ), $data );
                $html .= '<p><i>' . sprintf( __( 'Use placeholder %s if needed.', $userMeta->name ), '%activation_url%' ) . '</i></p>';                          

                echo $userMeta->metaBox( __( 'After email is verified', $userMeta->name ), $html, false, false );                 
                
                
                /**
                 * User Activation Form
                 */
                $html   = null;    
                $html  .= '<p>'. __( 'This e-mail will be sent to user upon activation.', $userMeta->name ) .'</p>';

                $html .= '<h3>'. __( 'User Notification', $userMeta->name ) . '</h3>';               
                $html .= $userMeta->buildRolesEmailTabs( array( 'activation', 'user_email' ), $data );               
                                     
                echo $userMeta->metaBox( __( 'User Activation E-mail', $userMeta->name ), $html, false, false );
                
                
                /**
                 * User Deactivation Form
                 */
                $html   = null;    
                $html  .= '<p>'. __( 'This e-mail will be sent to user upon deactivation.', $userMeta->name ) .'</p>';

                $html .= '<h3>'. __( 'User Notification', $userMeta->name ) . '</h3>';               
                $html .= $userMeta->buildRolesEmailTabs( array( 'deactivation', 'user_email' ), $data );               
                                     
                echo $userMeta->metaBox( __( 'User Deactivation E-mail', $userMeta->name ), $html, false, false );  
                                             
                
                /**
                 * LostPassword Email
                 */
                $html   = null;    
                $html  .= '<p>'. __( 'This e-mail will be sent to user when requested to reset password.', $userMeta->name ) .'</p>';

                $html .= '<h3>'. __( 'User Notification', $userMeta->name ) . '</h3>';           
                $html .= $userMeta->buildRolesEmailTabs( array( 'lostpassword', 'user_email' ), $data );    
                $html .= '<p><i>'. sprintf( __( 'Use placeholder %s or it will included automatically.', $userMeta->name ), '%reset_password_link%') . '</i></p>';         
                                     
                echo $userMeta->metaBox( __( 'Lost Password E-mail', $userMeta->name ), $html, false, false );     
                

                /**
                 * Password change email
                 */
                $html  = null;    
                
                //$html .= '<p>'. __( 'This e-mail will be sent to user when they reset their password.', $userMeta->name ) .'</p>';
                //$html .= '<h3>'. __( 'User Notification', $userMeta->name ) . '</h3>';              
                //$html .= $userMeta->buildRolesEmailTabs( array( 'reset_password', 'user_email' ), $data );     
                                  
                //$html .= '<div class="clear"></div>'; 
                //$html .= '<div class="pf_divider"></div>';            

                $html .= '<p>'. __( 'This e-mail will be sent to admin when user reset their password.', $userMeta->name ) .'</p>';                                                  
                $html .= '<h3>'. __( 'Admin Notification', $userMeta->name ) . '</h3>';    
                $html .= $userMeta->buildRolesEmailTabs( array( 'reset_password', 'admin_email' ), $data );
    
                echo $userMeta->metaBox( __( 'Reset Password E-mail', $userMeta->name ), $html, false, false );                

                
                /**
                 * Profile update Email
                 */
                $html  = null;    
                
                $html .= '<p>'. __( 'This e-mail will be sent to user when they update their front-end profile.', $userMeta->name ) .'</p>';
                $html .= '<h3>'. __( 'User Notification', $userMeta->name ) . '</h3>';              
                $html .= $userMeta->buildRolesEmailTabs( array( 'profile_update', 'user_email' ), $data );     
                                  
                $html .= '<div class="clear"></div>'; 
                $html .= '<div class="pf_divider"></div>';            

                $html .= '<p>'. __( 'This e-mail will be sent to admin when user update their front-end profile.', $userMeta->name ) .'</p>';                                                  
                $html .= '<h3>'. __( 'Admin Notification', $userMeta->name ) . '</h3>';    
                $html .= $userMeta->buildRolesEmailTabs( array( 'profile_update', 'admin_email' ), $data );
    
                echo $userMeta->metaBox( __( 'Profile Update E-mail', $userMeta->name ), $html, false, false );
                
                
                echo $userMeta->methodName( 'SaveEmailTemplate' );
                
                /**
                 * Button 
                 */ 
                echo $userMeta->createInput( "save_field", "submit", array(
                    "value" => __( "Save Changes", $userMeta->name ),
                    "id"    => "update_settings",
                    "class" => "button-primary",
                    "enclose"   => "p",
                ) );                  
                
                ?>
                </form>        
            </div>
            
            <div id="um_admin_sidebar">                            
                <?php
                $variable = null;
                $variable .= "<strong>" . __( 'Site Placeholder', $userMeta->name ) . "</strong><p>";                         
                $variable .= "%site_title%, ";
                $variable .= "%site_url%, ";
                $variable .= "%login_url%, ";
                $variable .= "%logout_url%, ";
                $variable .= "%activation_url%, ";
                $variable .= "%email_verification_url%";
                $variable .= "</p>";
                
                $variable .= "<strong>" . __( 'User Placeholder', $userMeta->name ) . "</strong><p>";
                $variable .= "%ID%, ";
                $variable .= "%user_login%, ";
                $variable .= "%user_email%, ";
                $variable .= "%password%, ";
                $variable .= "%display_name%, ";
                $variable .= "%first_name%, ";
                $variable .= "%last_name%";
                $variable .= "</p>";    
                
                $variable .= "<strong>" . __( 'Custom Field', $userMeta->name ) . "</strong><p>";      
                $variable .= "%your_custom_user_meta_key%</p>";                     

                $variable .= "<p><em>(" . __( "Placeholder will be replaced with the relevant value when used in email subject or body.", $userMeta->name ) . ")</em></p>";                
                
                echo $userMeta->metaBox( __( 'Placeholder', $userMeta->name ), $variable );                
                
                echo $userMeta->metaBox( __( '3 steps to get started', $userMeta->name ),  $userMeta->boxHowToUse(), false, false );               
                if( !@$userMeta->isPro )
                    echo $userMeta->metaBox( __( 'User Meta Pro', $userMeta->name ),   $userMeta->boxGetPro() );
                //echo $userMeta->metaBox( __( 'Shortcode', $userMeta->name ),   $userMeta->boxShortcodesDocs() );
                ?>
            </div>
        </div>
    </div>     
</div>
