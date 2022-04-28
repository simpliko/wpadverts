<?php
/**
 * Block Templates.
 * 
 * Handles blocks rendering on the Custom Post Type and Custom Taxonomy pages.
 * 
 * This class can tell if post type or taxonomy should use a shortcode or a block template
 * and if its a block then should it be a default or a custom one.
 *
 * @package Adverts
 * @subpackage Classes
 * @since 2.0
 * @access public
 */

class Adverts_Block_Templates {

    protected static $_post_id = null;

    /**
     * Set static post ID (used for Ad preview)
     * 
     * @var     int     $id     Post ID
     * @return  void
     */
    public static function set_id( $id ) {
        self::$_post_id = $id;
    }

    /**
     * Unset static Post ID
     * 
     * @return  void
     */
    public static function unset_id() {
        self::$_post_id = null;
    }

    /**
     * Return static Post ID
     * 
     * @return  int     Post ID or NULL
     */
    public static function get_id() {
        return self::$_post_id;
    }

    /**
     * Returns global/default rendering method either "shortcode" or "block".
     * 
     * The rendering method can be customized on the fly using wpadverts_block_templates_global_method filter
     * 
     * @uses wpadverts_block_templates_global_method filter
     * 
     * @since   2.0
     * @return  string
     */
    public static function get_global_method() {
        $grm = get_option( "wpadverts_block_templates_global_method", "shortcode" );
        return apply_filters( "wpadverts_block_templates_global_method", $grm );
    }

    /**
     * Saves the template option for object in the database
     * 
     * @since   2.0
     * @param   string  $type       Either "post" or "taxonomy"
     * @param   string  $name       Post type or taxonomy name
     * @param   string  $method     One of "block", "shortcode", "none", "global" or null
     * @param   int     $template   ID of the template applicable only if $method = block
     * @return  string
     */
    public static function save( $type, $name, $method, $template = 0 ) {
        $option = get_option( "wpadverts_block_templates" );

        if( $option === false ) {
            $option = array();
        }

        if( ! isset( $option[$type] ) ) {
            $option[$type] = array();
        }

        if( ! isset( $option[$type][$name] ) ) {
            $option[$type][$name] = array( "method" => "", "template" => 0 );
        }

        if( ! $method || $method === "global" ) {
            unset( $option[$type][$name] );
        } else {
            $option[$type][$name]["method"] = $method;
        }

        if( $method == "block" ) {
            $option[$type][$name]["template"] = absint( $template );
        }

        if( empty( $option[$type] ) ) {
            unset( $option[$type] );
        }

        if( empty( $option ) ) {
            delete_option( "wpadverts_block_templates" );
        } else {
            update_option( "wpadverts_block_templates", $option );
        }
    }

    /**
     * Loads templates config from the database
     * 
     * @since 2.0
     * @return null
     */
    protected function _load_options() {
        $option = get_option( "wpadverts_block_templates" );

        $options = array(
            "post" => array(
                "advert" => array(
                    "method" => "block",
                    "template" => null
                ),
            ),
            "taxonomy" => array()
        );

        return $option;
    }

    /**
     * Returns rendering mode for object
     * 
     * @since   2.0
     * @param   string      $object         Type of object either "post" or "taxonomy"
     * @param   string      $object_name    Name of the custom post type or taxonomy
     * @param   boolean     $return_global  Return global value or NULL if config not set
     * @return  string                      Rendering method (either block or shortcode )
     */
    protected function _get_method( $object, $object_name, $return_global = true ) {
        $option = $this->_load_options();

        if( $option === false || ! isset( $option[ $object ] ) || ! isset( $option[ $object ][ $object_name ] ) ) {
            if( $return_global ) {
                return self::get_global_method();
            } else {
                return null;
            }
            
        }

        return $option[ $object ][ $object_name ][ "method" ];
    }

