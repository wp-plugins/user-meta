<?php
global $userMeta;
// Expected: $login
// field slug: login

$roles = $userMeta->getRoleList();

$html = null;

/**
 * Login By
 */
$html .= "<h4>" . __( 'User login by', $userMeta->name ) . "</h4>";   
$html .= $userMeta->createInput( "login[login_by]", "radio", array(
    'value'     => @$login[ 'login_by' ],
    'id'        => 'um_login_login_by',
    'by_key'    => true,
), $userMeta->loginByArray() );     
$html .= "<div class='pf_divider'></div>";



/**
 * Login Page
 */
$html .= "<h4>". __( 'Login Page', $userMeta->name ) . "</h4>";              
$html .= wp_dropdown_pages(array(
    'name'      => 'login[login_page]',
    'id'        => 'um_login_login_page',
    'selected'  => @$login[ 'login_page' ],
    'echo'      => 0,
    'show_option_none'=>'None ',      
));

$createPageUrl = admin_url( 'admin-ajax.php' );
$createPageUrl = add_query_arg( array(
    'page'          => 'login',
    'method_name'   => 'generatePage',
    'action'        => 'pf_ajax_request',
), $createPageUrl );
$createPageUrl = wp_nonce_url( $createPageUrl, 'generate_page' );
$html .= "<a href='$createPageUrl' id='um_login_login_page_create' class='button-secondary'>Create Page</a>";

$html .= "<p>" . sprintf( __( 'Login page should contain shortcode like: %s', $userMeta->name ), "[user-meta-login]") . "</p>";

$html2 = $userMeta->createInput( "login[disable_wp_login_php]", "checkbox", array(
    "label" => sprintf( __( 'Disable default login url (%s)', $userMeta->name ), site_url( 'wp-login.php' ) ),
    "value" => @$login[ 'disable_wp_login_php' ],
    "id"    => "um_login_disable_wp_login_php",
    "onchange"  => "umSettingsToggleError()",
    "enclose"=> "p",
) ); 

$loginUrl = !empty( $login[ 'login_page' ] ) ? get_permalink( $login[ 'login_page' ] ) : null;
$html2 .= '<p><em>' . sprintf( __( 'Disable wp-login.php and redirect to front-end login page %s', $userMeta->name ), $loginUrl )  .'</em></p>';

$html .= "<div id=\"um_login_disable_wp_login_php_block\">$html2</div>";

$html .= "<div class='pf_divider'></div>";



/**
 * Login Form
 */
$html .= "<h4>". __( 'Login Form', $userMeta->name ) . "</h4>";  

/*
$html .= $userMeta->createInput( 'login[login_form]', 'textarea', array(
    'value'     => @$login[ 'login_form' ],
    'rows'      => '10',
    'cols'      => '50',
    'enclose'   => 'p'
) );
$html .= '<p><em>' . __( 'Use placeholder %login_form% for showing login form and %lostpassword_form% for showing lost password form. <br /> Note: %login_form% should be included. If not included, plugin will automatically include it.', $userMeta->name ) . '</em></p>';   
*/

$html .= $userMeta->createInput( "login[disable_lostpassword]", "checkbox", array( 
    "value"     => @$login[ 'disable_lostpassword' ],
    "id"        => "um_login_disable_lostpassword",
    "label"     => __( 'Disable lost password feature', $userMeta->name ),
    "enclose"   => "p",
) ); 

/*
$html .= $userMeta->createInput( "login[default_lostpassword]", "checkbox", array( 
    "value"     => @$login[ 'default_lostpassword' ],
    "id"        => "um_login_default_lostpassword",
    "label"     => __( 'Use default lostpassword url', $userMeta->name ),
    "enclose"   => "p",
) );
*/

$html .= $userMeta->createInput( "login[disable_registration_link]", "checkbox", array( 
    "value"     => @$login[ 'disable_registration_link' ],
    "id"        => "um_login_disable_registration_link",
    "label"     => __( 'Hide registration link', $userMeta->name ),
    "enclose"   => "p",
) );

$html .= $userMeta->createInput( "login[disable_ajax]", "checkbox", array( 
    "value"     => @$login[ 'disable_ajax' ],
    "id"        => "um_login_disable_ajax",
    "label"     => __( 'Disable AJAX submit', $userMeta->name ),
    "enclose"   => "p",
) ); 
$html .= "<div class='pf_divider'></div>";

/**
 * Reset Password
 */
$html .= "<h4>". __( 'Reset Password Page', $userMeta->name ) . "</h4>";  
$html .= wp_dropdown_pages(array(
    'name'      => 'login[resetpass_page]',
    'id'        => 'um_login_resetpass_page',
    'selected'  => @$login[ 'resetpass_page' ],
    'echo'      => 0,
    'show_option_none'=>'None ',      
));

$createPageUrl = admin_url( 'admin-ajax.php' );
$createPageUrl = add_query_arg( array(
    'page'          => 'resetpass',
    'method_name'   => 'generatePage',
    'action'        => 'pf_ajax_request',
), $createPageUrl );
$createPageUrl = wp_nonce_url( $createPageUrl, 'generate_page' );
$html .= "<a href='$createPageUrl' id='um_login_resetpass_page_create' class='button-secondary'>Create Page</a>";

$html .= " <span class='um_required_resetpass_page_page' style='color:red'><em><strong>(" . __( 'Please select any page for resetting password as your default login url is disabled!', $userMeta->name ) . ")</strong></em></span>";
$html .= '<p><em>' . __( 'This is the page a user will be redirected to when they want to retrieve/reset their password.', $userMeta->name ) .'</em></p>';

$html .= "<div class='pf_divider'></div>";

/**
 * LoggedIn Profile
 */
$html .= "<h4>" . __('Logged in user profile settings', $userMeta->name ) . "</h4>"; 
$html .= "<div id=\"loggedin_profile_tabs\">";
    $html .= "<ul>";
    foreach( $roles as $key => $val )
        $html .= "<li><a href=\"#profile-tabs-$key\">$val</a></li>";
    $html .= "</ul>";
    
    $placeholder = "<p><strong>Place Holder</strong></p>
    <p><em>%site_title%, %site_url%, %logout_url%, %admin_url%, %ID%, %user_login%, %user_email%, %user_url%, %first_name%, %last_name%, %display_name%, %nickname%, %avatar%, %your_meta_key%</em></p> " ;
    
    foreach( $roles as $key => $val ){
        $html .= $userMeta->createInput( "login[loggedin_profile][{$key}]", "textarea", array( 
            "value"         => @$login[ 'loggedin_profile' ][ $key ] ? @$login[ 'loggedin_profile' ][ $key ] : @$login[ 'loggedin_profile' ][ 'subscriber' ],
            "label"         => "Logged in user profile",
            "label_class"   => "pf_label",
            "rows"          => "10",
            "cols"          => "50",
            "after"         => $placeholder,
            "enclose"       => "div id=\"profile-tabs-$key\"",
        ) );    
    }
$html .= "</div>";

if( is_multisite() ){
	$html .= "<div class='pf_divider'></div>";
	$html .= "<h4>" . __( 'Multisite', $userMeta->name ) . "</h4>";
	
	$html .= $userMeta->createInput( "login[blog_member_only]", "checkbox", array( 
	    'value'     => @$login[ 'blog_member_only' ],
        'id'        => 'um_login_blog_member_only',
		'label'     => __( 'Prevent user login if user is not member of current site.', $userMeta->name ),
        'enclode'   => 'p',
	 ) );		
}
