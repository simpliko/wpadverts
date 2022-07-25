<?php

class Adverts_Block_List {
    
    public $path = null;
    
    public function __construct() {
        add_action( "init", array( $this, "init" ) );
    }
    
    public function init() {
        
        $package = "wpadverts";
        $module = "list";
        
        $js_handler = sprintf( "block-%s-%s", $package, $module );
        
        // automatically load dependencies and version
        $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');

        $this->path = dirname(__FILE__);
        
        wp_register_style(
            'wpadverts-blocks-editor-list',
            ADVERTS_URL . '/assets/css/blocks-editor-list.css',
            array( 'wp-edit-blocks' ),
            filemtime( ADVERTS_PATH . '/assets/css/blocks-editor-list.css' )
        );

        wp_register_script(
            $js_handler,
            plugins_url( 'build/index.js', __FILE__ ),
            $asset_file['dependencies'],
            $asset_file['version']
        );

        wp_register_script(
            "wpadverts-block-list-and-search",
            ADVERTS_URL . '/assets/js/block-list-and-search.js',
            array( 'jquery' ),
            '2.0.0'
        );

        register_block_type_from_metadata(
            dirname( __FILE__ ) . '/src/block.json',
            array(            
                'editor_style' => 'wpadverts-blocks-editor-list',
                'editor_script' => $js_handler,
                'render_callback' => array( $this, "render" ),
                'style' => wpadverts_load_assets_globally() ? 'wpadverts-blocks' : null,
                'script' => wpadverts_load_assets_globally() ? 'wpadverts-block-list-and-search' : null
            )
        );
    }
    
    public function render( $atts = array() ) {

        $atts = $this->handlePayload( $atts );

        if( ! wpadverts_load_assets_globally() ) {
            wp_enqueue_style( 'wpadverts-blocks' );
            wp_enqueue_script( 'wpadverts-block-list-and-search' );
        }
        
        $params = shortcode_atts(array(
            'name' => 'default',
            'author' => null,
            'redirect_to' => '',
            'search_bar' => adverts_config( 'config.ads_list_default__search_bar' ),
            'show_results' => true,
            'category' => null,
            'columns' => adverts_config( 'config.ads_list_default__columns' ),
            'display' => adverts_config( 'config.ads_list_default__display' ),
            'switch_views' => adverts_config( 'config.ads_list_default__switch_views' ),
            'allow_sorting' => 0,
            'order_by' => 'date-desc',
            'order_by_featured' => '0',
            'paged' => adverts_request("pg", 1),
            'posts_per_page' => adverts_config( 'config.ads_list_default__posts_per_page' ),
            'show_pagination' => true
        ), $atts, 'adverts_list' );

        extract( $params );

        if( isset( $atts["query"] ) && isset( $atts["query"]["author" ] ) ) {
            $author = intval( $atts["query"]["author"] );
        }

        if( is_numeric( $redirect_to ) ) {
            $action = get_permalink( $redirect_to );
        } else {
            $action = $redirect_to;
        }

        $taxonomy = null;
        $meta = array();
        $orderby = array();

        $query = adverts_request("query");
        $location = adverts_request("location");

        if($location) {
            $meta[] = array('key'=>'adverts_location', 'value'=>$location, 'compare'=>'LIKE');
        }

        $taxonomy = $this->_get_tax_query( $atts );


        if($allow_sorting && adverts_request("adverts_sort")) {
            $adverts_sort = adverts_request("adverts_sort");
        } else {
            $adverts_sort = $order_by;
        }

        // options: title, post_date, adverts_price
        $sort_options = apply_filters( "adverts_list_sort_options", array(
            "date" => array(
                "label" => __("Publish Date", "wpadverts"),
                "items" => array(
                    "date-desc" => __("Newest First", "wpadverts"),
                    "date-asc" => __("Oldest First", "wpadverts")
                )
            ),
            "price" => array(
                "label" => __("Price", "wpadverts"),
                "items" => array(
                    "price-asc" => __("Cheapest First", "wpadverts"),
                    "price-desc" => __("Most Expensive First", "wpadverts")
                )
            ),
            "title" => array(
                "label" => __("Title", "wpadverts"),
                "items" => array(
                    "title-asc" => __("From A to Z", "wpadverts"),
                    "title-desc" => __("From Z to A", "wpadverts")
                )
            )
        ) );

        $sarr = explode("-", $adverts_sort);
        $sort_current_text = __("Publish Date", "wpadverts");
        $sort_current_title = sprintf( __( "Sort By: %s - %s", "wpadverts"), __("Publish Date", "wpadverts"), __("Newest First", "wpadverts") );

        if( isset( $sarr[1] ) && isset( $sort_options[$sarr[0]]["items"][$adverts_sort] ) ) {

            $sort_key = $sarr[0];
            $sort_dir = $sarr[1];

            if($sort_dir == "asc") {
                $sort_dir = "ASC";
            } else {
                $sort_dir = "DESC";
            }

            if($sort_key == "title") {
                $orderby["title"] = $sort_dir;
            } elseif($sort_key == "date") {
                $orderby["date"] = $sort_dir;
            } elseif($sort_key == "price") {
                $orderby["adverts_price__orderby"] = $sort_dir;
                $meta["adverts_price__orderby"] = array(
                    'key' => 'adverts_price',
                    'type' => 'NUMERIC',
                    'compare' => 'NUMERIC',
                );
            } else {
                // apply sorting using adverts_list_query filter.
            }

            $sort_current_text = $sort_options[$sort_key]["label"] ;
            $s_descr = $sort_options[$sort_key]["items"][$adverts_sort];
            $sort_current_title = sprintf( __( "Sort By: %s - %s", "wpadverts"), $sort_current_text, $s_descr );
        } else {
            $adverts_sort = $order_by;
            $orderby["date"] = "desc"; 
        }


        $args = apply_filters( "adverts_list_query", array( 
            'author' => $author,
            'post_type' => 'advert', 
            'post_status' => 'publish',
            'posts_per_page' => $posts_per_page, 
            'paged' => $paged,
            's' => $query,
            'meta_query' => $meta,
            'tax_query' => $taxonomy,
            'orderby' => $orderby
        ), $params);

        if( ( $category || isset( $params["tax__advert_category"] ) ) && is_tax( 'advert_category' ) ) {
            $pbase = get_term_link( get_queried_object()->term_id, 'advert_category' );
        } else {
            $pbase = get_the_permalink();
        }

        $loop = new WP_Query( $args );
        $paginate_base = apply_filters( 'adverts_list_pagination_base', $pbase . '%_%' );
        $paginate_format = stripos( $paginate_base, '?' ) ? '&pg=%#%' : '?pg=%#%';

        include_once ADVERTS_PATH . 'includes/class-html.php';
        include_once ADVERTS_PATH . 'includes/class-form.php';

        if( $switch_views && in_array( adverts_request( "display", "" ), array( "grid", "list" ) ) ) {
            $display = adverts_request( "display" );
            add_filter( "adverts_form_load", "adverts_form_search_display_hidden" );
        }

        if( $display == "list" ) {
            $columns = 1;
        }

        if( adverts_request( "reveal_hidden" ) == "1" ) {
            add_filter( "adverts_form_load", "adverts_form_search_reveal_hidden" );
        }

        $form_scheme = apply_filters( "adverts_form_scheme", Adverts::instance()->get("form_search"), $params );

        $form = new Adverts_Form( $form_scheme );
        $form->bind( stripslashes_deep( $_GET ) );

        $fields_hidden = array();
        $fields_visible = array();

        $counter = array(
            "visible-half" => 0,
            "visible-full" => 0,
            "hidden-half" => 0,
            "hidden-full" => 0
        );

        foreach($form->get_fields() as $field) {

            $search_group = "hidden";
            $search_type = "half";

            if( isset( $field['meta']["search_group"] ) ) {
                $search_group = $field['meta']['search_group'];
            }

            if( $search_group == "visible" ) {
                $fields_visible[] = $field;
            } else {
                $fields_hidden[] = $field;
            }
        }
    
        
        $template = dirname( __FILE__ ) . "/templates/list.php";
        ob_start();
        include $template;
        return ob_get_clean();
    }
    
