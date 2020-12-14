<?php
/**
 * Emails Module - Emails Parser
 * 
 * The default email parser. This class is responsible for replacing variables in the emails.
 * (The variable looks like this {$var.property|callback:arg})
 * 
 * If you would like to use your own variable then you can do that by adding the code
 * below in your theme functions.php file
 * 
 * <code>
 * add_action( "init", function() {
 *      Adext_Emails::instance()->set_parser( new My_Parser_Class );
 * });
 * </code>
 * 
 * The My_Parser_Class needs to implement Adext_Emails_Parser_Interface interface
 * otherwise using set_parser() method will cause a fatal error.
 * 
 * Refer to class-emails-parser.php file to see how the parser class should look like.
 * 
 * @author Grzegorz Winiarski
 * @since 1.3.0
 * @package Adverts
 * @subpackage Emails
 */

class Adext_Emails_Parser implements Adext_Emails_Parser_Interface {
    
    /**
     * Variables assigned to the email
     *
     * @since   1.3.0
     * @var     array
     */
    protected $_args = array();
    
    /**
     * Variables in flat array format.
     * 
     * The variables are flatten using dot. For example an array
     * <code>array( "x" => 10, "y" => 20, "xy" => array( 0 => 10, "z" => 20 )</code>
     * after flattening will be saved as
     * <code>array( "x" => 10, "y" => 20, "xy.0" => 10, "xy.z" => 20 )</code>
     * 
     * @since   1.3.0
     * @var     array
     */
    protected $_flat = array();
    
    /**
     * Listed of registered callbacks
     * 
     * The callbacks can be applied to variables in the email.
     *
     * @since   1.3.0
     * @var     array
     */
    protected $_functions = array();
    
    /**
     * Class constructor
     * 
     * @since 1.3.0
     */
    public function __construct() {
        
    }
    
    /**
     * Assign variable to parser
     * 
     * Only assigned variables can be used when parsing an email template.
     * 
     * @since   1.3.0
     * @param   string    $name   Variable name
     * @param   mixed     $value  Variable value
     * @return  void
     */
    public function assign( $name, $value ) {
        $this->_args[$name] = $value;
    }
    
    /**
     * Add a custom function
     * 
     * The function can be later use on a variable in the message.
     * 
     * For example if you register function named my_custom_func then you can
     * use it on a variable with using this syntax {$advert.ID|my_custom_func}
     * 
     * @since   1.3.0
     * @param   string    $name       Function name
     * @param   mixed     $callback   Valid callback function
     * @return  void
     */
    public function add_function( $name, $callback ) {
        $this->_functions[ $name ] = $callback;
    }
    
    /**
     * Flattens variables (self::$_args)
     * 
     * @since   1.3.0
     * @return  array
     */
    public function flatten() {
        $this->_flat = apply_filters( "wpadverts_email_parser_flatten", $this->_flatten_inner( array(), array(), $this->_args ) );
        return $this->_flat;
    }
    
    /**
     * Recursively flattens variables.
     * 
     * This function is run by self::flatten()
     * 
     * @since   1.3.0
     * @param   array   $flat       
     * @param   array   $prefix     Prefix for flat key name
     * @param   array   $data       Variables to flatten
     * @return  array               Flatten self::$_args array
     */
    protected function _flatten_inner( $flat, $prefix, $data ) {
        foreach( $data as $key => $value ) {
            
            $t = $prefix;
            $t[] = $key;
            $pfix = implode( ".", $t );

            $flat[ $pfix ] = $value;
            
            if( is_array( $value ) ) {
                $flat = array_merge( $flat, $this->_flatten_inner( $flat, $t, $value ) );
            } else if( is_object( $value ) ) {
                $flat = array_merge( $flat, $this->_flatten_inner( $flat, $t, $value ) );
            } 
        }
        return $flat;
    }
    
