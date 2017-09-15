<?php

/**
 * Validate Uploaded Files
 * 
 * This class will validate uploaded files before moving them from
 * temporary to final directory.
 * 
 * @package Adverts
 * @subpackage Classes
 * @since 1.2.0
 * @access public
 * 
 */

class Adverts_Upload_Helper {
    
    /**
     * List of validators
     *
     * @var array
     */
    protected $_validator = array();

    /**
     * Adds new validator
     * 
     * @param mixed $validator  Callback to validation function
     */
    public function add_validator($validator) {
        $this->_validator[] = $validator;
    }

    /**
     * Get validator by name from registered validators array
     * 
     * @param string $name
     * @return mixed (array or null)
     */
    public function get_validator( $name ) {
        $validators = Adverts::instance()->get("field_validator", array());
        
        if(isset($validators[$name])) {
            return $validators[$name];
        } else {
            return null;
        }
    }
    
    /**
     * Validates the file
     * 
     * This function is executed usually by adverts_gallery_upload_prefilter filter
     * 
     * @see adverts_gallery_upload_prefilter filter
     * 
     * @param array $file   An element from $_FILES array
     * @return array        Updated element from $_FILES array
     */
    public function check( $file ) {
        foreach($this->_validator as $v) {
            $v = array_merge($this->get_validator( $v["name"] ), $v);

            if(empty($file) && $v["validate_empty"] === false ) {
                //continue;
            } 

            
            $result = call_user_func( $v["callback"], $file, $v["params"] );
            
            if( $result === true || $result === 1) {
                continue;
            }

            $find = array();
            $repl = array();

            foreach($v["params"] as $k => $pv) {
                $find[] = "%" . $k . "%";
                $repl[] = $pv;
            }

            if( isset($v["message"][$result]) ) {
                $file["error"] = str_replace($find, $repl, $v["message"][$result]);
            } elseif( isset($v["default_error"]) ) {
                $file["error"] = str_replace($find, $repl, $v["default_error"]); 
            } else {
                $file["error"] = __( "Invalid value.", "adverts" );
            }

            //if( isset($v["on_failure"]) && $v["on_failure"] == "break" ) {
                break;
            //}
        }

        return $file;
    }
}
