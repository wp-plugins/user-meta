
<?php global $userMeta; ?>

<div class="wrap">
    <div id="icon-edit-pages" class="icon32 icon32-posts-page"><br /></div>  
    <h2>Fields Editor</h2>  
    <?php $userMeta->showNotice(); ?>
    <p>Click field from right side panel for creating new field</p> 
    <div id="dashboard-widgets-wrap">
        <div class="metabox-holder">
            <div id="um_admin_content">
                <form id="um_fields_form" action="" method="post" onsubmit="umUpdateField(this); return false;" >
                    <?php echo $userMeta->createInput( 'save_field', 'submit', array('value'=>'Save Changes', 'class'=>'button-primary pf_save_button' ) ); ?>                 
                    <br /><br />
                    <div id="um_fields_container">                 
                        <?php
                        if( $fields ){
                            $n = 0;
                            foreach( $fields as $fieldID => $fieldData ){
                                $n++;
                                $fieldData['id'] = $fieldID;
                                $fieldData['n']  = $n;
                                $userMeta->render( 'field', $fieldData );
                            }
                        }   
                        ?>                                     
                    </div>
                    <?php echo $userMeta->createInput( 'save_field', 'submit', array('value'=>'Save Changes', 'class'=>'button-primary pf_save_button' ) ); ?>                 
                </form>
                <?php $maxKey      = $userMeta->maxKey( $fields ); ?>
                <?php $last_id     = $maxKey ? $maxKey : 0 ?>
                <input type="hidden" id="last_id" value="<?php echo $last_id; ?>"/>
            </div>
            
            
            
            <?php
            $wpFields = null;
            foreach( $userMeta->getFields( 'box_group', 'wp_default', 'title' ) as $fieldKey => $fieldValue )
                $wpFields .= "<div field_type='$fieldKey' class='button um_field_selecor' onclick='umNewField(this)'>$fieldValue</div>";            

            $standardFields = null;
            foreach( $userMeta->getFields( 'box_group', 'standard', 'title' ) as $fieldKey => $fieldValue )
                $standardFields .= "<div field_type='$fieldKey' class='button um_field_selecor' onclick='umNewField(this)'>$fieldValue</div>";            
            
            /*
            $formatingFields = null;
            foreach( $userMeta->getFields( 'box_group', 'formating', 'title' ) as $fieldKey => $fieldValue )
                $formatingFields .= "<div field_type='$fieldKey' class='button um_field_selecor' onclick='umNewField(this)'>$fieldValue</div>";
            */
            
            ?>
            
            <div id="um_admin_sidebar">                            
                <?php echo $userMeta->metaBox( 'WordPress Default Fields',  $wpFields ); ?>
                <?php echo $userMeta->metaBox( 'Extra Fields',           $standardFields ); ?>
                <?php //echo $userMeta->metaBox( 'Formating Fields',          $formatingFields ); ?>
                <?php echo $userMeta->metaBox( 'How to use',  $userMeta->howToUse()); ?>
            </div>
        </div>
    </div>     
</div>

<script>
jQuery(function() {
    /*jQuery( ".um_field_selecor" ).draggable({
        cursor: 'pointer',
        connectWith: '.um_test',
        helper: 'clone',
        //opacity: 0.5,
        zIndex: 10        
    });*/
    
    
    jQuery('#um_fields_container').sortable({
        connectWith: '#um_fields_container',
        cursor: 'pointer'
    }).droppable({
        accept: '.um_field_selecor',
        activeClass: 'um_highlight',
        drop: function(event, ui) {
            var $li = jQuery('<div>').html('List ' + ui.draggable.html());
            $li.appendTo(this);
        }
    });  
    
    
    /*jQuery( "#um_fields_container" ).droppable({
        accept: '.um_field_selecor',
        activeClass: 'um_highlight',
        drop: function(event, ui) {
            var $li = jQuery('<div>').html('List ' + ui.draggable.html());
            $li.appendTo(this);
        }
    });*/
       
	//jQuery( "#um_fields_container" ).sortable();
    
    jQuery( "#um_admin_sidebar" ).sortable();
    //jQuery( "#um_fields_form").validationEngine();
});
</script>   
