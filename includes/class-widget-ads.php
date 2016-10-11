<?php

/**
 * Recent Ads Widget
 * 
 * Display list of of Ads in a sidebar. This widget has following filters
 * - keyword
 * - location
 * - is_featured (if Featured module enabled)
 * - category
 * - price (min - max)
 * - sort (published, title, price)
 * - order (asc, desc)
 * 
 * @package Adverts
 * @subpackage Widgets
 * @since 1.0.9
 * @access public
 */

class Adverts_Widget_Ads extends WP_Widget
{
    public $defaults = array();
    
    /**
     * Registers widget with WordPress
     * 
     * @see WP_Widget::__construct()
     * 
     * @since 0.3
     */
    public function __construct() {

        $this->defaults = array(
            'title' => __("WPAdverts Ads", "adverts"),
            'count' => 5,
            'keyword' => '',
            'location' => '',
            'is_featured' => 0,
            'category' => array(),
            'price_min' => 0,
            'price_max' => 0,
            'sort' => 'published',
            'order' => 'desc'
        );
        
        
        parent::__construct(
            'wpadverts-widget-ads', 
            __("WPAdverts Ads", "adverts"),
            array(
                "description"=> __("Displays list of recent ads.", "adverts"),
                "classname" => 'wpadverts-widget-recent'
            )
        );
        
    }

    /**
     * Renders widget admin form.
     * 
     * This function is used to display widget configuration in wp-admin / Widgets panel.
     * 
     * @see WP_Widget::form()
     * 
     * @param array $instance From values
     * @since 0.3
     * @return void
     */
    public function form( $instance ) {
        
        $instance = wp_parse_args( (array) $instance, $this->defaults );

        $options = array(
            array(
                "name" => "title",
                "label" => __( "Title" ),
                "type" => "text"
            ),
            array(
                "name" => "count",
                "label" => __( "Count" ),
                "type" => "number",
                "append" => array( "step" => 1, "placeholder" => 5 )
            ),
            array(
                "name" => "keyword",
                "label" => __( "Keyword", "adverts" ),
                "type" => "text"
            ),
            array(
                "name" => "location",
                "label" => __( "Location", "adverts" ),
                "type" => "text"
            ),
            array(
                "name" => "category",
                "label" => __( "Category", "adverts" ),
                "type" => "select",
                "options" => adverts_taxonomies(),
                "append" => array( "multiple" => "multiple" ),
            ),
            array(
                "name" => "price_min",
                "label" => __( "Price Min.", "adverts" ),
                "type" => "number",
                "append" => array( "step" => 1 )
            ),
            array(
                "name" => "price_max",
                "label" => __( "Price Max.", "adverts" ),
                "type" => "number",
                "append" => array( "step" => 1 )
            ),
            array(
                "name" => "sort",
                "label" => __( "Sort By", "adverts" ),
                "type" => "select",
                "options" => array(
                    array(
                        "value" => "published",
                        "depth" => 0,
                        "text" => __( "Publish Date", "adverts" )
                    ),
                    array(
                        "value" => "title",
                        "depth" => 0,
                        "text" => __( "Title" )
                    ),
                    array(
                        "value" => "price",
                        "depth" => 0,
                        "text" => __( "Price", "adverts" )
                    ),
                )
            ),
            array(
                "name" => "order",
                "label" => __( "Order", "adverts" ),
                "type" => "select",
                "options" => array(
                    array(
                        "value" => "desc",
                        "depth" => 0,
                        "text" => __( "Descending", "adverts" )
                    ),
                    array(
                        "value" => "asc",
                        "depth" => 0,
                        "text" => __( "Ascending", "adverts" )
                    ),
                )
            ),
        );
        
        $modules = adverts_config('config.module');
        
        if( isset( $modules["featured"] ) ) {
            $options[] = array(
                "name" => "featured",
                "label" => __( "Show featured Ads only.", "adverts" ),
                "type" => "checkbox",
            );
        }
        
        include_once ADVERTS_PATH . 'includes/class-html.php';
        
        foreach( $options as $option ) {
            
            if( isset( $instance[$option["name"]])) {
                $value = $instance[$option["name"]];
            } else {
                $value = null;
            }
            
            if( in_array($option["type"], array( "text", "number", "range" ) ) ) {
                $this->input_text( $option, $value );
            } elseif( $option["type"] == "select" ) {
                $this->input_select( $option, $value );
            } elseif( $option["type"] == "checkbox" ) {
                $this->input_checkbox( $option, $value );
            }
        }      
                
        
    }

