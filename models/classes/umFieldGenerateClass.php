<?php

/**
 * Not in use so far
 */

if ( ! class_exists( 'umFieldGenerate' ) ) :
class umFieldGenerate {
    
    protected $id;
    
    protected $type;
    
    protected $data;
    
    
    protected $adminOnly;
    
    protected $readOnly;
    
    protected $required;
    
    
    protected $inputType;
    
    protected $label;
    
    protected $inputID;
    
    protected $inputClass;
    
    protected $inputStyle;
    
    protected $labelID;
    
    protected $labelClass;
    
    protected $divClass;
    
    
    protected $html;
    
    protected $attr;
    
    protected $validation;
    
    //protected $required;
    
    //protected $readonly;
    
    public static $uniqueID;
    
    
    function __construct( $id, $data ) {
        global $userMeta;
        
        $this->id   = $id;
        $this->data = is_array( $data ) ? $data : array();
        
        $this->type = ! empty( $data['field_type'] ) ? $data['field_type'] : '';
        
        $this->inputType    = 'text';
        
        
        $this->init();
        
        $userMeta->dump($data);
    }
    
    function init() {
        $field = $this->data;
        
        $this->inputID  = empty( $field['input_id'] ) ? "um_field_{$this->id}_" . self::$uniqueID : $field['input_id'];
        $labelID        = empty( $field['label_id'] ) ? $this->inputID . '_label' : $field['label_id'];
        $descriptionID  = empty( $field['description_id'] ) ? "id=\"{$this->inputID}_description\"" : "id=\"{$field['description_id']}\"";
        
        $this->divClass = 'um_field_container';
        
        $this->inputClass   = 'um_field_' . $this->id . ' um_input';
        if ( ! empty( $field['field_class'] ) )
            $this->inputClass .= ' ' . $field['field_class'];

        if ( ! empty( $field['label_class'] ) )
            $this->labelClass .= $field['label_class'];

        if ( ! empty( $field['field_style'] ) )
            $inputStyle .= $field['field_style'];
        
        
        
        /*if ( ! empty( $field['admin_only'] ) ) :
            if( !$userMeta->isAdmin() )
                return;
        endif;

        if ( ! empty( $field['non_admin_only'] ) ) :
            if( $userMeta->isAdmin($userID) )
                return;
        endif;

        if ( ! empty( $field['read_only_non_admin'] ) ) :
            if( !($userMeta->isAdmin()) )
                $fieldReadOnly = 'readonly'; 
        endif;

        if ( ! empty( $field['read_only'] ) )
            $fieldReadOnly = 'readonly'; */
        
        

        if ( ! empty( $field['required'] ) ) {
            $this->required = true;
            $this->validation .= 'required,';
        }

        if ( ! empty( $field['title_position'] ) ) {
            if ( isset( $field['field_title'] ) && ( ! in_array( $field['title_position'], array( 'hidden', 'placeholder' ) ) ) )
                $this->label = $field['field_title'];
        }
        
        if ( ! empty( $field['title_position'] ) ) {
            if ( $field['title_position'] == 'top' )
                $this->labelClass .= ' um_label_top';
            elseif ( $field['title_position'] == 'left' )
                $this->labelClass .= ' um_label_left';       
            elseif ( $field['title_position'] == 'right' )
                $this->labelClass .= ' um_label_right';
            elseif ( $field['title_position'] == 'inline' ) {
                $this->labelClass .= ' um_label_inline ';
                $this->divClass .= ' um_inline';
            } elseif ( $field['title_position'] == 'placeholder' ) {
                $field['placeholder'] =  isset( $field['placeholder'] ) ? $field['placeholder'] : $field['field_title'];
            }

        }

        if ( ! empty( $field['field_size'] ) ) {
            $inputStyle .= "width:{$field['field_size']}; ";
        }

        if ( ! empty( $field['field_height'] ) ) {
            $inputStyle .= "height:{$field['field_height']}; ";
        }

        if ( ! empty( $field['max_char'] ) ) {
            $maxlength = $field['max_char'];
        }

        if ( isset( $field['css_class'] ) ) {
            $divClass .= " {$field['css_class']} ";
        }

        if ( isset( $field['css_style'] ) ) {
            $divStyle .= "{$field['css_style']} ";
        }


        if ( isset( $field['options'] ) ) {
            $by_key = true;
            if ( ! is_array( $field['options'] ) ) {
                $fieldSeparator = ! empty( $field['field_separator'] ) ? $field['field_separator'] : ',';
                $keySeparator   = ! empty( $field['key_separator'] ) ? $field['key_separator'] : '=';
                $fieldOptions = $userMeta->toArray( esc_attr( $field['options'] ), $fieldSeparator, $keySeparator );
            } else
                $fieldOptions = $field['options'];
        }
        
    }
    
    function get( $key ) {
        return isset( $this->data[ $key ] ) ? $this->data[ $key ] : '';
    }
    
    function buildAttr() {
        $attr = array(
            'value'         => $this->get( 'field_value' ),
            'label'         => $this->label,
            'id'            => $this->inputID,
            'class'         => $this->inputClass,
            'style'         => $this->inputStyle,
            'label_id'      => $this->labelID,
            'label_class'   => $this->labelClass,
            'readonly'      => $this->readOnly,
        );
        
        $this->attr = $attr;
        
        return;
        
        $attr = array(
            //"value"         => isset( $field['field_value'] ) ? $field['field_value'] : "",
            //"label"         => __( $fieldTitle, $userMeta->name),
            //"readonly"      => ! empty( $fieldReadOnly )      ? $fieldReadOnly : "",
            "disabled"      => ! empty( $isDisabled ) ? true : false,
            //"id"            => $inputID,
            //"class"         => $class,
            //"style"         => @$inputStyle                   ? $inputStyle : "",
            "maxlength"     => $maxlength,
            "option_after"  => isset( $option_after )         ? $option_after : "",
            "by_key"        => $by_key,
            //"label_id"      => $labelID,
            //"label_class"   => $label_class,
            "onblur"        => isset( $onBlur )               ? $onBlur : "",
            "combind"       => isset( $combind )              ? $combind : false,
            "before"        => $fieldBefore,
            "after"         => $fieldAfter,
            "placeholder"   => isset( $field['placeholder'] ) ? $field['placeholder'] : "",
        );
     
    }
    
    function createInput() {
        global $userMeta;
        
        return $userMeta->createInput( $this->get( 'field_name' ), $this->inputType, $this->attr, array() );  
    }
    
    function _element_user_email() {
        $this->inputType = 'email';
        return $this->createInput();
    }
    
    function createElement() {
        
    }
     
    function html() {
        $this->buildAttr();
        
        return $this->_element_user_email();
    }
    
}
endif;