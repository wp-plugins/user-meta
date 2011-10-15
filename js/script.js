    
jQuery(document).ready(function(){
    jQuery("#add-meta").click(function(){
        var meta_field = jQuery(".new-meta-field").html();
        jQuery("#meta-container").append(meta_field);
        return false;
    });
    
    jQuery("#add-meta-group").click(function(){
        var meta_group = jQuery(".new-meta-group").html();
        jQuery("#group-container").append(meta_group);
        return false;
    });    
    
    jQuery(".remove-meta").click(function(){
        jQuery(this).parent().remove();
        return false;
    });
    
    
    jQuery("#um-frontend-profile-form").validationEngine();  
    

});
    