    /**
     * Renders text input 
     * 
     * This function handles rendering simple inputs (<input type="text" />) 
     * in Widget configuration (wp-admin / Appeareance / Widgets panel)
     * 
     * @param array $option     Input options
     * @param type $value       Input value(s)
     * @since 1.0.9
     * @return void
     */
    public function input_text( $option, $value ) 
    {
        $name = $option["name"];
        $params = array(
            "type" => $option["type"],
            "class" => "widefat", 
            "id" => $this->get_field_id($name), 
            "name" => $this->get_field_name($name),
            "value" => $value
        );
        
        if(isset($option["append"])) {
            $params += $option["append"];
        }

        $input = Adverts_Html::build("input", $params);
        $label = Adverts_Html::build("label", array(
            "for" => $this->get_field_id($name)), 
            $option["label"] . ' ' . $input
        );

        echo Adverts_Html::build("p", array(), $label);
    }
    
    /**
     * Renders select input 
     * 
     * This function handles rendering select inputs (<select>...</select>) 
     * in Widget configuration (wp-admin / Appeareance / Widgets panel)
     * 
     * @param array $option     Input options
     * @param type $value       Input value(s)
     * @since 1.0.9
     * @return void
     */
    public function input_select( $option, $value ) 
    {
        $name = $option["name"];
        $params = array(
            "class" => "widefat", 
            "id" => $this->get_field_id($name), 
            "name" => $this->get_field_name($name),
        );

        if(isset($option["append"])) {
            $params += $option["append"];
        }
        
        if( isset( $option["append"]["multiple"] ) ) {
            $params["name"] .= "[]";
        }
        
        $options = "";
        
        foreach( $option["options"] as $opt ) {
            $spacing = str_repeat("&nbsp;", $opt["depth"]);
            $p = array("value"=>$opt["value"]);
            
            if( $opt["value"] == $value || is_array( $value ) && in_array( $opt["value"], $value ) ) {
                $p["selected"] = "selected";
            }
            
            $o = new Adverts_Html("option", $p, $spacing . $opt["text"]);
            $o->forceLongClosing();
            $options .= $o->render();
        }
        
        $input = Adverts_Html::build("select", $params, $options);
        $label = Adverts_Html::build("label", array(
            "for" => $this->get_field_id($name)), 
            $option["label"] . ' ' . $input
        );

        echo Adverts_Html::build("p", array(), $label);
    }
    
    /**
     * Renders single checkbox input 
     * 
     * This function handles rendering checkbox inputs
     * in Widget configuration (wp-admin / Appeareance / Widgets panel)
     * 
     * @param array $option     Input options
     * @param type $value       Input value(s)
     * @since 1.0.9
     * @return void
     */
    public function input_checkbox( $option, $value ) 
    {
        $name = $option["name"];
        $params = array(
            "type" => "checkbox",
            "id" => $this->get_field_id($name), 
            "name" => $this->get_field_name($name),
            "class" => "", 
            "checked" => $value ? 'checked' : null,
            "value" => 1
        );

        if(isset($option["append"])) {
            $params += $option["append"];
        }
        
        $input = Adverts_Html::build("input", $params);
        $label = Adverts_Html::build("label", array(
            "for" => $this->get_field_id($name)), 
            $input . ' ' . $option["label"]
        );

        echo Adverts_Html::build("p", array(), $label);
    }
    
