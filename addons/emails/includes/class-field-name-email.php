<?php
/**
 * Emails Module - Field Name 
 * 
 * This class handles the Name and Email field used in wp-admin / Classifieds / Options / Emails / Edit panel.
 * 
 * The class generates a field with two input fields
 * <input type="text" /> <input type="text" />
 * 
 * @author Grzegorz Winiarski
 * @since 1.3.0
 * @package Adverts
 * @subpackage Emails
 */

class Adext_Emails_Field_Name_Email {
    
    /**
     * Class constructor
     * 
     * Registers init action
     * 
     * @since 1.0
     * @return self
     */
    public function __construct() {
        $this->init();
    }
    
    /**
     * Init Action
     * 
     * Executed by self::__construct()
     * 
     * @since 1.0
     * @return void
     */
    public function init() {
        
        // Register <input> <input> input
        adverts_form_add_field("adext_emails_field_name_email", array(
            "renderer" => array( $this, "renderer" ),
            "callback_save" => array( $this, "save" ),
            "callback_bind" => array( $this, "bind" ),
        ));
    }
    
    /**
     * Renderer for the fullname field
     * 
     * This function displays HTML for the fullname input field.
     * 
     * The renderer is registered in self::init()
     * 
     * @since 1.0
     * 
     * @param   array   $field      Field params
     * @return  void
     */
    public function renderer( $field = null ) {
        $field1 = $field;
        $field2 = $field;
        
        $field1["placeholder"] = __( "Full Name", "wpadverts" );
        $field1["name"] = $field["name"] . "[name]";
        $field1["value"] = $field["value"]["name"];
        
        $field2["placeholder"] = __( "Email Address (e.g. user@example.com)", "wpadverts" );
        $field2["name"] = $field["name"] . "[email]";
        $field2["value"] = $field["value"]["email"];
        
        echo '<div class="adext-emails-field-name-email">';
        adverts_field_text($field1);
        adverts_field_text($field2);
        echo '</div>';
        
        return;
        
        if( isset( $field["placeholder"]["user_firstname"] ) ) {
            $uf_placeholder = $field["placeholder"]["user_firstname"] ;
        } else {
            $uf_placeholder = __( "First Name", "wpadverts" );
        }
        
        echo adverts_field_text(array(
            "name" => "user_name[user_firstname]",
            "class" => "wpadverts-authors-field-fullname-input",
            "value" => $field["value"]["user_firstname"],
            "placeholder" =>  $uf_placeholder
        ));
        
        echo '<span class="wpadverts-authors-field-fullname-input-spacing">&nbsp;</span>';
        
        if( isset( $field["placeholder"]["user_lastname"] ) ) {
            $ul_placeholder = $field["placeholder"]["user_lastname"] ;
        } else {
            $ul_placeholder = __( "Last Name", "wpadverts" );
        }
        
        echo adverts_field_text(array(
            "name" => "user_name[user_lastname]",
            "class" => "wpadverts-authors-field-fullname-input",
            "value" => $field["value"]["user_lastname"],
            "placeholder" => $ul_placeholder
        ));

    }
    
    /**
     * Binds fullname data in the form
     * 
     * This function is executed using adverts_form_bind filter registered in
     * self::init()
     * 
     * @see adverts_form_load filter
     * 
     * @since 1.0
     * 
     * @param   array   $field  Field scheme
     * @param   mixed   $value  Value submitted via form
     * @return  mixed           Updated value to match field requirements
     */
    public function bind( $field, $value ) {

        if( adverts_request( $field["name"] ) && is_array( adverts_request( $field["name"] ) ) ) {
            $values = array_map( "trim", adverts_request( $field["name"] ) );
        } else {
            $values = array(
                "name" => isset( $value["name"] ) ? $value["name"] : "",
                "email" => isset( $value["email"] ) ? $value["email"] : ""
            );
        }
        
        return $values;
    }
    
    /**
     * Save function
     * 
     * Do nothing - we do not want to save form data in post meta fields. 
     * 
     * @since 1.0
     * 
     * @param int       $post_id    Post ID
     * @param string    $key        Meta key
     * @param string    $value      Meta value
     * @return void
     */
    public function save( $post_id, $key, $value ) {
        // do nothing
        return;
    }
}