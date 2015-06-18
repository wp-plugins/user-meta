<?php

if ( ! class_exists( 'umField' ) ) :
class umField {
    
    private $id;
    
    private $data = array();
        
    private $rules = array();
    
    private $errors = array();
    
    /**
     * accept: user_id, insert_type
     */
    private $options = array();
    
    private $type;
    
    private static $fieldTypes;
    
    public function __construct( $id, $data = array(), $options = array() ) {
        global $userMeta;
        
        $this->id   = $id;
        if ( empty( $id ) && ! empty( $data['id'] ) )
            $this->id   = $data['id'];
        
        $this->data     = $data;
        
        if ( empty( $this->data ) && ! empty( $this->id ) ) {
            $fields = $userMeta->getData( 'fields' );
            if ( isset( $fields[ $this->id ] ) ) {
                $this->data = $fields[ $this->id ];
            }
        }
        
        $this->options  = $options; 
    }
    
    public function getData() {
        $this->sanitizeField();
        return ! empty( $this->data ) ? $this->data : array();
    }
    
    public function getConfig( $key = null ) {
        $this->sanitizeField();
        
        if ( empty( $key ) )
            return $this->data;
        
        if ( isset( $this->data[ $key ] ) )
            return $this->data[ $key ];
        
        return false;
    }
    
    /*
     * Only used inside shortcode.
     */
    public function displayValue( $metaKey = '' ) {
        global $userMeta;
        
        $key = $metaKey;
        if ( empty( $metaKey ) || ! empty( $this->id ) ) {
            $this->sanitizeField();
            $key = ! empty( $this->data['field_name'] ) ? $this->data['field_name'] : $key;
        }

        if ( empty( $key ) ) return;

        $fieldValue = null;
        $user = $userMeta->determineUser();
        
        if ( ! empty( $user ) ) {

            if ( isset( $this->data['default_value'] ) )
                $fieldValue = $userMeta->convertUserContent( $user, $this->data['default_value']  );
            
            $key = trim( $key );
            if ( isset( $user->$key ) )
                $fieldValue = $user->$key; 
            
            if ( is_array( $fieldValue ) )
                $fieldValue = implode( ', ', $fieldValue );
                       
            if ( ! empty( $this->type ) && in_array( $this->type, array( 'user_avatar', 'file' ) ) ) {
                $field      = $this->data;

                if ( ! empty( $field ) ) {
                    $field['field_value']   = $fieldValue;
                    $field['read_only']     = true;
                }
                
                $umFile = new umFile( $field );
                
                $fieldValue = $umFile->showFile();
            }  
        }
        
        return $fieldValue;
    }
    
    /*
     * Only used inside shortcode.
     */
    function generateField() {
        global $userMeta;
        
        if ( empty( $this->data ) ) return;        
        
        $field      = $this->getConfig();
        
        $user       = $userMeta->determineUser();  
        $userID     = ! empty( $user ) ? $user->ID : 0;
        $formKey    = ! empty( $this->options['form_key'] ) ? $this->options['form_key'] : '';    
        
        // Determine Field Value
        $fieldValue = null;
        if ( isset( $field['default_value'] ) ) {
            $fieldValue = $userMeta->convertUserContent( $user, $field['default_value']  );
        }       
        
        if ( ! empty( $field['field_name'] ) ) {
            $fieldName  = $field['field_name'];
            if ( isset( $user->$fieldName ) )
                $fieldValue = $user->$fieldName; 
        }

        $field['field_value'] = $fieldValue; 
        
        $field = apply_filters( 'user_meta_field_config', $field, $this->id, $formKey, $userID );
        
        $fieldDisplay = $userMeta->renderPro( 'generateField', array( 
            'field'         => $field,
            //'form'          => $form,
            'actionType'    => isset( $this->options['insert_type'] ) ? $this->options['insert_type'] : '',
            'userID'        => $userID,
            'inPage'        => '',
            'inSection'     => '',
            'isNext'        => '',
            'isPrevious'    => '',
            'currentPage'   => '',
            'uniqueID'      => '',
        ) );
        
        $html = apply_filters( 'user_meta_field_display', $fieldDisplay, $this->id, $formKey, $field, $userID );
        
        if ( in_array( $field['field_type'], array( 'file', 'user_avatar' ) ) ) {
            $uploaderPath = $userMeta->pluginUrl . '/framework/helper/uploader.php';
            $html .= "<script type=\"text/javascript\">jQuery(document).ready(function(){umFileUploader(\"$uploaderPath\");});</script>";
        }
                
        return $html;  
    }
    
