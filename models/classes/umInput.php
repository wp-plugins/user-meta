<?php
// Not in used so far

if ( ! class_exists( 'umInput' ) ) :
class umInput {
    
    /**
     * @var string Name of input element 
     */
    private $name;

    /**
     * @var string Type of input element 
     */
    private $type;
    
    /**
     * @var array valid input element's attributes, which will added to element
     */
    private $attr = array();
    
    /**
     * @var array Field's config 
     */
    private $config = array();
    
    /**
     * @var array Options for option's element 
     */
    private $options = array();
    
    private $optionsAttr = array();
    
    /**
     * @var bool Indicate options array should make options by array key or array value 
     */
    private $byKey;
    
    /**
     * @var mixed Contains value of input element 
     */
    private $value = null;
    
    /**
     * @var string Html string containing input attributes
     */
    private $attrHtml;
    
    /**
     * @var string Html string for input element
     */
    private $html;
    
    /**
     * @var array List of valid input attributes
     */
    private $validConfig;
    
    
    function __construct( $type = 'text', $attr = array() ) {
        $this->type     = trim( $type );
        
        $this->_setStatic();
        
        $this->_setName( $attr );
        $this->_setValue( $attr );
        $this->_setAttr( $attr );
        
        $this->setOptions();
        $this->setOptionAttr();
    }
    
    private function _setStatic() {
        $this->validConfig = array( 'before', 'after', 'enclose', 'field_enclose', 'by_key', 'combind',
            'label', 'for', 'label_id', 'label_class',
            'options', 'options_attr', 'option_before', 'option_after' ); 
        //if ( $type == 'checkbox' && ! empty( $attr['combind'] ) )
          //  array_push ( $excludeAttr, 'required' );
        
        return;
        
        $this->validAttr = array( 'type', 'name', 'value', 'id', 'class', 
            'required', 'readonly', 'disabled', 'size', 'maxlength' );
        
        switch ( $this->type ) {
            case 'textarea' :
                array_push( $this->validAttr, 'rows', 'cols' );
            break;
        
            case 'a' :
                array_push( $this->validAttr, 'href' );
            break;
        
            case 'img' :
                array_push( $this->validAttr, 'src' );
            break;
        }
        

    }
    
    /**
     * Set $this->name and sanitize $attr['name']
     * @param array $attr
     */
    private function _setName( $attr ) {
        $this->name = isset( $attr['name'] ) ? trim( $attr['name'] ) : '';
        
        if ( ! $this->name )
            return $attr;
        
        switch( $this->type ) {
            case 'multiselect' :
                $this->name = $this->name . '[]';
            break;
        
            case 'checkbox' :
                if ( ! empty( $attr['combind'] ) )
                    $this->name = $this->name . '[]';
            break;
        }
    }
    
    /**
     * Set $this->value and sanitize $attr['value']
     * @param array $attr
     */
    private function _setValue( $attr ) {
        if ( isset( $attr['value'] ) ) {
            if ( is_string( $attr['value'] ) )
                $this->value = esc_attr( trim( $attr['value'] ) );
            elseif ( is_array( $attr['value'] ) ) {
                $this->value = array_map( 'esc_attr', $attr['value'] );
            }
        }
    }
    
    /**
     * Split $attr to $this->attr and $this->config
     * 
     * @param array All attributes and config for input element
     */
    private function _setAttr( $attr ) {
        foreach ( $attr as $key => $val ) {
            if ( in_array( $key, $this->validConfig ) )
                $this->config[ $key ] = $val;
            else
                $this->attr[ $key ] = $val;
        }
    }
    
    /**
     * Set options for optionsElement
     * 
     * @param array Options array for optionsElement
     */
    function setOptions( $options = array() ) {
        $this->options = ( ! $options && ! empty( $this->config['options'] ) ) ? $this->config['options'] : $options;
    }
    
    function setOptionAttr( $optionsAttr = array() ) {
        $this->optionsAttr = ( ! $optionsAttr && ! empty( $this->config['options_attr'] ) ) ? $this->config['options_attr'] : $optionsAttr;;
    }
    
    /**
     * Build $this->attrHtml string from $this->attr array
     */
    private function buildAttr() {
        $attr = $this->_sanitizeAttr();
        foreach ( $attr as $key => $val ) {
            $this->attrHtml .= $this->notEmpty( $val ) ? $key . '="' . $val . '" ' : '';
        }
            
        $this->attrHtml = trim( $this->attrHtml );
    }

    
    private function _sanitizeAttr() {
        $attr = $this->attr;
        
        if ( ! empty( $this->name ) )
            $attr['name'] = $this->name;
        
        /**
         * exclude adding value 
         */
        $excludeType = array( 'select', 'multiselect', 'radio', 'checkbox', 'textarea', 'a', 'img', 'label' );
        if ( in_array( $this->type, $excludeType ) )
            unset( $attr['value'] );
        else
            $attr['value'] = $this->value;
        
        if ( $this->type == 'checkbox' && ! empty( $attr['combind'] ) )
            unset( $attr['required'] );
        
        return $attr;        
    }
    
