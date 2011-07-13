    
jQuery(document).ready(function(){
    jQuery("#add-meta").click(function(){
        var meta_field = jQuery(".new-meta-field").html();
        jQuery("#meta-container").append(meta_field);
        return false;
    });
    
    jQuery(".remove-meta").click(function(){
        jQuery(this).parent().remove();
        return false;
    });
    
});
    