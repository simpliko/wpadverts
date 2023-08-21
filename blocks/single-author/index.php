<?php

class Adverts_Block_Single_Author {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "single-author";
        
        $js_handler = sprintf( "block-%s-%s", $package, $module );
        
        // automatically load dependencies and version
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

        $this->path = dirname(__FILE__);
        
        /*
        wp_register_style(
            'wpadverts-blocks-editor-single-author',
            ADVERTS_URL . '/assets/css/blocks-editor-single-author.css',
            array( 'wp-edit-blocks' ),
            filemtime( ADVERTS_PATH . '/assets/css/blocks-editor-single-author.css' )
        );
        */

        wp_register_script(
            $js_handler,
            plugins_url( 'build/index.js', __FILE__ ),
            $asset_file['dependencies'],
            $asset_file['version']
        );

        wp_register_script(
            "wpadverts-block-single-author",
            ADVERTS_URL . '/assets/js/block-single-author.js',
            array( 'jquery' ),
            '2.0.4'
        );

        register_block_type_from_metadata(
            dirname( __FILE__ ) . '/src/block.json',
            array(            
                //'editor_style' => 'wpadverts-blocks-editor-single-author',
                'editor_script' => $js_handler,
                'render_callback' => array( $this, "render" ),
                //'style' => wpadverts_load_assets_globally() ? 'wpadverts-blocks' : null,
                //'script' => wpadverts_load_assets_globally() ? 'wpadverts-block-single-data-table' : null
            )
        );

    }
    
    public function render( $atts = array() ) {

        $atts = apply_filters( "wpadverts/block/single-author/atts", $atts );

        if( ! wpadverts_load_assets_globally() ) {
            wp_enqueue_style( 'wpadverts-blocks' );
            wp_enqueue_script( 'wpadverts-block-details');
        }

        $params = shortcode_atts(array(
            'name' => 'default',
            'post_type' => 'advert'
        ), $atts, 'adverts_details' );

        extract( $params );

        $data_secondary = $atts["data_secondary"];
        $avatar_size = $atts["avatar_size"];
        $avatar_radius = $atts["avatar_radius"];

        include_once ADVERTS_PATH . '/includes/class-block-templates.php';

        // If user is in Publish -> Preview use the Adverts_Block_Templates::get_id() instead of current page ID.
        if( Adverts_Block_Templates::get_id() !== null ) {
            $post_id = Adverts_Block_Templates::get_id();
        } else {
            $post_id = get_the_ID();
        }

        $last_seen = $this->get_user_last_seen( $post_id );
        $last_seen_rel = human_time_diff( $last_seen, current_time('timestamp') );
        $last_seen_date = $this->get_foramtted_date( $last_seen );

        $post = get_post( $post_id );

        $published = strtotime( $post->post_date );
        $published_rel = human_time_diff( current_time('timestamp'), $published );
        $published_date = $this->get_foramtted_date( $published );

        $template = sprintf( "%s/templates/%s.php", dirname( __FILE__ ), $atts["layout"]);
        ob_start();
        include $template;
        return ob_get_clean();

    }

    public function get_user_last_seen( $post_id ) {
        $post = get_post( $post_id );
        $last_seen = strtotime( $post->post_modified );

        if( $post->post_author ) {
            $user = get_user_by( "id", $post->post_author );
            $last_tmp = strtotime( $user->user_registered );
            if( $last_tmp > $last_seen ) {
                $last_seen = $last_tmp;
            }

            $last_tmp = get_post_meta( $user->ID, "wc_last_active", true );
            if( $last_tmp > $last_seen ) {
                $last_seen = $last_tmp;
            }
        }

        return $last_seen;
    }

    public function get_foramtted_date( $last_seen ) {
        $date_format = apply_filters( "wpadverts/block/date-format", adverts_config( "block_date_format" ), "single-author" );
        return date_i18n( $date_format, $last_seen );

    }

}