    /**
     * Init optionElement: Applicable for select, multiselect, radio and checkbox
     */
    private function _initOptionElement() {
        //$this->setValue();
        //$this->buildAttr();
        
        $this->byKey = isset( $this->config['by_key'] ) ? $this->config['by_key'] : null; 
    }
    
    
    /**
     * Build input element. Init and store html to $this->html
     * Call $this->html_xxx() if method exists.
     */
    private function buildInput() {
        $methodName = 'html_' . $this->type;
        if ( method_exists( $this, $methodName ) )
           return $this->$methodName();
        
        $this->html_text();
    } 

    
    private function html_text() {
        $type = $this->type ? $this->type : 'text';
        $this->html = "<input type=\"$type\" {$this->attrHtml} />";
    }

    
    private function _buildSelectOption( $key, $val ) {
        $html = null;

        if ( is_array( $val ) ) {
            $html .= "<optgroup label=\"$key\">";
            foreach( $val as $k => $v )
                $html .= $this->_buildSelectOption( $k, $v );
            $html .= "</optgroup>";

        } else {
            if ( ! $this->byKey ) $key = $val;

            $selected = "selected=\"selected\"";
            if ( is_array( $this->value ) )
                $selected = in_array( $key, $this->value ) ? $selected : '';
            else
                $selected = ( $this->value == $key ) ? $selected : '';
            
            $optionAttr = '';
            if ( is_array( $this->optionsAttr ) ) {
                if ( isset( $this->optionsAttr[ $key ] ) )
                    $optionAttr = $this->optionsAttr[ $key ];
            } else
                $optionAttr = $this->optionsAttr;

            $html .= "<option value=\"$key\" $selected $optionAttr>$val</option>"; 
        }

        return $html;
    }
    
    
    private function html_select() {
        $this->_initOptionElement();
        
        $this->html = "<select {$this->attrHtml}>";
        if ( ! empty( $this->options ) && is_array( $this->options ) ) {
            foreach ( $this->options as $key => $val) 
                $this->html .= self::_buildSelectOption( $key, $val );

        }
        $this->html .= "</select>";
    }
    
    
    private function html_multiselect() {
        $this->_initOptionElement();
        
        //$name = rtrim( $name, "\"") . "[]\""; 
        //$html .= "<select $name multiple=\"multiple\" $include>";
        $this->html = "<select {$this->attrHtml}> multiple=\"multiple\"";
        $isInside = false;
        if ( ! empty( $this->options ) && is_array( $this->options ) ) {
            foreach ( $this->options as $key => $val) 
                $html .= self::_buildSelectOption( $key, $val );

        }
        $this->html .= "</select>";
    }
    
    
    private function html_radio() {
        $this->_initOptionElement();

        if ( $this->options && is_array( $this->options ) ) {
            
            $i = 0;
            foreach ( $this->options as $key => $val ) {
                
                if ( ! $this->byKey )
                    $key = $val; 
                
                $key = is_string( $key ) ? trim( $key ) : $key;
                $checked = ( $key == $this->value ) ? "checked=\"checked\"" : "";

                // Changing id for each option 
                if ( ! empty( $this->attr['id'] ) ) {
                    $includeModify  = str_replace( "id=\"{$this->attr['id']}\"",  "id=\"{$this->attr['id']}_$i\"", $this->attrHtml );
                    $label = "<label for=\"{$this->attr['id']}_$i\">$val</label>";
                } else {
                    $includeModify = $this->attrHtml;
                    $label = "<label>$val</label>";
                }
                
                $option = "<input type=\"$this->type\" $includeModify value=\"$key\" $checked /> $label";
                $this->html .= $this->_optionEnclosed( $option );
                
                $i++;
            } 
            
        } else
            $this->html_text();
    }
    
    
    private function html_checkbox() {
        $this->_initOptionElement();
        
        if ( ! empty( $this->config['combind'] ) ) {
            $name = rtrim( $name, "\"") . "[]\"";
            if ( is_array( $this->options ) ) {
                $i = 0;
                foreach ( $this->options as $key => $val ) {
                    if ( ! $this->byKey ) $key = $val; 
                    $key = is_string( $key ) ? trim( $key ) : $key;
                    if ( is_array( $this->value ) )
                        $checked = in_array( $key, $this->value ) ? "checked=\"checked\"" : "";
                    else
                        $checked = ( $key == $this->value ) ? "checked=\"checked\"" : "";

                    // Changing id for each option 
                    if ( ! empty( $this->attr[ 'id' ] ) ) {
                        $includeModify  = str_replace( "id=\"{$attr['id']}\"",  "id=\"{$attr['id']}_$i\"", $this->attrHtml );
                        $label = "<label for=\"{$this->attr['id']}_$i\">$val</label>";
                    } else {
                        $includeModify = $this->attrHtml;
                        $label = "<label>$val</label>";
                    }

                    $option = "<input type=\"$type\" $includeModify value=\"$key\" $checked /> $label";
                    $this->html .= $this->_optionEnclosed( $option );

                    $i++;
                }     
            }

        } else {
            $checked = '';
            if ( isset( $this->attr['checked'] ) )
                $checked = ! empty( $this->attr['checked'] ) ? "checked=\"checked\"" : "";
            elseif ( ! empty( $this->value ) )
                $checked = "checked=\"checked\"";

            $checkboxValue = ( ! empty( $this->value ) && ! is_array( $this->value ) ) ? $this->value : 1;
            $include .= 'value="' . $checkboxValue . '"';

            $this->html = "<input type=\"$type\" $this->attrHtml $checked />";
        }
    }
    
    
    private function html_textarea() {
        $this->html = "<textarea {$this->attrHtml}>{$this->value}</textarea>";
    }
    
    
    private function html_a() {
        $this->html = "<a {$this->attrHtml}>{$this->value}</a>";
    }
    
    
    private function html_img() {
        $this->html = "<img {$this->attrHtml} />";
    }
    
    
    private function html_file() {
        $this->html = "<input type=\"{$this->type}\" {$this->attrHtml} />";
        $form_id = @$$this->config['form_id'];      
        if ( $form_id ) {
            ?><script type="text/javascript">
                    var form = document.getElementById($form_id);
                    form.encoding = "multipart/form-data";
                    form.setAttribute('enctype', "multipart/form-data");
            </script><?php  
        }
    }
    
    
    private function html_label() {
        $for   = isset( $this->config['for'] ) ? "for=\"{$this->config['for']}\"" : '';
        $this->html = "<label $for {$this->attrHtml}>{$this->value}</label>";
    }

    
    private function _optionEnclosed( $html ) {
        if ( isset( $this->config['option_before'] ) )
            $html = $this->config['option_before'] . $html;

        if ( isset( $this->config['option_after'] ) )
            $html .= $this->config['option_after'];
        
        return $html;
    }

    
    private function beforeAfterInput() {
        if ( ! empty( $this->config['before'] ) )
            $this->html = $this->config['before'] . $this->html;
        
        if ( ! empty( $this->config['after'] ) )
            $this->html .= $this->config['after'];
        
        if ( ! empty( $this->config['field_enclose'] ) ) {
            $enclose = $this->config['field_enclose'];
            $encloseTag = explode( ' ', trim( $enclose ) );
            $encloseTag = $encloseTag[0];
            $this->html = "<$enclose>{$this->html}</$encloseTag>";
        }
    }
    
