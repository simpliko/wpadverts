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
    private $instance = array();
    
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
            'multi_level' => 0,
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
        
        $instance = wp_parse_args( (array)$instance, $this->defaults );

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
        
        $params = array(
            "type" => "checkbox",
            "class" => "", 
            "id" => $this->get_field_id('multi_level'), 
            "name" => $this->get_field_name('multi_level'),
            "checked" => $instance['multi_level'] ? 'checked' : null,
            "value" => 1
        );
        
        $input = Adverts_Html::build("input", $params);
        $label = Adverts_Html::build("label", array("for" => $this->get_field_id('multi_level')), $input . ' ' . __( "Show multiple category levels", "adverts" ) );
        
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
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['hide_empty'] = intval($new_instance['hide_empty']);
        $instance['show_count'] = intval($new_instance['show_count']);
        $instance['top_only'] = intval($new_instance['top_only']);
        $instance['multi_level'] = intval($new_instance['multi_level']);
        if ( $instance['multi_level'] ) {
            $instance['top_only'] = 0;
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
        $instance = wp_parse_args( (array)$instance, $this->defaults );

        if ( isset( $instance['multi_level'] ) && $instance['multi_level'] ) {
            return $this->widget_multi_level( $args, $instance );
        } else {
            return $this->widget_single_level( $args, $instance );
        }
    }
    
    /**
     * Display additional CSS class for 'current' category
     * 
     * This function checks if user is currently at /advert-category/(.?*)/ page,
     * or on a single advert page set to only the queried category,
     * and if so then it adds an additional CSS class to the current category link.
     * 
     * @since 1.1.3
     * 
     * @param int       $term_id    Taxonomy Term ID
     * @param string    $class      CSS class name to add
     * @param boolean   $echo       True if you want to echo the data false to return as string
     * @return string               Class name or null
     */
    public function is_term( $term_id, $class = "adverts-widget-category-current", $echo = true ) {
        if( ( is_tax( 'advert_category' ) && get_queried_object()->term_id == $term_id ) ||
            ( $this->has_term( $term_id, true ) ) )
        {
            if( $echo ) {
                echo $class;
            } else {
                return $class;
            }
        }
    }

    /**
     * Check if a term is set for the current post.
     * 
     * This function checks if the current post has the queried term set,
     * as the first (or only) term if $single_category is true.
     * 
     * @since 1.2.1
     * 
     * @param int       $term_id    Taxonomy Term ID
     * @param boolean   $single_category    True if the term must be the first term returned
     * @return boolean              Class name or null
     */
    public function has_term( $term_id, $single_category = false ) {
        if( is_singular( 'advert' ) && has_term( $term_id, 'advert_category' ) ) {
            if ( $single_category == false ) {
                return true;
            }
            $terms = wp_get_post_terms( get_the_ID(), 'advert_category' );
            if( is_array( $terms ) && count( $terms ) > 0 && $terms[0]->term_id == $term_id ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Front-end display of widget.
     * 
     * This function is executed when widget is displayed in single-level mode.
     *
     * @since 1.1.4
     * 
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget_single_level($args, $instance)  
    {
        global $term;
        
        $instance = wp_parse_args( (array)$instance, $this->defaults );
        
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
            $home_url = Adverts_Html::build("a", array("href"=>get_permalink( adverts_config( "ads_list_id" ) ) ), __("Go Up (Home)", "adverts"));
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
          echo $before_title . $title . $after_title;

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
                        <span class="adverts-widget-grid-link <?php echo "adverts-icon-".$icon ?> <?php $this->is_term($term_item->term_id) ?>">
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
    
    /**
     * Front-end display of widget alternate view showing multiple category levels.
     *
     * This based on the widget() function and will be called when multi_level is set.
     *
     * @since 1.1.4
     * 
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget_multi_level( $args, $instance )
    {
        global $term;

        $instance = wp_parse_args( (array) $instance, $this->defaults );

        $current = get_term_by( 'slug', $term, 'advert_category' );
        
        if( $current !== false ) {
            $current_id = $current->term_id;
        } else {
            $current_id = false;
        }

        include_once ADVERTS_PATH . '/includes/class-html.php';

        extract( $args, EXTR_SKIP );

        $terms = get_terms( 'advert_category', array( 
            'hide_empty' => (int)$instance['hide_empty'],
            'parent' => 0,
        ) );

        wp_enqueue_style( 'adverts-frontend' );
        wp_enqueue_style( 'adverts-icons' );

        echo $before_widget;
        $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

        if ( ! empty( $title ) ) {
          echo $before_title . $title . $after_title;
        }

        ?>
        <div class="wpjb adverts-widget adverts-widget-categories adverts-widget-multi-level-categories">
            <div class="adverts-grid adverts-grid-compact">
                <?php
                    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ):
                        $this->print_terms_multi_level( $terms, $current_id, $instance, 0 );
                    else:
                ?>
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

    /**
     * Prints multi level categories
     * 
     * This function is run by self::widget_multi_level()
     * 
     * @see self::widget_multi_level()
     * 
     * @param array $terms          Terms to display
     * @param int   $current_id     Current term ID
     * @param array $instance       Widget settings
     * @param int   $level          Category depth
     */
    protected function print_terms_multi_level( $terms, $current_id = false, $instance, $level = 0 )
    {
        foreach ( $terms as $term_item ):
            $default_icon = "folder";
            if ( $current_id && $current_id == $term_item->term_id || $this->has_term( $term_item->term_id, true ) ) {
                $default_icon = "folder-open";
            }
            $icon = adverts_taxonomy_get( "advert_category", $term_item->term_id, "advert_category_icon", $default_icon );
            if ( $icon == "" ) {
                $icon = $default_icon;
            }

            ?>
            <div class="adverts-grid-row">
                <div class="adverts-col-100">
                    <span class="adverts-widget-grid-link <?php echo "adverts-icon-".$icon ?> <?php $this->is_term($term_item->term_id) ?>">
                        <a href="<?php echo esc_attr(get_term_link($term_item)) ?>"><?php esc_html_e($term_item->name) ?></a>
                        <?php if( $instance['show_count'] ): ?>
                        (<?php echo adverts_category_post_count( $term_item ) ?>)
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <?php

            $child_terms = get_terms( 'advert_category', array(
                'hide_empty' => $instance['hide_empty'],
                'parent' => (int)$term_item->term_id,
            ) );

            if ( ! empty( $child_terms ) && ! is_wp_error( $child_terms ) ):
                ?>
                <div class="adverts-multi-level <?php echo 'adverts-multi-level-'.$level ?>">
                <?php $this->print_terms_multi_level( $child_terms, $current_id, $instance, $level+1 ); ?>
                </div>
                <?php
            endif;

        endforeach;
     }
}
