<?php

class Adverts_Taxonomies {
    
    protected $_post_type = null;
    
    protected $_taxonomy = null;
    
    public function __construct( $post_type = 'advert', $taxonomy = '' ) {
        $this->_post_type = $post_type;
        $this->_taxonomy = $taxonomy;
        
        add_action( 'template_redirect', array( $this, 'tax_archive' ) );
        add_action( 'wp', array( $this, 'wp' ) );
    }
   
    public function wp() {
        add_filter( "shortcode_atts_adverts_list", array( $this, "shortcode_atts_adverts_list" ), 10, 3 );
        add_filter( "adverts_list_query", array( $this, "filter_by_taxonomy" ), 9, 2 );
        add_filter( "adverts_tax_shortcode_args", array( $this, "apply_tax_value" ), 9 );
    }
    
    public function shortcode_atts_adverts_list( $out, $pairs, $att ) {
        foreach( $this->get_supported_taxonomies() as $tax_name ) {
            $param = "tax__" . $tax_name;
            $out[$param] = "";
            
            if( isset( $att[$param] ) ) {
                $out[$param] = $att[$param];
            }
        }
        return $out;
    }
    
    function filter_by_taxonomy( $args, $params ) {
        foreach( $this->get_supported_taxonomies() as $tax_name ) {
            $param_name = "tax__" . $tax_name;
            
            if( isset( $params[$param_name] ) && $params[$param_name] ) {
                
                if( ! isset( $args["tax_query"] ) || ! is_array( $args["tax_query"] ) ) {
                    $args["tax_query"] = array();
                }

                $args["tax_query"][] = array(
                    'taxonomy' => $tax_name,
                    'field'    => 'term_id',
                    'terms'    => $params[$param_name],
                );
            }
        }

        return $args;
    }
    
    public function apply_tax_value( $args ) {
        foreach( $this->get_supported_taxonomies() as $tax_name ) {
            if( is_tax( $tax_name ) ) {
                $args[ "tax__" . $tax_name ] = get_queried_object()->term_id;
            }
        }
        return $args;
    }
    
    public function get_supported_taxonomies() {
        return array_keys( get_object_taxonomies( $this->_post_type, 'objects' ) );
    }
    
    public function tax_archive() {
        global $wp_query, $post;

        if( ! is_tax( $this->get_supported_taxonomies() ) || is_feed() ) {
            // Default theme archive for all other taxonomies.
            return;
        }
        
        foreach( $this->get_supported_taxonomies() as $tax_name ) {
            if( is_tax( $tax_name ) && locate_template( sprintf( "taxonomy-%s.php", $tax_name ) ) ) {
                // This taxonomy uses a custom taxonomy template
                return;
            } 
        }

        do_action( "adverts_tax_init" );
        
        $queried_object = get_queried_object();
        $shortcode_args = array( );
        
        // Description handling.
        if ( ! empty( $queried_object->description ) ) { 
            $prefix = '<div class="term-description">' . apply_filters( "adverts_tax_term_description", $queried_object->description ) . '</div>'; 
        } else {
            $prefix = '';
        }

        $shortcode_args = apply_filters( "adverts_tax_shortcode_args", $shortcode_args );
        $shortcode = shortcode_adverts_list( $shortcode_args );
        
        $adverts_list_page = get_post( adverts_config( 'ads_list_id' ) );

        $dummy_post_properties = array(
            'ID'                    => 0,
            'post_status'           => 'publish',
            'post_author'           => $adverts_list_page->post_author,
            'post_parent'           => 0,
            'post_type'             => 'page',
            'post_date'             => $adverts_list_page->post_date,
            'post_date_gmt'         => $adverts_list_page->post_date_gmt,
            'post_modified'         => $adverts_list_page->post_modified,
            'post_modified_gmt'     => $adverts_list_page->post_modified_gmt,
            'post_content'          => apply_filters( "adverts_tax_post_content", $prefix . $shortcode ), 
            'post_title'            => apply_filters( "adverts_tax_post_title", $queried_object->name ),
            'post_excerpt'          => '',
            'post_content_filtered' => '',
            'post_mime_type'        => '',
            'post_password'         => '',
            'post_name'             => $queried_object->slug,
            'guid'                  => '',
            'menu_order'            => 0,
            'pinged'                => '',
            'to_ping'               => '',
            'ping_status'           => '',
            'comment_status'        => 'closed',
            'comment_count'         => 0,
            'filter'                => 'raw',
        );

        $dummy_post_properties = apply_filters( "adverts_tax_post", $dummy_post_properties );
        
        // Set the $post global.
        $post = new WP_Post( (object) $dummy_post_properties ); // @codingStandardsIgnoreLine.

        // Copy the new post global into the main $wp_query.
        $wp_query->post  = $post;
        $wp_query->posts = array( $post );

        // Prevent comments form from appearing.
        $wp_query->post_count    = 1;
        $wp_query->is_404        = false;
        $wp_query->is_page       = true;
        $wp_query->is_single     = true;
        $wp_query->is_archive    = false;
        $wp_query->is_tax        = true;
        $wp_query->max_num_pages = 0;

        // Prepare everything for rendering.
        setup_postdata( $post );
        remove_all_filters( 'the_content' );
        remove_all_filters( 'the_excerpt' );

        add_filter( 'template_include', array( $this, 'template_include' ) );
        
        // Disable comments
        add_filter( 'comments_template', array( $this, 'comments_template' ) );
        add_filter( 'comments_template_query_args', array( $this, 'comments_template_query_args' ) );
    }
    
    public function template_include( $template ) {
        
        $possible_templates = array(
            'page',
            'single',
            'singular',
            'index',
        );
        
        foreach ( $possible_templates as $possible_template ) {
            $path = get_query_template( $possible_template );
            if ( $path ) {
                return $path;
            }
        }

        return $template;
    }
    
    public function comments_template( $theme_template ) {
        return ADVERTS_PATH . "/includes/blank.php";
    }
    
    public function comments_template_query_args( $comment_args ) {
        $comment_args["comment__in"] = array( 0 );
        return $comment_args;
    }

}