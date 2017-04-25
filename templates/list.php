<?php 
/* 
 * included by adverts/includes/shortcodes.php shortcodes_adverts_list() 
 * 
 * @var $loop WP_Query
 * @var $query string
 * @var $location string
 * @var $paged int
 */
?>

<?php do_action( "adverts_sh_list_before", $params ) ?>

<?php if( $search_bar == "enabled" ): ?>
<div class="adverts-options">
    <form action="<?php echo esc_attr( $action ) ?>" class="adverts-search-form" method="get">
        
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field) ?>
        <?php endforeach; ?>
        
        <?php if( !empty( $fields_visible ) ): ?>
        <div class="adverts-search">
            <?php foreach( $fields_visible as $field ): ?>
            <div class="advert-input <?php echo esc_attr( $field['adverts_list_classes'] ) ?>">
                <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                <span class="adverts-search-input-label"><?php echo esc_html( $field["label"] ) ?></span>
                <?php endif; ?>
                <?php call_user_func( adverts_field_get_renderer($field), $field) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if( !empty( $fields_hidden ) ): ?>
        <div class="adverts-search adverts-search-hidden">
            <?php foreach( $fields_hidden as $field ): ?>
            <div class="advert-input <?php echo esc_attr( $field['adverts_list_classes'] ) ?>">
                <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                <span class="adverts-search-input-label"><?php echo esc_html( $field["label"] ) ?></span>
                <?php endif; ?>
                <?php call_user_func( adverts_field_get_renderer($field), $field) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    
        <div class="adverts-options-left adverts-js">
            <?php if($switch_views): ?>
            <a href="<?php echo esc_html( add_query_arg( "display", "grid" ) ) ?>" class="adverts-button-small adverts-switch-view" title="<?php esc_attr_e("Grid", "adverts") ?>"><span class="adverts-square-icon adverts-icon-th-large"></span></a>
            <a href="<?php echo esc_html( add_query_arg( "display", "list" ) ) ?>" class="adverts-button-small adverts-switch-view" title="<?php esc_attr_e("List", "adverts") ?>"><span class="adverts-square-icon adverts-icon-th-list"></span></a>
            <?php endif; ?>
            
            <?php if($allow_sorting): ?>
            <span class="adverts-list-sort-wrap">
                <a href="#" class="adverts-button-small adverts-list-sort-button" title="<?php echo esc_attr( $sort_current_title ) ?>">
                    <span class="adverts-list-sort-label"><?php echo esc_html( $sort_current_text ) ?></span> 
                    <span class="adverts-icon-sort"></span>
                </a>

                <div id="adverts-list-sort-options-wrap" class="adverts-multiselect-holder">
                    <div class="adverts-multiselect-options adverts-list-sort-options">

                        <?php foreach( $sort_options as $sort_group): ?>
                            <span class="adverts-list-sort-option-header">
                                <strong><?php echo esc_html( $sort_group["label"] ) ?></strong>
                            </span>
                            <?php foreach( $sort_group["items"] as $sort_item_key => $sort_item): ?>
                                <a href="<?php echo esc_html( add_query_arg( "adverts_sort", $sort_item_key ) ) ?>" class="adverts-list-sort-option">
                                    <?php echo esc_html( $sort_item ) ?>
                                    <?php if($adverts_sort==$sort_item_key): ?><span class="adverts-icon-asterisk" style="opacity:0.5"></span><?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
   

                    </div>
                </div>
            </span>
            <?php endif; ?>
        </div>

        <div class="adverts-options-right adverts-js">
            <?php if( !empty( $fields_hidden ) ): ?>
            <a href="#" class="adverts-form-filters adverts-button-small"><span><?php _e("Advanced Search", "adverts") ?> <span class="adverts-advanced-search-icon adverts-icon-down-open"></a>
            <?php endif; ?>
            <a href="#" class="adverts-form-submit adverts-button-small"><?php _e("SEARCH", "adverts") ?> <span class="adverts-icon-search"><span></a>
        </div>

        <div class="adverts-options-fallback adverts-no-js">
            <input type="submit" value="<?php _e("Filter Results", "adverts") ?>" />
        </div>
    </form>
</div>
<?php endif; ?>

<?php if( $show_results ): ?>
<div class="adverts-list adverts-bg-hover">
    <?php if( $loop->have_posts() ): ?>
    <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
    <?php include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/list-item.php' ) ?>
    <?php endwhile; ?>
    <?php else: ?>
    <div class="adverts-list-empty"><em><?php _e("There are no ads matching your search criteria.", "adverts") ?></em></div>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
</div>

<?php if( $show_pagination ): ?>
<div class="adverts-pagination">
    <?php echo paginate_links( array(
        'base' => $paginate_base,
	'format' => $paginate_format,
	'current' => max( 1, $paged ),
	'total' => $loop->max_num_pages,
        'prev_next' => false
    ) ); ?>
</div>
<?php endif; ?>

<?php endif; ?>