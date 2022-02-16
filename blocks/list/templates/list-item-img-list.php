<?php $data = wpadverts_block_list_image_list( get_the_ID(), $atts ) ?>
    
<div class="wpa-picture-list wpa-block-list-view-list atw-flex atw-pr-4 ">
    <div class="atw-flex atw-items-center atw-box-border atw-bg-gray-50 atw-border atw-border-solid atw-rounded atw-border-gray-300 <?php echo join( " ", $data->classes ) ?>">
    <?php if($data->image): ?>
        <?php $image = get_post( $data->image_id ) ?>
        <img src="<?php echo esc_attr( adverts_get_main_image( get_the_ID(), $data->image_type ) ) ?>" class="atw-w-full atw-h-full atw-max-w-full atw-max-h-full atw-block atw-rounded-none atw-border-0 atw-shadow-none <?php echo join( " ", $data->classes_img ) ?>" title="<?php echo esc_attr($image->post_excerpt) ?>" alt="<?php echo esc_attr($image->post_content) ?>" />
    <?php elseif($data->default_image_url): ?>
        <img src="<?php echo esc_attr( $data->default_image_url ) ?>" class="atw-w-full atw-h-full atw-max-w-full atw-max-h-full atw-block atw-rounded-none atw-border-0 atw-shadow-none <?php echo join( " ", $data->classes_img ) ?>" title="" alt="" />
    <?php else: ?>
        <div class="atw-transform atw-grow atw-text-center atw-rotate-12">
            <i class="fas fa-image atw-text-6xl atw-text-gray-200 "></i>
        </div>
    <?php endif; ?>
    </div>
</div>