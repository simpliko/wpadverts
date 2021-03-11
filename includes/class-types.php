<?php

class Adverts_Types {

    protected static $_cpt_defaults = array();
    
    protected static $_taxonomy_defaults = array();
    
    public function __construct() {
        
        add_filter( "adverts_post_type", array( $this, "register_post_type" ), 10, 2 );
        
        add_filter( "adverts_register_taxonomy_post_type", array( $this, "register_taxonomy_post_type" ), 10, 2 );
        add_filter( "adverts_register_taxonomy", array( $this, "register_taxonomy" ), 10, 2 );
        
        // Compatibility with MAL location taxonomy
        add_filter( "wpadverts_mal_register_taxonomy_post_type", array( $this, "register_taxonomy_post_type" ), 10, 2 );
        add_filter( "wpadverts_mal_register_taxonomy", array( $this, "register_taxonomy" ), 10, 2 );
        
    }
    
    public function register_post_type( $args, $post_type ) {

        self::$_cpt_defaults[$post_type] = $args;
        
        $option = get_option( "wpadverts_post_types" );
        
        if( !is_array( $option ) || ! isset( $option[ $post_type ] ) ) {
            return $args;
        }

        if( isset( $args["labels" ] ) ) {
            $labels = array_merge( $args["labels"], $option[ $post_type ]["labels"] );
        } else {
            $labels = array();
        }
        
        $args = array_merge( $args, $option[ $post_type ] );
        $args["labels"] = $labels;
        $args["menu_position"] = absint( $args["menu_position"] );

        return $args;
    }
    
    public function register_taxonomy_post_type( $post_types, $taxonomy ) {
        
        $option = get_option( "wpadverts_taxonomies" );
        
        if( !is_array( $option ) || ! isset( $option[ $taxonomy ] ) ) {
            return $post_types;
        }

        if( isset( $option[ $taxonomy ][ "__connect_to" ] ) ) {
            $post_types = $option[ $taxonomy ][ "__connect_to" ];
        }
        
        return $post_types;
    }
    
    public function register_taxonomy( $args, $taxonomy ) {
        
        self::$_taxonomy_defaults[$taxonomy] = $args;
        
        $option = get_option( "wpadverts_taxonomies" );
        
        if( !is_array( $option ) || ! isset( $option[ $taxonomy ] ) ) {
            return $args;
        }

        if( isset( $args["labels" ] ) ) {
            $labels = array_merge( $args["labels"], $option[ $taxonomy ]["labels"] );
        } else {
            $labels = array();
        }
        
        $args = array_merge( $args, $option[ $taxonomy ] );
        $args["labels"] = $labels;
        
        return $args;
    }
    
    public static function get_cpt_defaults( $post_type ) {
        if( isset( self::$_cpt_defaults[$post_type] ) ) {
            return self::$_cpt_defaults[$post_type];
        } else {
            return null;
        }
    }
    
    public static function get_taxonomy_defaults( $taxonomy ) {
        if( isset( self::$_taxonomy_defaults[$taxonomy] ) ) {
            return self::$_taxonomy_defaults[$taxonomy];
        } else {
            return null;
        }
    }
}
