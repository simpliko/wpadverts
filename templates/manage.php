<div class="adverts-grid">
    <?php if( $loop->have_posts()): ?>
    <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
    <?php global $post ?>
    
    <div class="adverts-grid-row adverts-manage-grid">
        <div class="adverts-grid-col adverts-col-title adverts-col-50">
            <span class="adverts-manage-title">
                <a href="<?php esc_attr_e(get_the_permalink()) ?>">
                    <?php esc_html_e(get_the_title()) ?>
                </a>

                <?php if($post->post_status == "pending"): ?>
                &nbsp;<span class="adverts-inline-icon adverts-icon-lock" title="<?php _e("This Ad is in moderation.", "adverts") ?>"></span>
                <?php endif; ?>

                <?php if($post->post_status == "expired"): ?>
                &nbsp;<span class="adverts-inline-icon adverts-icon-eye-off" title="<?php _e("This Ad expired.", "adverts") ?>"></span>
                <?php endif; ?>
                
                <?php do_action("adverts_sh_manage_list_status", $post) ?>
            </span>
        </div>
        
        <div class="adverts-grid-col adverts-col-date adverts-col-25">
            <span class="adverts-manage-date">
                <?php esc_html_e( date_i18n( get_option("date_format"), get_post_time( 'U' ) ) ) ?>
            </span>
        </div>
        
        <div class="adverts-grid-col adverts-col-actions adverts-col-25 adverts-grid-col-right">
            <a href="<?php esc_attr_e(get_the_permalink()) ?>" title="<?php _e("View Ad", "adverts") ?>" class="adverts-button adverts-button-icon adverts-icon-eye"></a>
            <a href="<?php esc_attr_e($baseurl . str_replace("%#%", get_the_ID(), $edit_format)) ?>" title="<?php _e("Edit", "adverts") ?>"class="adverts-button adverts-button-icon adverts-icon-pencil">&nbsp;</a>
            <a href="<?php esc_attr_e(admin_url("admin-ajax.php")) ?>?action=adverts_delete&id=<?php echo get_the_ID() ?>&redirect_to=<?php esc_attr_e( urlencode( $baseurl ) ) ?>" title="<?php _e("Delete", "adverts") ?>" class="adverts-button adverts-button-icon adverts-icon-trash-1">&nbsp;</a></div>
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