    protected function _rec_var( $path, $x ) {

        if( empty( $path ) ) {
            return $x;
        }

        $key = array_shift( $path );

        if( is_scalar( $x ) ) {
            return $x;
        } else if( is_object( $x ) && isset( $x->$key) ) {
            return $this->_rec_var( $path, $x->$key);
        } else if( is_array( $x ) && isset( $x[$key] ) ) {
            return $this->_rec_var( $path, $x[$key] );
        } else {
            return "";
        }

    }
    
    /**
     * Parses text
     * 
     * This function finds variables in the $text and replaces them with
     * actual variable values and applies filter functions
     * 
     * @since   1.3.0
     * @param   string  $text   Text to parse
     * @return  string          Parsed text
     */
    public function parse( $text ) {
        
        $matches = array();
        //preg_match_all( '/\{\$([A-z0-9\._\:\ }\|]*)\}/', $text, $matches );
        preg_match_all( '/\{\$([^\}]*)\}/', $text, $matches );

        $flat = $this->_flat;
        $vars = array();
        $count = count( $matches[0] );
        
        for( $i = 0; $i<$count; $i++ ) {

            if( stripos( $matches[1][$i], "|" ) !== false ) {
                list( $var, $filters ) = explode( "|", $matches[1][$i] );
            } else {
                $var = $matches[1][$i];
                $filters = null;
            }
            $vars[ $matches[0][$i] ] = array( 
                "value" => $var, 
                "callback" => $filters
            );
        }

        foreach( $vars as $var => $repl ) {
            /*
            if( isset( $flat[ $repl["value"] ] ) ) {
                $value = $flat[ $repl["value"] ];
            } else {
                $value = "";
            }
             * 
             */
            $value = $this->_rec_var( explode( ".", $repl["value"] ), $this->_args );
            
            
            if( ! empty( $repl["callback"] ) ) {
                $cb = array();
                $cb_array = explode( "|", trim( $repl["callback"], "|" ) );
                foreach( $cb_array as $cb_string ) {
                    $cb_args = explode( ":", $cb_string );
                    $callback = array_shift( $cb_args );
                    array_unshift( $cb_args, $value );

                    if( isset( $this->_functions[ $callback ] ) ) {
                        $value = call_user_func_array( $this->_functions[ $callback ], $cb_args );
                    } else {
                        $value = call_user_func_array( $callback, $cb_args );
                    }
                }
            }
            $text = str_replace( $var, $value, $text );
        }
        
        return $text;
    }
    
    /**
     * Resets variables and the flat array.
     * 
     * @since   1.3.0
     * @return  void
     */
    public function reset() {
        $this->_args = array();
        $this->_flat = array();
    }
    
    /**
     * Compiles the email arguments.
     * 
     * @since   1.3.0
     * @param   array       $mail_args     An array with keys (to, subject, message, headers, attachments)
     * @param   string      $message       Message key
     * @param   array       $args          Variables passed to the message
     * @return  array                      Compiled message
     */
    public function compile( $mail_args, $message, $args ) {
        
        foreach( $args as $arg_name => $arg_value ) {
            $this->assign( $arg_name, $arg_value );
        }
        $this->flatten();
        
        //echo "<pre>";
        //print_r($args);
        //print_r($mail_args);
        //echo "</pre>";

        $mail_args["to"] = $this->parse( $mail_args["to"] );
        $mail_args["subject"] = $this->parse( $mail_args["subject"] );
        $mail_args["message"] = $this->parse( $mail_args["message"] );
        foreach( $mail_args["headers"] as $key => $header ) {
            $mail_args["headers"][$key] = $this->parse( $header );
        }
        if( isset( $mail_args["attachments"] ) && is_array( $mail_args["attachments"] ) ) {
            foreach( $mail_args["attachments"] as $key => $attachment ) {
                $mail_args["attachments"][$key] = $this->parse( $attachment );
            }

            $mail_args["attachments"] = join( "\n", $mail_args["attachments"] );
        }
        
        $this->reset();
        
        //echo "<pre>";
        //print_r($mail_args);
        //echo "</pre>";
        //exit;
        
        return $mail_args;
    }
}