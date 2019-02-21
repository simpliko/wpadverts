<?php
/**
 * Emails Module - Emails Parser Interface
 * 
 * The interface which the any email parser should implement.
 * 
 * The interface makes sure that a new email parser will implement all methods
 * required for the parser to work properly.
 * 
 * @author Grzegorz Winiarski
 * @since 1.3.0
 * @package Adverts
 * @subpackage Emails
 */

interface Adext_Emails_Parser_Interface {
    
    /**
     * Compiles the email arguments.
     * 
     * @since   1.3.0
     * @param   array       $mail_args     An array with keys (to, subject, message, headers, attachments)
     * @param   string      $message       Message key
     * @param   array       $args          Variables passed to the message
     * @return  array                      Compiled message
     */
    public function compile( $mail_args, $message, $args );
    
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
    public function add_function( $name, $callback );
}