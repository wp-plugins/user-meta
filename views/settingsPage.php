
<?php global $userMeta; ?>

<div class="wrap">
    <div id="icon-edit-pages" class="icon32 icon32-posts-page"><br /></div>  
    <h2>User Meta Settings</h2>   
    <?php $userMeta->showNotice(); ?>
    <div id="dashboard-widgets-wrap">
        <div class="metabox-holder">
            <div id="um_admin_content">
                <?php $userMeta->render("settings", $settings); ?>
            </div>

            
            <div id="um_admin_sidebar">                            
                <?php echo $userMeta->metaBox( 'How to use',  $userMeta->howToUse()); ?>
            </div>
        </div>
    </div>     
</div>
