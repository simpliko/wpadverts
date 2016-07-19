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

<?php if( $search_bar == "enabled" ): ?>
<div class="adverts-options">
    <form action="<?php echo esc_attr( $action ) ?>" class="adverts-search-form" method="get">
        
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field) ?>
        <?php endforeach; ?>
        
        <?php if( !empty( $fields_visible ) ): ?>
        <div class="adverts-search">
            <?php foreach( $fields_visible as $field ): ?>
            <div class="advert-input <?php esc_attr_e( 'advert-input-type-' . $field['meta']['search_type'] ) ?>">
                <?php call_user_func( adverts_field_get_renderer($field), $field) ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if( !empty( $fields_hidden ) ): ?>
        <div class="adverts-search adverts-search-hidden">
            <?php foreach( $fields_hidden as $field ): ?>
            <div class="advert-input <?php esc_attr_e( 'advert-input-type-' . $field['meta']['search_type'] ) ?>">
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
            
            <!--a href="#" class="adverts-button-small adverts-filter-date"><?php _e("Date") ?> <span class="adverts-icon-sort"></span></a-->
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
<div class="adverts-list">
    <?php if( $loop->have_posts() ): ?>
    <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
    <?php include apply_filters( "adverts_template_load", ADVERTS_PATH . 'templates/list-item.php' ) ?>
    <?php endwhile; ?>
    <?php else: ?>
    <div class="adverts-list-empty"><em><?php _e("There are no ads matching your search criteria.", "adverts") ?></em></div>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
</div>

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