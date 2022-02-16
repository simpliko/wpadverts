        
<?php $data = wpadverts_block_list_image_grid( get_the_ID(), $atts ) ?>
    
<div class="wpa-picture-grid wpa-block-list-view-grid atw-flex atw-grow">
    <div class="atw-flex atw-items-center atw-box-border atw-w-full atw-bg-gray-50 atw-rounded atw-overflow-hidden <?php echo join( " ", $data->classes ) ?>">
    <?php if($data->image_id): ?>
        <?php $image = get_post( $data->image_id ) ?>
        <img src="<?php echo esc_attr( adverts_get_main_image( get_the_ID(), $data->image_type ) ) ?>" class="atw-w-full atw-max-w-full atw-max-h-full atw-block atw-rounded-none atw-border-0 atw-shadow-none <?php echo join( " ", $data->classes_img ) ?>" title="<?php echo esc_attr($image->post_excerpt) ?>" alt="<?php echo esc_attr($image->post_content) ?>" />
    <?php elseif($data->default_image_url): ?>
        <img src="<?php echo esc_attr( $data->default_image_url ) ?>" class="atw-w-full atw-max-w-full atw-max-h-full atw-block atw-rounded-none atw-border-0 atw-shadow-none" title="<?php echo esc_attr($image->post_excerpt) ?>" alt="<?php echo esc_attr($image->post_content) ?>" />
    <?php else: ?>
        <div class="atw-transform atw-grow atw-text-center atw-rotate-12">
            <i class="fas fa-image atw-text-9xl atw-text-gray-200 "></i>
        </div>
    <?php endif; ?>
    </div>
</div>