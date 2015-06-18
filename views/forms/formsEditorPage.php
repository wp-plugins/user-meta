<?php
global $userMeta;

echo '<div class="wrap">';

echo '<h2>Forms <a href="?page=usermeta&action=new" class="add-new-h2">Add New</a></h2>';


if ( ! class_exists( 'WP_List_Table' ) ) {
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once( $userMeta->modelsPath . 'classes/umFormsListTableClass.php' );

$listTable = new umFormsListTable();

$listTable->prepare_items();

$listTable->display();

echo '</div>';  


