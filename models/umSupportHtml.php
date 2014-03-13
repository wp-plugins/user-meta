<?php

if( !class_exists( 'umSupportHtml' ) ) :
class umSupportHtml {
    
    function boxHowToUse(){
        global $userMeta;
        
        $html = null;
        $html .= sprintf( __( '<p><strong>Step 1.</strong> Create Field from User Meta %s page.</p>', $userMeta->name ), $userMeta->adminPageUrl('fields_editor') );
        $html .= sprintf( __( '<p><strong>Step 2.</strong> Go to User Meta %s page. Choose a Form Name, drag and drop fields from right to left and save the form.</p>', $userMeta->name ), $userMeta->adminPageUrl('forms_editor') );
        $html .= sprintf( __( '<p><strong>Step 3.</strong> Write shortcode to your page or post. Shortcode (e.g.): %s</p>', $userMeta->name ), '&#91;user-meta type="profile" form="'.__('your_form_name', $userMeta->name).'"&#93;'  );
        $html .= "<div><center><a class=\"button-primary\" href=\"" . $userMeta->website .  "\">". __( 'Visit Plugin Site', $userMeta->name ) ."</a></center></div>";
        return $html;
    }
    
    function boxGetPro(){
        global $userMeta;
        
        $html = null;
        $html .= "<div style='padding-left: 10px'>";
        $html .= "<p>Get <strong>User Meta Pro</strong> for : </p>";
        $html .= "<li>Frontend Registration.</li>";
        $html .= "<li>Frontend Login shortcode and widget.</li>";
        $html .= "<li>Allow user to login with their Email or Username.</li>";
        $html .= "<li>Add extra fields to backend profile.</li>";
        $html .= "<li>Role based user redirection on login, logout and registratioin.</li>";
        $html .= "<li>User activatation/deactivation, Admin approval on new user registration.</li>";
        $html .= "<li>Customize email notification with including extra field's data.</li>";
        $html .= "<li>Frontend password reset.</li>";
        $html .= "<p></p>";
        $html .= "<li>35 types of fields for creating profile/registration form.</li>";        
        $html .= "<li>Fight against spam by Captcha.</li>";
        $html .= "<li>Brake your form into multiple page.</li>";
        $html .= "<li>Group fields using Section Heading.</li>";
        $html .= "<li>Write your own html by Custom HTML.</li>";
        $html .= "<li>Allow user to upload their file by File Upload.</li>";
        $html .= "<li>Country Dropdown for country selection.</li>";        
        $html .= "<br />";
        $html .= "<center><a class='button-primary' href='http://user-meta.com'>Get User Meta Pro</a></center>";
        $html .= "</div>";
        return $html;
    }    
    
    function boxShortcodesDocs(){
        global $userMeta;
        
        $html = null;
        $html .= "<div style='padding-left: 10px'>";
        $html .= '<p><strong>&#91;user-meta type="type_name" form="'.__('your_form_name', $userMeta->name).'"&#93;</strong></p>';
        $html .= '<li>' . sprintf( __( '%s for showing profile form.', $userMeta->name ), '<strong>type="profile"</strong>') . '</li>';     
        $html .= '<li>' . sprintf( __( '%s for showing registration form.', $userMeta->name ), '<strong>type="registration"</strong>') . '</li>';
        $html .= '<li>' . sprintf( __( '%s for showing profile form if user logged in, or showing registration form, if user not logged in.', $userMeta->name ), '<strong>type="profile-registration"</strong>') . '</li>';
        $html .= '<li>' . sprintf( __( '%s for showing public profile if user_id parameter provided as GET request.', $userMeta->name ), '<strong>type="public"</strong>') . '</li>';
        $html .= '<li>' . sprintf( __( '%s for showing login page.', $userMeta->name ), '<strong>type="login"</strong>') . '</li>';   
        $html .= "<p></p>";
        if( !$userMeta->isPro() )
            $html .= __( '<p>N.B. "registration", "profile-registration" and "login" is only supported in pro version.</p>', $userMeta->name );
        $html .= "<center><a class='button-primary' href='http://user-meta.com/documentation/'>". __( 'Read More', $userMeta->name ) ."</a></center>";
        $html .= "</div>";
        return $html;        
    }
    
    function boxTips(){
        global $userMeta;
        
        $html = "<div style='padding-left: 10px'>";
        $html .= '<li>' . sprintf( __( 'Admin users can view frontend profiles from User Administration screen. To enable this feature, select a profile page from Profile Page Selection in <a href="%s">General settings</a> tab and enable the checkbox to the right.', $userMeta->name ), $userMeta->adminPageUrl('settings', false).'#um_settings_general') . '</li>'; 
        $html .= '<li>' . __( 'In case of extra field, you need to define unique meta_key. That meta_key will be use to save extra data in usermeta table. Without defining meta_key, extra data won\'t save.', $userMeta->name ) . '</li>'; 
        $html .= "<center><a class='button-primary' href='http://user-meta.com/documentation/'>". __( 'Read More', $userMeta->name ) ."</a></center>";
        $html .= "</div>";
        return $html; 
    }
    
    function getProLink( $label=null ){
        global $userMeta;
        
        $label = $label ? $label : $userMeta->website;
        return "<a href=\"{$userMeta->website}\">$label</a>";
    }    
}
endif;
