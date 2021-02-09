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
     * Form name
     *
     * @since 1.5.0
     * @var string
     */
    protected $_form_name = null;
    
    /**
     * Field that is being uploaded
     *
     * @since 1.5.0
     * @var array
     */
    protected $_field = null;
    
    /**
     * Unique ID set for this field
     *
     * @since 1.5.0
     * @var string
     */
    protected $_uniqid = null;
    
    /**
     * Related post ID
     *
     * @since 1.5.0
     * @var int
     */
    protected $_post_id = null;
    
    /**
     * List of validators
     *
     * @var array
     */
    protected $_validator = array();
    
    private $_upload = array();

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
                $file["error"] = __( "Invalid value.", "wpadverts" );
            }

            //if( isset($v["on_failure"]) && $v["on_failure"] == "break" ) {
                break;
            //}
        }

        return $file;
    }
    
    /**
     * Sets Field
     * 
     * @since   1.5.0
     * @param   array   $field  Field definition from the form scheme
     * @return  void
     */
    public function set_field( $field ) {
        $this->_field = $field;
    }
    
    /**
     * Returns Field
     * 
     * @since   1.5.0
     * @return  array   Field definition from the form scheme
     */
    public function get_field() {
        return $this->_field;
    }
    
    /**
     * Checks if the field (self::set_field()) is a file.
     * 
     * The function will return true only if the field is for a file saved 
     * in the file system directory (NOT in the media library).
     * 
     * @since   1.5.0
     * @return  boolean
     */
    public function is_file() {
        if( isset( $this->_field["save"] ) && $this->_field["save"]["method"] == "file" ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Sets Form Name
     * 
     * @since   1.5.0
     * @param   string  $form_name
     */
    public function set_form_name( $form_name ) {
        $this->_form_name = $form_name;
    }
    
    /**
     * Returns Form Name
     * 
     * @since   1.5.0
     * @return  string      
     */
    public function get_form_name() {
        return $this->_form_name;
    }
    
    /**
     * Sets Uniqid
     * 
     * @since   1.5.0
     * @param   string  $uniqid
     */
    public function set_uniquid( $uniqid ) {
        $this->_uniqid = $uniqid;
    }
    
    /**
     * Returns Uniqid
     * 
     * @since   1.5.0
     * @return  string
     */
    public function get_uniquid() {
        return $this->_uniqid;
    }
    
    /**
     * Sets Post ID
     * 
     * @since   1.5.0
     * @param   int     $post_id
     */
    public function set_post_id( $post_id ) {
        $this->_post_id = $post_id;
    }
    
    /**
     * Returns Post ID
     * 
     * @since   1.5.0
     * @return  int
     */
    public function get_post_id() {
        return $this->_post_id;
    }
    
    /**
     * Handles the file upload
     * 
     * @since   1.5.0
     * @return  void
     */
    public function upload() {
        if( $this->is_file() ) {
            $this->_upload = array();
            add_filter( 'pre_move_uploaded_file', array( $this, "move_file" ), 10, 4 );
        }
        
        $status = wp_handle_upload($_FILES['async-upload'], array('test_form'=>true, 'action' => 'adverts_gallery_upload'));
        
        if( $this->is_file() ) {
            remove_filter( 'pre_move_uploaded_file', array( $this, "move_file" ), 10, 4 );
            $status["file"] = $this->_upload["file"];
            $status["url"] = $this->_upload["url"];
        }
        
        return $status;
    }
    
    /**
     * Moves uploaded file
     * 
     * @since   1.5.0
     * @param   string      $move
     * @param   type        $file
     * @param   type        $new_file
     * @param   type        $type
     * @return  boolean
     */
    public function move_file( $move, $file, $new_file, $type ) {

        if( $this->get_post_id() ) {
            $path = $this->get_path_dest();
            $uri = $this->get_uri_dest();
        } else {
            $path = $this->get_path();
            $uri = $this->get_uri(); 
        }
        

        $mkdir = wp_mkdir_p( $path );
        
        if( $mkdir === false ) {
            // rise error
            return false;
        }
        
        $name = sanitize_file_name( $file["name"] );
        
        $actual_name = pathinfo( $name, PATHINFO_FILENAME );
        $original_name = $actual_name;
        $extension = pathinfo( $name, PATHINFO_EXTENSION );

        $i = 1;
        while( file_exists( rtrim( $path ) . "/" .$actual_name . "." . $extension ) ) {           
            $actual_name = (string)$original_name . "-" . $i;
            $name = $actual_name.".".$extension;
            $i++;
        }
        
        
        $move = rtrim( $path ) . "/" . $name;
        
        $move_new_file = @move_uploaded_file( $file['tmp_name'], $move );
        
        $this->_upload["file"] = $move;
        $this->_upload["url"] = $uri . "/" . $name;
        
        return $move;
    }
    
    public function move_files() {
        wpadverts_rename_dir( $this->get_path(), $this->get_path_dest() );
        
        $path = $this->get_path();
        
        // maybe cleanup old path
        do {
            $files = glob( rtrim( $path, "/" ) . "/*" );
            if( ! is_array( $files ) ) {
                $files = array();
            }
            if( is_dir( $path ) && empty( $files ) ) {
                rmdir( $path );
            } else {
                // do nothing
            }
            $path = dirname( $path );
        } while( empty( $files ) );
    }
    
    public function get_path() {
        $dirs = wp_upload_dir();
        $basedir = $dirs["basedir"];
        $baseurl = $dirs["baseurl"];

        $field = $this->_field;

        $path = str_replace(
            array( "{tmpdir}", "{basedir}", "{uniqid}", "{field_name}", "{form_name}" ),
            array( adverts_get_tmp_dir(), $basedir, $this->_uniqid, $field["name"], $this->get_form_name() ),
            $field["save"]["path"]
        );
        
        return $path;
    }
    
    public function get_path_dest() {
        $dirs = wp_upload_dir();
        $basedir = $dirs["basedir"];
        $baseurl = $dirs["baseurl"];

        $field = $this->_field;

        $path = str_replace(
            array( "{basedir}", "{post_id}", "{field_name}", "{form_name}" ),
            array( $basedir, $this->get_post_id(), $field["name"], $this->get_form_name() ),
            $field["save"]["dest"]
        );
        
        return $path;
    }
    
    public function get_uri() {
        $dirs = wp_upload_dir();
        $basedir = $dirs["basedir"];
        $baseurl = $dirs["baseurl"];

        $field = $this->_field;

        
        $uri = str_replace(
            array( "{tmpdir}", "{basedir}", "{uniqid}", "{field_name}", "{form_name}" ),
            array( adverts_get_tmp_url(), $baseurl, $this->_uniqid, $field["name"], $this->get_form_name() ),
            $field["save"]["path"]
        ); 
        
        return $uri;
    }
    
    public function get_uri_dest() {
        $dirs = wp_upload_dir();
        $basedir = $dirs["basedir"];
        $baseurl = $dirs["baseurl"];

        $field = $this->_field;

        
        $uri = str_replace(
            array( "{basedir}", "{post_id}", "{field_name}", "{form_name}" ),
            array( $baseurl, $this->get_post_id(), $field["name"], $this->get_form_name() ),
            $field["save"]["dest"]
        ); 
        
        return $uri;
    }
}
