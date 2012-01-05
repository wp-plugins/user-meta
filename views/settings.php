<?php
global $userMeta;

$html = null;
$html .= '<form id="um_settings_form" action="" method="post" onsubmit="umUpdateSettings(this); return false;" >';
    /*$html .= "<h4>reCAPTCHA Settings</h4>";
    $html .= $userMeta->createInput( "recaptcha_public_key", "text", array(
        "value" => isset($recaptcha_public_key)? $recaptcha_public_key : null,
        "label" => "reCaptcha Public Key",
    ) );    
    $html .= $userMeta->createInput( "recaptcha_private_key", "text", array(
        "value" => isset($recaptcha_private_key)? $recaptcha_private_key : null,
        "label" => "reCaptcha Private Key",
    ) ); */
    
    $html .= "<h4>User Meta Version: " . $userMeta->version . "</h4>";
    $html .= "<div class='pf_divider'></div>";  
    
      
     
    $html .= "<h4>Profile Page Selection</h4>";      
    $html .= wp_dropdown_pages(array(
        'name'      => 'profile_page',
        'selected'  => isset($profile_page)? $profile_page : null,
        'echo'      => 0,
        'show_option_none'=>'Select a Profile Page ',       
    ));
    $html .= $userMeta->createInput( "profile_in_admin", "checkbox", array(
        "value" => isset($profile_in_admin)? $profile_in_admin : null,
        "after" => " Show profile link to User Administration Page",
    ) );             
    $html .= "<p>Profile page should contain shortcode like: [user-meta type='profile' form='profile']</p>";
    $html .= "<div class='pf_divider'></div>";
    
    
    $html .= $userMeta->createInput( "save_field", "submit", array(
        "value" => "Save Changes",
        "id"    => "update_settings",
        "class" => "button-primary",
    ) );

       
    
    $html .= "<div class='um_ajax_result'></div>";
$html .= "</form>";

echo $userMeta->metaBox( "Settings", $html );

?>