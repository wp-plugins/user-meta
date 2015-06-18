<?php
/**
 * 
 * Without setting $fromName parameter, it is possible to get all fields by calling getAllFields method.
 * When $formName is set, $this->data contains form's data and $this->data['fields'] contains populated fields.
 */

if ( ! class_exists( 'umFormBase' ) ) :
class umFormBase {
    
    /**
     * @var (string) Form Name 
     */
    protected $name;
    
    /**
     * @var (array) Form Data including populated fields.
     */
    protected $data = array();
    
    /**
     * @var (bool) Is form found in DB? 
     */
    protected $found;
    
    /**
     * @var (array) All shared fields from DB. 
     */
    protected $allFields = array();
    
    
    /**
     * @param (string) $formName
     */
    function __construct( $formName = null ) {
        $this->name = $formName;

        /**
         * get all shared fields from db and set to $this->allFields.
         */
        $this->_loadAllFields();
        
        /**
         * Populate: $this->found, $this->data, Sanitize: $this->data['fields']
         */
        if ( ! empty( $this->name ) ) {
            $this->_loadForm();
            $this->_initForm();
        }
    }
    
    /**
     * get all shared fields from db and set to $this->allFields.
     */
    private function _loadAllFields() {
        global $userMeta;
        
        $allFields = $userMeta->getData( 'fields' );
        $this->allFields    = is_array( $allFields ) ? $allFields : array();
    }
    
    /**
     * Load raw form and form's fields from DB.
     */
    private function _loadForm() {
        global $userMeta;
        
        if ( empty( $this->name ) ) return;
        
        if ( 'wp_backend_profile' == $this->name ) {
            $backendProfile = $userMeta->getSettings( 'backend_profile' );                  
            $this->data['fields']  = isset( $backendProfile['fields'] ) ? $backendProfile['fields'] : array();
            
        } else {
            $forms  = $userMeta->getData( 'forms' );
            if ( isset( $forms[ $this->name ] ) ) {
                $this->found    = true;
                $this->data     = $forms[ $this->name ];
            }
        }
    }
    
    /**
     * Populate: $this->found, $this->data.
     * Sanitize: $this->data['fields']: Merge by $this->allFields.
     * Set: $field['is_shared'] in case of shared field.
     */
    private function _initForm() {
        global $userMeta;
        
        $formFields = array();
        if ( ! empty( $this->data['fields'] ) && is_array( $this->data['fields'] ) ) {
            foreach ( $this->data['fields'] as $id => $field ) {
                $id     = is_array( $field ) ? $id : $field;
                $field  = is_array( $field ) ? $field : array();
                if ( ! empty( $this->allFields[ $id ] ) && is_array( $this->allFields[ $id ] ) ) {
                    $field  = array_merge( $this->allFields[ $id ], $field );
                    $field['is_shared'] = true;
                }
                
                $field['id'] = $id;
                
                if ( ! empty( $field['field_type'] ) )
                    $formFields[ $id ] = $field;
            }
        }

        $this->data['fields'] = $formFields;
    }    
    
    /**
     * Is form found in DB?
     * @return (bool)
     */
    function isFound() {
        return $this->found ? true : false;
    }
    
    /**
     * Get raw shared fields from DB.
     * @return (array)
     */
    function getAllFields() {
        return $this->allFields;
    }
    
    function getData() {
        return $this->data;
    }
    
}
endif;
