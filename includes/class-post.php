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
    
    public static function get_form_data( $post, Adverts_Form $form ) {
        $data = array();
        $meta = get_post_meta( $post->ID, '', false );

        foreach($form->get_fields() as $field) {
            
            $name = $field["name"];
            $save_method = "default";
            
            if( isset( $field["save"]["method"] ) ) {
                $save_method = $field["save"]["method"];
            }
            
            if( $save_method == "taxonomy" ) {
                $data[$name] = self::get_object_term_ids( $post->ID, $field["save"]["taxonomy"] );
            } elseif( in_array( $save_method, array( "meta-single", "meta-multi" ) ) ) {
                $data[$name] = self::get_object_meta_values( $post->ID, $field["save"]["meta"] );
            } elseif( property_exists( "WP_Post", $name ) ) {
                $data[$name] = $post->$name;
            } elseif( taxonomy_exists( $name ) ) {
                $data[$name] = self::get_object_term_ids( $post->ID, $name );
            } elseif( isset( $meta[ $name ] ) ) {
                $data[$name] = self::get_object_meta_values( $post->ID, $name );
            } else {
                $data[ $name ] = "";
            }
        }
        
        return $data;
    }
    
    public static function get_object_meta_values( $post_id, $meta_name ) {
        $meta = get_post_meta( $post_id, $meta_name, false );
        
        if( empty( $meta ) ) {
            return "";
        } else if( count( $meta ) === 1 ) {
            return $meta[0];
        } else {
            return $meta;
        }
    }
    
    public static function get_object_term_ids( $post_id, $taxonomy ) {
        $terms = array();
        $list = wp_get_object_terms( $post_id, $taxonomy );
        foreach( $list as $term ) {
            $terms[] = $term->term_id;
        }
        return $terms;
    }
    
    /**
     * Saves data in DB
     * 
     * @param Adverts_Form $form
     * @param WP_Post $post
     * @param array $defaults
     */
    public static function save(Adverts_Form $form, $post = null, $init = array(), $skip_post = false ) {
        
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
        $custom = array();
   
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
            if(isset($field["save"]) && isset($field["save"]["method"]) && $field["save"]["method"] != "default" ) {
                $custom[] = $field;
            } elseif(property_exists("WP_Post", $field["name"])) {
                $data[$field["name"]] = $field["value"];
            } elseif(taxonomy_exists($field["name"])) {
                $taxo[$field["name"]] = $field["value"];
            } elseif(isset($field["value"])) {
                $meta[$field["name"]] = array("field"=>$field, "value"=>$field["value"]);
            }
        }
        
        if( $post && $skip_post === true ) {
            $post_id = $post->ID;
        } elseif($post && $post->ID > 0) {
            // Post already exists, update only.
            $data["ID"] = $post->ID;
            $post_id = wp_update_post( apply_filters( "adverts_update_post", $data ), true );
        } else {
            // Post does not exist, insert it.
            $data["comment_status"] = "closed";
            $post_id = wp_insert_post( apply_filters( "adverts_insert_post", $data ), true );
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
        
        // Custom saving strategy (since 1.4.4)
        self::_save_custom( $post_id, $custom );
        
        if( self::$_tmp_guid ) {
            // After save tmp_guid filter is no longer needed, remove it.
            self::$_tmp_guid = null;
            remove_filter("post_guid", array(__CLASS__, "tmp_guid"));
        }

        do_action( "adverts_post_save", $form, $post_id );
        
        return $post_id;
    }
    
    /**
     * Handles custom saving strategy
     * 
     * This function is executed when a field has $field["save"] param
     * available.
     * 
     * @since   1.5.0
     * @param   int     $post_id    Post ID
     * @param   array   $fields     Form fields
     */
    protected static function _save_custom( $post_id, $fields ) {
        foreach( $fields as $field ) {
            $s = $field["save"];
            $name = $field["name"];
            if( $field["save"]["method"] == "none" ) {
                // skip saving
            } else if( $s["method"] == "meta-single" ) {
                if( isset( $s["meta"] ) ) {
                    $name = $s["meta"];
                }
                adverts_save_single( $post_id, $name, $field["value"] );
            } else if( $s["method"] == "meta-multi" ) {
                if( isset( $s["meta"] ) ) {
                    $name = $s["meta"];
                }
                adverts_save_multi( $post_id, $name, $field["value"] );
            } else if( $s["method"] == "taxonomy" ) {
                if( isset( $s["taxonomy"] ) ) {
                    $name = $s["taxonomy"];
                }
                wp_set_post_terms( $post_id, $field["value"], $name );
            } else if( $s["method"] == "file" ) {
                adverts_save_files( $post_id, $name, $field, adverts_request( "wpadverts-form-upload-uniqid" ) );
            } else if( $s["method"] == "callback" ) {
                $params = array( "post_id" => $post_id, "field" => $field );
                call_user_func_array( $s["callback"], $params );
            }
        }
    }
    
}
