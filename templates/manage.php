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
                <span class="adverts-inline-icon adverts-inline-icon-warn adverts-icon-lock" title="<?php _e("Inactive — This Ad is in moderation.", "adverts") ?>"></span>
                <?php endif; ?>

                <?php if($post->post_status == "expired"): ?>
                <span class="adverts-inline-icon adverts-inline-icon-warn adverts-icon-eye-off" title="<?php _e("Inactive — This Ad expired.", "adverts") ?>"></span>
                <?php endif; ?>
                
                <?php do_action("adverts_sh_manage_list_status", $post) ?>
                
                <span class="adverts-manage-info">
                    <?php $price = get_post_meta( get_the_ID(), "adverts_price", true ) ?>
                    <?php if( $price ): ?>
                        <span class="adverts-manage-price"><?php echo adverts_price( $price ) ?></span>
                    <?php endif; ?>
                    
                    <?php $expires = get_post_meta( $post->ID, "_expiration_date", true ) ?>
                    <?php if( $expires ): ?>
                    <span class="adverts-manage-date adverts-icon-history"><abbr title="<?php echo esc_html( sprintf( __( "Expires %s", "adverts" ), date_i18n( get_option("date_format"), $expires ) ) ) ?>"><?php echo esc_html( apply_filters( 'adverts_sh_manage_date', date_i18n( __( 'Y/m/d' ), $expires ), $post ) ) ?></abbr></span>
                    <?php endif; ?>
                    

                </span>
                
            </span>
            
        
        
        </div>
        
        <div class="advert-published adverts-manage-actions-wrap">

            <span class="adverts-manage-actions-left">
                <a href="<?php esc_attr_e(get_the_permalink()) ?>" title="<?php _e("View", "adverts") ?>" class="adverts-manage-action"><span class="adverts-icon-eye"></span><?php _e("View", "adverts") ?></a>
                <a href="<?php esc_attr_e($baseurl . str_replace("%#%", get_the_ID(), $edit_format)) ?>" title="<?php _e("Edit", "adverts") ?>" class="adverts-manage-action"><span class="adverts-icon-pencil-squared"></span><?php _e("Edit", "adverts") ?></a>
                <a href="<?php esc_attr_e(admin_url("admin-ajax.php")) ?>?action=adverts_delete&id=<?php echo get_the_ID() ?>&redirect_to=<?php esc_attr_e( urlencode( $baseurl ) ) ?>&_ajax_nonce=<?php echo wp_create_nonce('adverts-delete') ?>" title="<?php _e("Delete", "adverts") ?>" class="adverts-manage-action adverts-manage-action-delete" data-id="<?php echo get_the_ID() ?>" data-nonce="<?php echo wp_create_nonce('adverts-delete') ?>">
                    <span class="adverts-icon-trash-1"></span><?php _e("Delete", "adverts") ?>
                </a>
                
                <div class="adverts-manage-action adverts-manage-delete-confirm">
                    <span class="adverts-icon-trash-1"></span>
                    <?php _e( "Are you sure?", "adverts" ) ?>
                    <span class="animate-spin adverts-icon-spinner adverts-manage-action-spinner" style="display:none"></span>
                    <a href="#" class="adverts-manage-action-delete-yes"><?php _e( "Yes", "adverts" ) ?></a>
                    <a href="#" class="adverts-manage-action-delete-no"><?php _e( "Cancel", "adverts" ) ?></a>
                </div>
                
                <?php do_action( "adverts_sh_manage_actions_left", $post->ID ) ?>
            </span>
            <span class="adverts-manage-actions-right">
                <?php do_action( "adverts_sh_manage_actions_right", $post->ID ) ?>
                
                <a href="#" class="adverts-manage-action adverts-manage-action-more"><span class="adverts-icon-menu"></span><?php _e("More", "adverts") ?></a>
            </span>
            
            <div class="adverts-manage-actions-more">
                <?php do_action( "adverts_sh_manage_actions_more", $post->ID ) ?>
            </div>
        </div>
        
        <?php do_action( "adverts_sh_manage_actions_after", $post->ID ) ?>
        
    </div>
    
    <?php endwhile; ?>
    <?php else: ?>
    <div class="adverts-grid-row adverts-grid-compact">
        <div class="adverts-grid-col adverts-col">
            <em><?php _e("You do not have any Ads posted yet.", "adverts") ?></em>
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