    /**
     * Set :
     * self::$fieldTypes
     * $this->type
     * $this->typeData
     * 
     * $this->data['field_group']
     * $this->data['field_name']
     * $this->data['id']
     */
    private function sanitizeField() {
        global $userMeta;
        
        if ( empty( $this->data ) ) return;
        
        if ( empty( self::$fieldTypes ) )
            self::$fieldTypes = $userMeta->umFields();
          
        $this->type     = ! empty( $this->data['field_type'] ) ? $this->data['field_type'] : '';
        
        if ( isset( self::$fieldTypes[ $this->type ] ) )
            $typeData = self::$fieldTypes[ $this->type ];
        
        if ( isset( $typeData['field_group'] ) )
            $this->data['field_group'] = $typeData['field_group'];

        $fieldName = null;
        if ( isset( $this->data['field_group'] ) &&  $this->data['field_group'] == 'wp_default' ) {
            $fieldName = $this->data['field_type'];
        } else {
            if ( ! empty( $this->data['meta_key'] ) )
                $fieldName = $this->data['meta_key'];
        }  

        $this->data['field_name']   = $fieldName;
        $this->data['id']           = $this->id;
    }
    
    
    public function addRule( $rule ) {
        $this->rules[] = $rule;
    }
    
    public function validate() {
        
        $this->assignRules();
        
        foreach ( $this->rules as $rule ) {
            $value = isset ( $this->data['field_value'] ) ? $this->data['field_value'] : null;
            $validate = new umValidationRule( $rule, $value, array(
                'field_name'    => isset( $this->data['field_name'] )   ? $this->data['field_name'] : null,
                'user_id'       => isset( $this->options['user_id'] )   ? $this->options['user_id'] : 0,
                'insert_type'   => isset( $this->options['insert_type'] ) ? $this->options['insert_type'] : null,
            ) );
            
            /*if ( 'custom' == $rule ) {
                $regex = @$this->data['regex'];
                $regex = ! empty( $regex ) ? "/$regex/" : null;
                $validate->setProperty( $regex, @$this->data['error_text'] );
            }*/
            
            if ( 'regex' == $rule ) {
                $regex = ! empty( $this->data['regex'] ) ? '/' . $this->data['regex'] . '/' : null;
                $errorText = ! empty( $this->data['error_text']  ) ? $this->data['error_text'] : '';
                $validate->setProperty( $regex, $errorText );
            }
                     
            if ( ! $validate->validate() )
                $this->errors[ $rule ] = $validate->getError();
        }
        
        return empty( $this->errors ) ? true : false;
    }
    
    private function assignRules() {
        if ( isset( $this->data['field_type'] ) ) {
            switch ( $this->data['field_type'] ) {
                case 'user_login':
                    $this->rules[] = 'required';
                    $this->rules[] = 'unique';
                    break;
                case 'user_email':
                    $this->rules[] = 'required';
                    $this->rules[] = 'unique';
                    $this->rules[] = 'email';
                    break;
                case 'email':
                    $this->rules[] = 'email';
                    break;
                case 'url':
                case 'user_url':
                    $this->rules[] = 'url';
                    break;
                case 'user_registered':
                    $this->rules[] = 'datetime';
                    break;
                case 'number':
                    $this->rules[] = 'number';
                    break;
                case 'phone':
                    $this->rules[] = 'phone';
                    break;
                /*case 'custom':
                    $this->rules[] = 'custom';
                    break;*/
            }

            if ( ! empty( $this->data['required'] ) ) {
                if( ! in_array( 'required', $this->rules ) )
                    $this->rules[] = 'required';
            }
            
            if ( ! empty( $this->data['unique'] ) ) {
                if( ! in_array( 'unique', $this->rules ) )
                    $this->rules[] = 'unique';
            }
            
            if ( ! empty( $this->data['regex'] ) ) {
                $this->rules[] = 'regex';
            }
            
        }
    }
    
    public function getErrors() {
        $errors = array();
        foreach ( $this->errors as $rule => $error ) {
            $title = isset( $this->data['field_title'] ) ? $this->data['field_title'] : null;
            $errors["validate_$rule"] = sprintf( $error, $title );
        }
        return $errors;
    }
    
}
endif;