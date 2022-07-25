<?php

class Adverts_Block_Details {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "details";
        
        $js_handler = sprintf( "block-%s-%s", $package, $module );
        
        // automatically load dependencies and version
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

        $this->path = dirname(__FILE__);
        
        wp_register_style(
            'wpadverts-blocks-editor-details',
            ADVERTS_URL . '/assets/css/blocks-editor-details.css',
            array( 'wp-edit-blocks' ),
            filemtime( ADVERTS_PATH . '/assets/css/blocks-editor-details.css' )
        );

        wp_register_script(
            $js_handler,
            plugins_url( 'build/index.js', __FILE__ ),
            $asset_file['dependencies'],
            $asset_file['version']
        );

        wp_register_script(
            "wpadverts-block-details",
            ADVERTS_URL . '/assets/js/block-details.js',
            array( 'jquery' ),
            '2.0.0'
        );

        register_block_type_from_metadata(
            dirname( __FILE__ ) . '/src/block.json',
            array(            
                'editor_style' => 'wpadverts-blocks-editor-details',
                'editor_script' => $js_handler,
                'render_callback' => array( $this, "render" ),
                'style' => wpadverts_load_assets_globally() ? 'wpadverts-blocks' : null,
                'script' => wpadverts_load_assets_globally() ? 'wpadverts-block-details' : null
            )
        );

    }
    
    public function render( $atts = array() ) {

        $atts = apply_filters( "wpadverts/block/details/atts", $atts );

        if( ! wpadverts_load_assets_globally() ) {
            wp_enqueue_style( 'wpadverts-blocks' );
            wp_enqueue_script( 'wpadverts-block-details');
        }

        $params = shortcode_atts(array(
            'name' => 'default',
            'post_type' => 'advert'
        ), $atts, 'adverts_details' );

        extract( $params );

        include_once ADVERTS_PATH . '/includes/class-block-templates.php';

        // If user is in Publish -> Preview use the Adverts_Block_Templates::get_id() instead of current page ID.
        if( Adverts_Block_Templates::get_id() !== null ) {
            $post_id = Adverts_Block_Templates::get_id();
        } else {
            $post_id = get_the_ID();
        }
        
        $post_content = get_post( $post_id )->post_content;
        $post_content = wp_kses($post_content, wp_kses_allowed_html( 'post' ) );
        $post_content = apply_filters( "adverts_the_content", $post_content );

        $data_table = apply_filters( "wpadverts/block/details/data-table", array(
            array(
                "label" => __( "Category", "wpadverts" ),
                "icon" => "fas fa-folder",
                "value" => $this->_get_categories_parsed( $post_id )
            ),
            array(
                "label" => __( "Location", "wpadverts" ),
                "icon" => "fas fa-map-marker-alt",
                "value" => $this->_get_location_parsed( $post_id )
            )

        ), $post_id );

        $content_table = apply_filters( "wpadverts/block/details/content-table", array(
            array(
                "label" => __( "Description", "wpadverts" ),
                "icon" => "",
                "value" => $post_content
            )
        ), $post_id );

        $contact_methods = $this->_get_contact_options( $atts, $post_id );
        $contact_options = array( );
        $contact_additional = array();

        foreach( $atts['contact'] as $k => $contact ) {
            if( isset( $contact_methods[ $contact['name'] ] ) ) {
                $contact_options[] = $contact_methods[ $contact['name'] ];
            }
        } 

        // sort here
        $co_count = 0;

        $more_button = $this->_get_more_button();

        $template = dirname( __FILE__ ) . "/templates/single.php";
        ob_start();
        include $template;
        return ob_get_clean();
    }
    
    protected function _get_contact_options( $atts, $post_id ) {

        $contact_options = array();
        $contact_options = apply_filters( "wpadverts/block/details/contact-options", $contact_options, $atts, $post_id );

        return $contact_options;
    }

    protected function _get_more_button() {
        return array(
            "text" => "",
            "html" => "", 
            "icon" => "fas fa-ellipsis-h", 
            "class" => "wpadverts-more",
            "type" => "secondary",
            "options" => array()
        );
    }

    protected function _get_categories_parsed( $post_id ) {
        $advert_category = get_the_terms( $post_id, 'advert_category' );

        if( empty( $advert_category ) ) {
            return false;
        }

        ob_start();
        foreach($advert_category as $c) {
            ?>
                <div><?php echo adverts_get_taxonomy_path( $c, " / " ) ?></div>
            <?php
        }

        return ob_get_clean();
    }

    protected function _get_location_parsed( $post_id ) {
        $location = get_post_meta( $post_id, "adverts_location", true );

        if( empty( $location ) ) {
            return false;
        }

        return apply_filters( "adverts_tpl_single_location", esc_html( $location ), $post_id );
    }
}