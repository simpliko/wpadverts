<div class="adverts-grid">
    <?php if( $loop->have_posts()): ?>
    <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
    <?php global $post ?>
    
    <?php $columns = 1; ?>
    <div class="advert-manage-item <?php echo 'advert-item advert-item-col-'.(int)$columns ?>">

        <?php $image = adverts_get_main_image( get_the_ID() ) ?>
        <div class="advert-img">
            <?php if($image): ?>
                <img src="<?php echo esc_attr($image) ?>" alt="" class="advert-item-grow" />
            <?php endif; ?>
        </div>
     
        <div class="advert-post-title">
            <span class="adverts-manage-link">
                
                <a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ) ?>" class="advert-link-wraps"><strong><?php the_title() ?></strong></a>

                <?php if($post->post_status == "pending"): ?>
                <span class="adverts-inline-icon adverts-inline-icon-warn adverts-icon-lock" title="<?php _e("Inactive — This Ad is in moderation.", "wpadverts") ?>"></span>
                <?php endif; ?>

                <?php if($post->post_status == "expired"): ?>
                <span class="adverts-inline-icon adverts-inline-icon-warn adverts-icon-eye-off" title="<?php _e("Inactive — This Ad expired.", "wpadverts") ?>"></span>
                <?php endif; ?>
                
                <?php do_action("adverts_sh_manage_list_status", $post) ?>
                
                <span class="adverts-manage-info">
                    <?php $price = get_post_meta( get_the_ID(), "adverts_price", true ) ?>
                    <?php if( $price ): ?>
                        <span class="adverts-manage-price"><?php echo adverts_price( $price ) ?></span>
                    <?php endif; ?>
                    
                    <?php $expires = get_post_meta( $post->ID, "_expiration_date", true ) ?>
                    <?php if( $expires ): ?>
                    <span class="adverts-manage-date adverts-icon-history"><abbr title="<?php echo esc_html( sprintf( __( "Expires %s", "wpadverts" ), date_i18n( get_option("date_format"), $expires ) ) ) ?>"><?php echo esc_html( apply_filters( 'adverts_sh_manage_date', date_i18n( __( 'Y/m/d' ), $expires ), $post ) ) ?></abbr></span>
                    <?php endif; ?>
                    

                </span>
                
            </span>
            
        
        
        </div>
        
        <div class="advert-published adverts-manage-actions-wrap">

            <span class="adverts-manage-actions-left">
                
                <?php if( is_post_publicly_viewable( $post->ID ) ): ?>
                <a href="<?php echo esc_attr(get_the_permalink()) ?>" title="<?php _e("View", "wpadverts") ?>" class="adverts-manage-action"><span class="adverts-icon-eye"></span><?php _e("View", "wpadverts") ?></a>
                <?php else: ?>
                <a href="<?php echo esc_attr(add_query_arg( array( 'action' => 'preview', 'preview_id' => $post->ID ))) ?>" class="adverts-manage-action"><span class="adverts-icon-eye"></span><?php _e("Preview", "wpadverts") ?></a>
                <?php endif; ?>
                <a href="<?php echo esc_attr($baseurl . str_replace("%#%", get_the_ID(), $edit_format)) ?>" title="<?php _e("Edit", "wpadverts") ?>" class="adverts-manage-action"><span class="adverts-icon-pencil-squared"></span><?php _e("Edit", "wpadverts") ?></a>
                <a href="<?php echo esc_attr(adverts_ajax_url()) ?>?action=adverts_delete&id=<?php echo get_the_ID() ?>&redirect_to=<?php esc_attr_e( urlencode( $baseurl ) ) ?>&_ajax_nonce=<?php echo wp_create_nonce( sprintf( 'wpadverts-delete-%d', get_the_ID() ) ) ?>" title="<?php _e("Delete", "wpadverts") ?>" class="adverts-manage-action adverts-manage-action-delete" data-id="<?php echo get_the_ID() ?>" data-nonce="<?php echo wp_create_nonce( sprintf( 'wpadverts-delete-%d', get_the_ID() ) ) ?>">
                    <span class="adverts-icon-trash-1"></span><?php _e("Delete", "wpadverts") ?>
                </a>
                
                <div class="adverts-manage-action adverts-manage-delete-confirm">
                    <span class="adverts-icon-trash-1"></span>
                    <?php _e( "Are you sure?", "wpadverts" ) ?>
                    <span class="animate-spin adverts-icon-spinner adverts-manage-action-spinner" style="display:none"></span>
                    <a href="#" class="adverts-manage-action-delete-yes"><?php _e( "Yes", "wpadverts" ) ?></a>
                    <a href="#" class="adverts-manage-action-delete-no"><?php _e( "Cancel", "wpadverts" ) ?></a>
                </div>
                
                <?php do_action( "adverts_sh_manage_actions_left", $post->ID, $baseurl ) ?>
            </span>
            <span class="adverts-manage-actions-right">
                <?php do_action( "adverts_sh_manage_actions_right", $post->ID, $baseurl ) ?>
                
                <a href="#" class="adverts-manage-action adverts-manage-action-more"><span class="adverts-icon-menu"></span><?php _e("More", "wpadverts") ?></a>
            </span>
            
            <div class="adverts-manage-actions-more">
                <?php do_action( "adverts_sh_manage_actions_more", $post->ID, $baseurl ) ?>
            </div>
        </div>
        
        <?php do_action( "adverts_sh_manage_actions_after", $post->ID, $baseurl ) ?>
        
    </div>
    
    <?php endwhile; ?>
    <?php else: ?>
    <div class="adverts-grid-row adverts-grid-compact">
        <div class="adverts-grid-col adverts-col">
            <em><?php _e("You do not have any Ads posted yet.", "wpadverts") ?></em>
        </div>
    </div>
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