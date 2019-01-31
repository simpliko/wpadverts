<?php

class Adext_Emails_Parser {
    
    protected $_args = array();
    
    protected $_flat = array();
    
    public function __construct() {
        
    }
    
    public function assign( $name, $value ) {
        $this->_args[$name] = $value;
    }
    
    public function flatten() {
        $flat = array();
        
        foreach( $this->_args as $key => $value ) {
            if( is_array( $value ) ) {
                
            }
        }
        
        $this->_flat = apply_filters( "wpadverts_email_parser", $flat, $this->_args );
    }
    
    protected function _flatten_inner( $prefix, $data ) {
        
    }
    
    public function parse( $text ) {
        preg_match_all( '/\{\$([A-z0-9\._]*)((|[A-z0-9_]?.*)([:]?[A-z0-9_]?))?\}/', $body, $matches );

        $vars = array();
        $count = count( $matches[0] );
        
        for( $i = 0; $i<$count; $i++ ) {
            $vars[ $matches[0][$i] ] = array( 
                "value" => $matches[1][$i], 
                "callback" => $matches[2][$i]
            );
        }

        foreach( $vars as $var => $repl ) {
            if( isset( $this->_flat[ $repl["value"] ] ) ) {
                $value = $this->_flat[ $repl["value"] ];
            } else {
                $value = "";
            }

            if( ! empty( $repl["callback"] ) ) {
                $cb = array();
                $cb_array = explode( "|", trim( $repl["callback"], "|" ) );
                foreach( $cb_array as $cb_string ) {
                    $cb_args = explode( ":", $cb_string );
                    $callback = array_shift( $cb_args );
                    array_unshift( $cb_args, $value );

                    $value = call_user_func_array( $callback, $cb_args );
                }
            }
            $body = str_replace( $var, $value, $body );
        }
        
        return $body;
    }
    
    public function reset() {
        $this->_args = array();
        $this->_flat = array();
    }
    
    public function compile( $mail_args, $message, $args ) {
        
        $this->assign( "args", $args );
        $this->flatten();

        $mail_args["to"] = $this->parse( $mail_args["to"] );
        $mail_args["subject"] = $this->parse( $mail_args["subject"] );
        $mail_args["message"] = $this->parse( $mail_args["message"] );
        foreach( $mail_args["headers"] as $key => $header ) {
            $mail_args["headers"][$key] = $this->parse( $header );
        }
        foreach( $mail_args["attachments"] as $key => $attachment ) {
            $mail_args["attachments"][$key] = $this->parse( $attachment );
        }
        
        $this->reset();

        return $mail_args;
    }
}