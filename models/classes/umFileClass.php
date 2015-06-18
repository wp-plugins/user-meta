<?php

if ( ! class_exists( "umFile" ) ) :
class umFile {
    
    private $file;
    
    private $url;
    
    private $fileName;
    
    private $width;
    
    private $height;
    
    private $field = array();
    
    public function __construct( $field = array() ) {
        if ( ! empty( $field ) )
            $this->initFile( $field );
    }
    
    /**
     * Initiate all private properties.
     * 
     * @param type $field
     */
    private function initFile( $field ) {
        global $userMeta;
        
        $this->field = $field;
        
        $this->sanitizeField();
        
        if ( empty( $this->field['field_value'] ) ) return;
        
        $uploads    = $userMeta->determinFileDir( $this->field['field_value'] );
        if ( empty( $uploads ) ) return;

        $this->file = $uploads['path'];
        $this->url  = $uploads['url'];

        $fileData   = pathinfo( $this->file );
        $this->fileName   = $fileData['basename']; 
        
        $this->width    = isset( $this->field['image_width'] ) ? $this->field['image_width'] : null;
        $this->height   = isset( $this->field['image_height'] ) ? $this->field['image_height'] : null;
        
        if ( ! empty( $this->field['resize_image'] ) ) {
            $crop = ! empty( $this->field['crop_image'] ) ? true : false;
            $this->resize( $crop );
        }
    }
    
      
    private function sanitizeField() {
        if ( isset( $this->field['field_type'] ) &&  $this->field['field_type'] == 'user_avatar' ) {
            if ( ! empty( $this->field['image_size'] ) ) {
                $this->field['image_width']   = $this->field['image_size'];
                $this->field['image_height']  = $this->field['image_size'];
            } else {
                $this->field['image_width']   = 96;
                $this->field['image_height']  = 96;
            }
            
            $class = "avatar avatar-{$this->field['image_width']} photo";
            if ( ! empty( $this->field['field_class'] ) )
                $class .= ' ' . $this->field['field_class'];
            
            $this->field['field_class'] = $class;
        }  
    }
    
    
    public function showFile() {
        global $userMeta;
        
        $html   = '';
        $class  = isset( $this->field['field_class'] ) ? $this->field['field_class'] : '';
        
        if ( $userMeta->isImage( $this->url ) ) {
            $arg = array(
                'src'       => esc_url( $this->url ),
                'class'     => $class,
                'alt'       => esc_attr( $this->fileName )
            );
            
            if ( ! empty( $this->width ) )
                $arg['width'] = $this->width;
            
            if ( ! empty( $this->height ) )
                $arg['height'] = $this->height; 
            
            $html .= $userMeta->createInput( null, 'img', $arg );

        } else {
            $html .= $userMeta->createInput( null, 'a', array(
                'href'      => esc_url( $this->url ),
                'class'     => $class,
                'alt'       => esc_attr( $this->fileName ),
                'value'     => $this->fileName
            ) );
        }

        /**
         * Remove Link
         */
        if ( empty( $this->field['read_only'] ) && ! empty( $this->url ) )
            $html .= "<p><a href='#' onclick='umRemoveFile(this)' name='{$this->field['field_name']}'>". __('Remove', $userMeta->name) ."</a><p>";

        /**
         * Hidden field        
         */
        if ( ! empty( $this->field['field_name'] ) && empty( $this->field['read_only'] ) && isset( $this->field['field_value'] ) ) {
            $html .= $userMeta->createInput( $this->field['field_name'], 'hidden', array(
                'value' => $this->field['field_value'],
            ) );
        }
        
        return $html;
    }
    
    
    public function ajaxUpload() {
        global $userMeta;
        
        if ( isset( $_REQUEST['form_key']  ) )
            $formName = esc_attr( $_REQUEST['form_key'] );
        
        if ( ! empty( $formName ) ) {
            $form = new umFormGenerate( $formName, null, null );
            $validFields = $form->validInputFields();

            if ( empty( $validFields[ $_REQUEST['field_name'] ] ) ) return;

            $field = $validFields[ $_REQUEST['field_name'] ];

        } else {
            if ( ! empty( $_REQUEST['field_id'] ) ) {
                $id    = trim( str_replace( 'um_field_', '', esc_attr( $_REQUEST['field_id'] ) ) );
                $umField = new umField( $id );
                $field = $umField->getData();
            }
        }

        
        if ( ! empty( $field ) ) {
            if ( ! empty( $_REQUEST['filepath'] ) )
                $field['field_value'] = esc_attr( $_REQUEST['filepath'] ); 
            
            $this->initFile( $field );
            echo $this->showFile();
        }
    }
    
    
    private function resize( $isCrop ) {
        if ( empty( $this->width ) || empty( $this->height ) ) return;
        
        if ( version_compare( get_bloginfo('version'), '3.5', '>=' ) ) {
            $image = wp_get_image_editor( $this->file );
            if ( ! is_wp_error( $image ) ) {
                $image->resize( $this->width, $this->height, $isCrop );
                $image->save( $this->file );
            }                
        } else {
            $image = image_resize( $this->file, $this->width, $this->height, $isCrop );
            if ( ! is_wp_error( $image ) )
                $this->file = $image;               
        } 
    }

}
endif;