    protected function _get_field_width( $field ) {
        $arr = array(
            "full" => "atw-w-full",
            "half" => "atw-w-full md:atw-w-2/4",
            "third" => "atw-w-full md:atw-w-1/3",
            "fourth" => "atw-w-full md:atw-w-1/4"
        );

        return $arr[ $field['meta']['search_type'] ];
    }

    public function handlePayload( $atts ) {
        if( adverts_request( "payload" ) == "1" ) {
            $request_body = file_get_contents('php://input');
            return json_decode( $request_body, true );
        } else {
            return $atts;
        }
    }

    protected function _get_tax_query( $atts ) {

        $category = null;
        $tax_query = array();

        $taxonomies = get_object_taxonomies( $atts["post_type"] );

        if( ! isset( $atts["query"] ) ) {
            return $tax_query;
        }

        if( isset( $atts["query"]["term_autodetect"] ) && is_tax( $taxonomies ) ) {

            $tax_query[] = array(
                'taxonomy' => get_queried_object()->taxonomy,
                'field'    => 'term_id',
                'terms'    => get_queried_object_id(),
            );

            return $tax_query;
        }

        foreach( $taxonomies as $taxonomy ) {
            
            if( ! isset( $atts["query"][$taxonomy] ) ) {
                continue;
            }

            $terms = array_map( "trim", explode( ",", $atts["query"][$taxonomy] ) );

            if( $this->uses_ids( $terms ) ) {
                $field = "term_id";
            } else {
                $field = "slug";
            }

            if( stripos( $terms[0], "-" ) === 0 ) {
                $operator = "NOT IN";
                $terms[0] = substr( $terms[0], 1 );
            } else {
                $operator = "IN";
            }

            $tax_query[] = array(
                'taxonomy' => $taxonomy,
                'field'    => $field,
                'terms'    => $terms,
                'operator' => $operator
            );
        }

        return $tax_query;
    }

    protected function uses_ids( $arr ) {
        foreach( $arr as $item ) {
            if( ! is_numeric( $item ) ) {
                return false;
            }
        }
        return true;
    }
}