    /**
     * Returns template ID for object
     * 
     * Template ID is the id of the post in the wp_posts table that holds the block template
     * that can be used to render the post/taxonomy archive page.
     * 
     * @since   2.0
     * @param   string      $object         Type of object either "post" or "taxonomy"
     * @param   string      $object_name    Name of the custom post type or taxonomy
     * @return  int                         Template ID or 0
     */
    protected function _get_template( $object, $object_name ) {
        $option = $this->_load_options();

        if( $option === false || ! isset( $option[ $object ] ) || ! isset( $option[ $object ][ $object_name ] ) ) {
            return 0;
        }

        if( ! empty( $option[ $object ][ $object_name ][ "template" ] ) ) {
            return $option[ $object ][ $object_name ][ "template" ];
        } else {
            return 0;
        }
        
    }

    /**
     * Returns default post block template
     * 
     * @since   2.0
     * @param   string      $post_type      Post type
     * @return  string                      block template string
     */
    public function get_default_post_template( $post_type ) {
        return sprintf( '<!-- wp:wpadverts/details {"post_type":"%s"} /-->', $post_type );
    }

    /**
     * Returns default taxonomy block template
     * 
     * @since   2.0
     * @param   string      $taxonomy       Taxonomy name
     * @return  string                      block template string
     */
    public function get_default_taxonomy_template( $taxonomy ) {
        $tpl  = sprintf( '<!-- wp:wpadverts/search {"post_type":"advert"} /-->', $taxonomy );
        $tpl .= sprintf( '<!-- wp:wpadverts/list { "post_type": "advert", "query":{"term_autodetect":true} } /-->', $taxonomy );
        return $tpl;
    }

    /**
     * Returns rendering mode for post_type
     * 
     * @since   2.0
     * @param   string      $post_type      Post type
     * @param   boolean     $return_global  Return global value or NULL if config not set
     * @return  string                      Rendering method (either block or shortcode )
     */
    public function get_post_render_method( $post_type, $return_global = true ) {
        return $this->_get_method( "post", $post_type, $return_global );
    }

    /**
     * Returns rendering mode for taxonomy
     * 
     * @since   2.0
     * @param   string      $taxonomy       Taxonomy
     * @param   boolean     $return_global  Return global value or NULL if config not set
     * @return  string                      Rendering method (either block or shortcode )
     */
    public function get_taxonomy_render_method( $taxonomy, $return_global = true ) {
        return $this->_get_method( "taxonomy", $taxonomy, $return_global );
    }

    /**
     * Returns post_type block template ID
     * 
     * @since   2.0
     * @param   string      $post_type      Post type
     * @return  int                         ID of the WP_Post storing the post_type template
     */
    public function get_post_template_id( $post_type ) {
        return absint( $this->_get_template( "post", $post_type ) );
    }

    /**
     * Returns taxonomy block template ID
     * 
     * @since   2.0
     * @param   string      $taxonomy       Taxonomy
     * @return  int                         ID of the WP_Post storing the post_type template
     */
    public function get_taxonomy_template_id( $taxonomy ) {
        return absint( $this->_get_template( "taxonomy", $taxonomy ) );
    }

    /**
     * Returns post_type block template code
     * 
     * @since   2.0
     * @param   string      $post_type      Post type
     * @return  string                      block template string
     */
    public function get_post_template( $post_type ) {

        $template_id = $this->_get_template( "post", $post_type );
    
        if( $template_id < 1 ) {
            return $this->get_default_post_template( $post_type );
        }
    
        $template = get_post( $template_id );
    
        if( $template === null ) {
            return $this->get_default_post_template( $post_type ) . sprintf( '<!-- wpadverts-error: could not load template %d; using default. -->', $template_id );
        } else {
            return $template->post_content;
        }
    }

    /**
     * Returns taxonomy block template code
     * 
     * @since   2.0
     * @param   string      $taxonomy       Taxonomy name
     * @return  string                      block template string
     */
    public function get_taxonomy_template( $taxonomy ) {
        $template_id = $this->_get_template( "taxonomy", $taxonomy );
    
        if( $template_id < 1 ) {
            return $this->get_default_taxonomy_template( $taxonomy );
        }
    
        $template = get_post( $template_id );
    
        if( $template === null ) {
            return $this->get_default_taxonomy_template( $taxonomy ) . sprintf( '<!-- wpadverts-error: could not load template %d; using default. -->', $template_id );
        } else {
            return $template->post_content;
        }
    }
}