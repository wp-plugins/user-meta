<?php

if ( ! class_exists( 'umFieldsController' ) ) :
class umFieldsController {
    
    function __construct() {      
        //add_action( 'wp_ajax_um_add_field',     array($this, 'ajaxAddField' ) ); 
        //add_action( 'wp_ajax_um_change_field',  array($this, 'ajaxChangeField' ) ); 
        //add_action( 'wp_ajax_um_update_field',  array($this, 'ajaxUpdateField' ) );                
    }
    

    function ajaxAddField() {
        global $userMeta;
        $userMeta->verifyAdminNonce( 'add_field' ); 
        
        if ( empty( $_POST['id'] ) || empty( $_POST['editor'] ) ) die();
        
        if ( ! empty( $_POST['existing_field'] ) && ( $_POST['editor'] == 'form_editor' ) ) {
            
            $fields = $userMeta->getData( 'fields' );
            
            if ( isset( $fields[ $_POST['id'] ] ) ) {
                $field  = $fields[ $_POST['id'] ];
                $field['id']    = $_POST['id'];
                $fieldBuilder = new umFieldBuilder( $field );
                $fieldBuilder->setEditor( $_POST['editor'] );
                echo $fieldBuilder->buildPanel();
                
            } else {
                echo "<div class=\"alert alert-warning\" role=\"alert\">Field id {$_POST['id']} is not exists!</div>";
            }
            
        } elseif (  ! empty( $_POST['field_type'] ) ) {
            $fieldBuilder = new umFieldBuilder( $_POST );
            $fieldBuilder->setEditor( $_POST['editor'] );
            echo $fieldBuilder->buildPanel();
        }

        die();
    }
    
    
    function ajaxChangeField() {
        global $userMeta;
        $userMeta->verifyAdminNonce( 'fields_editor' ); 
                  
        if ( isset( $_POST['field_type'] ) && isset( $_POST['id'] ) && $_POST['editor'] ) {
            $field = $_POST;
            $fieldBuilder = new umFieldBuilder( $field );
            $fieldBuilder->setEditor( $_POST['editor'] );
            echo $fieldBuilder->buildPanel();
        }
        
        die();    
    }
    
    
    function ajaxUpdateField() {
        global $userMeta;
        $userMeta->verifyAdminNonce( 'updateFields' );         

        $data = array();
        if ( isset( $_POST['fields'] ) )
            $data = $userMeta->arrayRemoveEmptyValue( $_POST['fields'] );
        
        $fields = array();
        foreach ( $data as $field ) {
            if ( empty( $field['id'] ) ) continue;
            
            $id = $field['id'];
            unset( $field['id'] );
            $fields[ $id ] = $field;
        }
 
        $data = apply_filters( 'user_meta_pre_configuration_update', $fields, 'fields_editor' );
        $userMeta->updateData( 'fields', $data );
        
        echo 1;
        die();
    }
           
}
endif;
