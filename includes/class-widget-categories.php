<?php

/**
 * Adverts Categories Widget
 * 
 * Display list of categories in the sidebar. The widget is content aware,
 * that is it will show a list of top categories, unless user is already browsing
 * by category in this case it will show categories on the same level
 * 
 * @package Adverts
 * @subpackage Widgets
 * @since 0.3
 * @access public
 */

class Adverts_Widget_Categories extends WP_Widget
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
            'title' => __("Advert Categories", "adverts"),
            'show_count' => 1,
            'hide_empty' => 1,
            'top_only' => 0,
        );
        
        
        parent::__construct(
            'wpadverts-widget-categories', 
            __("Advert Categories", "adverts"),
            array(
                "description"=> __("Displays list of available categories.", "adverts"),
                "classname" => 'wpadverts-widget-categories'
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

        $params = array(
            "type" => "text",
            "class" => "widefat", 
            "id" => $this->get_field_id('title'), 
            "name" => $this->get_field_name('title'),
            "value" => $instance['title']
        );
        
        include_once ADVERTS_PATH . 'includes/class-html.php';
        
        $input = Adverts_Html::build("input", $params);
        $label = Adverts_Html::build("label", array("for" => $this->get_field_id('title')), __("Title") . ' ' . $input);
        
        echo Adverts_Html::build("p", array(), $label);
        
        $buffer = "";
        
        $params = array(
            "type" => "checkbox",
            "class" => "", 
            "id" => $this->get_field_id('hide_empty'), 
            "name" => $this->get_field_name('hide_empty'),
            "checked" => $instance['hide_empty'] ? 'checked' : null,
            "value" => 1
        );
        
        $input = Adverts_Html::build("input", $params);
        $label = Adverts_Html::build("label", array("for" => $this->get_field_id('hide_empty')), $input . ' ' . __("Hide empty", "adverts") );
        
        $buffer .= $label . '<br/>';
        
        $params = array(
            "type" => "checkbox",
            "class" => "", 
            "id" => $this->get_field_id('show_count'), 
            "name" => $this->get_field_name('show_count'),
            "checked" => $instance['show_count'] ? 'checked' : null,
            "value" => 1
        );
        
        $input = Adverts_Html::build("input", $params);
        $label = Adverts_Html::build("label", array("for" => $this->get_field_id('show_count')), $input . ' ' . __("Show post counts") );
        
        $buffer .= $label . '<br/>';
        
        $params = array(
            "type" => "checkbox",
            "class" => "", 
            "id" => $this->get_field_id('top_only'), 
            "name" => $this->get_field_name('top_only'),
            "checked" => $instance['top_only'] ? 'checked' : null,
            "value" => 1
        );
        
        $input = Adverts_Html::build("input", $params);
        $label = Adverts_Html::build("label", array("for" => $this->get_field_id('top_only')), $input . ' ' . __( "Show top categories only", "adverts" ) );
        
        $buffer .= $label . '<br/>';
        
        echo Adverts_Html::build("p", array(), $buffer);
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
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['hide_empty'] = intval($new_instance['hide_empty']);
        $instance['show_count'] = intval($new_instance['show_count']);
        $instance['top_only'] = intval($new_instance['top_only']);
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
    function widget($args, $instance)
    {
        global $term;
        
        $instance = wp_parse_args( (array) $instance, $this->defaults );
        
        $home_url = null;
        $child_of = 0;
        $parent_id = 0;
        
        if( $instance["top_only"] ) {
            $current = false;
        } else {
            $current = get_term_by('slug', $term, 'advert_category');
        }
        
        if( $current !== false ) {
            $child_of = $current->term_id;
            $parent_id = $current->parent;
    
        }

        include_once ADVERTS_PATH . '/includes/class-html.php';
        
        if( $parent_id ) {
            $parent = get_term_by( 'term_id', $parent_id, 'advert_category' );
            $link = get_term_link( $parent );
            $home_url = Adverts_Html::build("a", array("href"=>$link, 'title'=>$parent->name), sprintf(__("Go Up (%s)", "adverts"), $parent->name));
        } else if ( $current !== false ) {
            $home_url = Adverts_Html::build("a", array("href"=>site_url()), __("Go Up (Home)", "adverts"));
        }
        
        extract($args, EXTR_SKIP);

        $terms = get_terms( 'advert_category', array( 
            'hide_empty' => (int)$instance['hide_empty'], 
            'parent' => $child_of 
        ) );
        
        wp_enqueue_style( 'adverts-frontend' );
        wp_enqueue_style( 'adverts-icons' );
        
        echo $before_widget;
        $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

        if (!empty($title))
          echo $before_title . $title . $after_title;;

        // WIDGET CODE GOES HERE
        ?>
        <div class="wpjb adverts-widget adverts-widget-categories">
            <div class="adverts-grid adverts-grid-compact">
                <?php if($home_url): ?>
                <div class="adverts-grid-row">
                    <div class="adverts-col-100">
                        <span class="adverts-widget-grid-link">
                            <em><?php echo $home_url ?></em>
                            <span class="adverts-icon-up-open" style="vertical-align: middle"></span>
                        </span>
                    </div>
                </div>
                <?php endif; ?>
                <?php if(!empty($terms)): ?>
                <?php foreach($terms as $term_item): ?>
                <?php $icon = adverts_taxonomy_get("advert_category", $term_item->term_id, "advert_category_icon", "folder") ?>
                <div class="adverts-grid-row">
                    <div class="adverts-col-100">
                        <span class="adverts-widget-grid-link <?php echo "adverts-icon-".$icon ?>">
                            <a href="<?php esc_attr_e(get_term_link($term_item)) ?>"><?php esc_html_e($term_item->name) ?></a>
                            <?php if($instance['show_count']): ?>
                            (<?php echo adverts_category_post_count( $term_item ) ?>)
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="adverts-grid-row">
                    <div class="adverts-col-100">
                        <span><?php _e("No categories found.", "adverts") ?></span>
                    </div>
                </div>
                <?php endif; ?> 
            </div>
        </div>
        <?php
        
        
        echo $after_widget;
    }
 
}