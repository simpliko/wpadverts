<?php
/**
 * Set of static methods that help managing Post (Adverts) data
 *
 * @package Adverts
 * @subpackage Classes
 * @since 0.1
 * @access public
 */

class Adverts_Post {
    
    /**
     * Temporary Post URL
     *
     * @var string 
     */
    private static $_tmp_guid = null;
    
    /**
     * Forces post permalink
     * 
     * Used by 'post_guid' filter only.
     * 
     * @param string $guid
     * @return string
     */
    public static function tmp_guid( $guid ) {
        if( self::$_tmp_guid !== null ) {
            return self::$_tmp_guid;
        } else {
            return $guid;
        }
    }
    
    /**
     * Converts selected WP_Post and its meta fields to flat array.
     * 
     * Flat array is a key => value array where 'value' is always scalar.
     * 
     * @param WP_Post $post (int | WP_Post)
     * @return array
     */
    public static function to_array( $post ) {
        if( is_numeric( $post ) ) {
            $post = get_post( $post );
        }
        
        $result = $post->to_array();
        $meta = get_post_meta( $post->ID, '', true );

        foreach( $meta as $k => $v ) {
            if( count( $v ) == 1 ) {
                $result[$k] = $v[0];
            } else {
                $result[$k] = $v;
            }
        }
        
        return $result;
    }
    
    /**
     * Saves data in DB
     * 
     * @param Adverts_Form $form
     * @param WP_Post $post
     * @param array $defaults
     */
    public static function save(Adverts_Form $form, $post = null, $init = array() ) {
        
        if(is_numeric($post)) {
            $post = get_post($post);
        }

        // Change Post GUID using post_guid filter if the post is already saved in DB
        // (otherwise guid will be set automatically)
        if(isset($init["post"]["ID"]) && isset($init["post"]["guid"]) && $init["post"]["guid"] == "") {
            self::$_tmp_guid = get_post_permalink( $init["post"]["ID"] );
            add_filter("post_guid", array(__CLASS__, "tmp_guid"));
        }
        
        $data = array();
        $meta = array();
        $taxo = array();
        
        // Set default values
        
        if(isset($init["post"]) && is_array($init["post"])) {
            $data = $init["post"];
        }
        
        if(isset($init["meta"]) && is_array($init["meta"])) {
            $meta = $init["meta"];
        }
        
        if(isset($init["taxo"]) && is_array($init["taxo"])) {
            $taxo = $init["taxo"];
        }
        
        // Merge defaults with data from the Adverts_Form
        
        foreach($form->get_fields() as $field) {
            if(property_exists("WP_Post", $field["name"])) {
                $data[$field["name"]] = $field["value"];
            } elseif(taxonomy_exists($field["name"])) {
                $taxo[$field["name"]] = $field["value"];
            } elseif(isset($field["value"])) {
                $meta[$field["name"]] = array("field"=>$field, "value"=>$field["value"]);
            }
        }
        
        if($post && $post->ID > 0) {
            // Post already exists, update only.
            $data["ID"] = $post->ID;
            $post_id = wp_update_post( apply_filters( "adverts_update_post", $data ) );
        } else {
            // Post does not exist, insert it.
            $data["comment_status"] = "closed";
            $post_id = wp_insert_post( apply_filters( "adverts_insert_post", $data ) );
        }
        
        if(is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Save meta data values
        $fields = Adverts::instance()->get("form_field");
        foreach($meta as $key => $data) {
            
            $field = $data["field"];
            $field_type = $field["type"];
            $value = $data["value"];
            
            $callback_save = $fields[$field_type]["callback_save"];
            
            if( is_callable( $callback_save ) ) {
                call_user_func( $callback_save, $post_id, $key, $value );
            }
        }
        
        // Save taxonomies
        foreach($taxo as $key => $tax) {
            wp_set_post_terms($post_id, $tax, $key);
        }
        
        if( self::$_tmp_guid ) {
            // After save tmp_guid filter is no longer needed, remove it.
            self::$_tmp_guid = null;
            remove_filter("post_guid", array(__CLASS__, "tmp_guid"));
        }

        do_action( "adverts_post_save", $form, $post_id );
        
        return $post_id;
    }
}