    /**
     * Enclose by other html element
     */
    private function encloseField() {            
        if ( ! empty( $this->config['enclose'] ) ) {
            $enclose = $this->config['enclose'];
            $encloseTag = explode( ' ', trim( $enclose ) );
            $encloseTag = $encloseTag[0];
            $this->html = "<$enclose>{$this->html}</$encloseTag>";
        }
    }
    
    /**
     * Add lebel if required  
     */
    private function AddLabel() {
        if ( ! isset( $this->config['label'] ) ) return;
        
        $labelId    = ! empty( $this->config['label_id'] )      ? "id=\"{$this->config['label_id']}\"" : '';
        $labelClass = ! empty( $this->config['label_class'] )   ? "class=\"{$this->config['label_class']}\"" : '';
        $for        = ! empty( $this->attr['id'] )              ? "for=\"{$this->attr['id']}\"" : '';
              
        $labelAttr = trim( "$labelId $labelClass $for" );
        
        $html = '<label';
        if ( $labelAttr )
            $html .= " $labelAttr";
        $html .= '>';
        
        
        if ( $this->type == 'checkbox' && empty( $this->config['combind'] ) )
            $html .= "$this->html {$this->config['label']}</label>";
        else
            $html .= "{$this->config['label']}</label>" . '' . $this->html;
            
        $this->html = $html;
        
        return;
            
        $htmlLabel = "<label $label_id $label_class $for>{$this->config['label']}</label>";
        if ( $this->type == 'checkbox' && empty( $this->config['combind'] ) )
            $this->html = "<label $label_id $label_class $for>$this->html {$this->config['label']}</label>";
        else
            $this->html = $htmlLabel . ' ' . $this->html;
       
    }

    
    private function notEmpty( $data ) {
        if ( is_int( $data ) )
            return true;
        elseif ( is_string( $data ) ) {
            $data = trim( $data );
            if( ( "0" == $data ) || ! empty( $data ) )
                return true;
        } else {
            if ( ! empty( $data ) )
                return true;
        }

        return false;
    }
    
    
    function render() {
        $this->buildAttr();
        $this->buildInput();
        $this->beforeAfterInput();
        $this->AddLabel();
        $this->encloseField();
        
        return $this->html;
    }
    
}
endif;