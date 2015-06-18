<?php
/**
 * Generate UM Form
 */

if ( ! class_exists( 'umFormGenerate' ) ) :
class umFormGenerate extends umFormBaseSanitized {
    
    function __construct( $formName, $actionType = '', $userID = 0 ) {
        parent::__construct( $formName, $actionType, $userID );
    }

}
endif;