    /**
     * Sanitize widget form values as they are saved.
     * 
     * @see WP_Widget::update()
     * 
     * @param array $new_instance New config values
     * @param type $old_instance Old config values
     * @since 0.3
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = trim( $new_instance['title'] );
        $instance['count'] = intval($new_instance['count']);
        $instance['keyword'] = trim( $new_instance['keyword'] );
        $instance['location'] = trim( $new_instance['location'] );
        $instance['category'] = array_map("intval", $new_instance['category']);
        $instance['price_min'] = intval($new_instance['price_min']);
        $instance['price_max'] = intval($new_instance['price_max']);
        $instance['sort'] = $new_instance['sort'];
        $instance['order'] = $new_instance['order'];
        
        $modules = adverts_config('config.module');
        
        if( isset( $modules["featured"] ) ) {

            if( ! isset( $new_instance["featured"] ) ) {
                $new_instance["featured"] = 0;
            }
            
            $instance["featured"] = intval( $new_instance["featured"] );
        }
        
        return $instance;
    }
    
    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        global $term;
        
        $instance = wp_parse_args( (array) $instance, $this->defaults );
        
        wp_enqueue_style( 'adverts-frontend' );
        wp_enqueue_style( 'adverts-icons' );
        
        $meta = array();
        $taxonomy = array();
        $orderby = array( );
        $sort = strtoupper( $instance["order"] );
        $meta_key = null;
        $menu_order = null;
        $modules = adverts_config('config.module');
        
        if( isset($instance["location"]) && !empty( $instance["location"] ) ) {
            $meta[] = array( 'key'=>'adverts_location', 'value'=>$instance["location"], 'compare'=>'LIKE' );
        }

        if( isset($instance["category"]) && !empty($instance["category"]) ) {
            $taxonomy[] =  array(
                'taxonomy' => 'advert_category',
                'field'    => 'term_id',
                'terms'    => $instance["category"],
            );
        }
        
        if( isset( $modules["featured"] ) ) {
            $orderby = array( 'menu_order' => 'DESC' );
        }
        
        if( isset( $modules["featured"] ) && isset( $instance["featured"] ) && $instance["featured"] == "1" ) {
            $menu_order = 1;
        } 
        
        if( isset( $instance["price_min"]) && $instance["price_min"] > 0 ) {
            $meta[] = array( 
                'key' => 'adverts_price', 
                'value' => $instance["price_min"], 
                'compare' => '>=',
                'type' => 'DECIMAL(12,2)'
            );
        }
        
        if( isset( $instance["price_max"]) && $instance["price_max"] > 0 ) {
            $meta[] = array( 
                'key' => 'adverts_price', 
                'value' => $instance["price_max"], 
                'compare' => '<=',
                'type' => 'DECIMAL(12,2)'
            );
        }
        
        switch( $instance["sort"] ) {
            case "published":   
                $orderby['date'] = $sort; 
                break;
            case "title":       
                $orderby['title'] = $sort; 
                break;
            case "price": 
                $meta_key = 'adverts_price';
                $orderby['meta_value_num'] = $sort; 
                break;
            default:            
                $orderby['date'] = $sort;
                break;
        }
        
        $params = apply_filters( "adverts_widget_list_query", array( 
            'post_type' => 'advert', 
            'post_status' => 'publish',
            'posts_per_page' => $instance["count"], 
            'paged' => 1,
            's' => $instance["keyword"],
            'meta_query' => $meta,
            'tax_query' => $taxonomy,
            'orderby' => $orderby,
            'meta_key' => $meta_key,
            'menu_order' => $menu_order
        ));
        
        $loop = new WP_Query( $params );
        
        extract($args, EXTR_SKIP);
        
        echo $before_widget;
        $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

        if (!empty($title))
          echo $before_title . $title . $after_title;;

          
          
        // WIDGET CODE GOES HERE
        ?>

        <div class="wpjb adverts-widget adverts-widget-categories">
            <div class="adverts-grid adverts-grid-compact">
            <?php if( $loop->have_posts() ): ?>
                <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                <?php $price = adverts_get_the_price( get_the_ID() ) ?>
                <?php $location = esc_html( get_post_meta( get_the_ID(), "adverts_location", true ) ) ?>
                <div class="<?php echo adverts_css_classes( 'adverts-widget-recent', get_the_ID() ) ?>">
                    
                    <div class="advert-widget-recent-item">
                        <span class="adverts-widget-recent-title">
                            <a href="<?php the_permalink() ?>"><?php the_title() ?></a>
                        </span>
                    </div>
                    
                    <?php if( $location ): ?>
                    <div class="adverts-widget-recent-location">
                        <span class=" adverts-icon-location">
                        <?php echo ( $location ) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if( $price ): ?>
                    <span class="advert-widget-recent-price"><?php echo esc_html( $price ) ?></span>
                    <?php endif; ?>
                    
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="adverts-list-empty"><em><?php _e("There are no ads matching your search criteria.", "adverts") ?></em></div>
            <?php endif; ?>
            </div>
        </div>

        <?php wp_reset_query(); ?>
        <?php
        
        
        echo $after_widget;
    }
 
}