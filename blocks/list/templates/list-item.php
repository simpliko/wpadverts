
<?php $image_id = adverts_get_main_image_id( get_the_ID() ) ?>

<div class="wpa-result-item atw-flex atw-border-solid atw-px-0 atw-border-t atw-border-gray-100 hover:atw-bg-gray-50 atw-relative <?php echo adverts_css_classes( '', get_the_ID() ) ?>">
    <div class="atw-flex atw-flex-none atw-items-center">
        
        <div class="wpa-picture-list wpa-block-list-view-list atw-flex atw-pr-4">
            <div class="atw-flex atw-items-center atw-box-border atw-w-20 atw-h-20 atw-bg-gray-50 atw-border atw-border-solid atw-rounded atw-border-gray-300 ">
            <?php if($image_id): ?>
                <?php $image = get_post( $image_id ) ?>
                <img src="<?php echo esc_attr( adverts_get_main_image( get_the_ID() ) ) ?>" class="atw-w-24 atw-h-24 atw-block atw-rounded-none atw-border-0 atw-shadow-none atw-object-contain" title="<?php echo esc_attr($image->post_excerpt) ?>" alt="<?php echo esc_attr($image->post_content) ?>" />
            <?php else: ?>
                <div class="atw-transform atw-flex-grow atw-text-center atw-rotate-12">
                    <i class="fas fa-image atw-text-6xl atw-text-gray-200 "></i>
                </div>
            <?php endif; ?>
            </div>
        </div>
        
        <div class="wpa-picture-grid wpa-block-list-view-grid atw-hidden atw-flex atw-flex-grow">
            <div class="atw-flex atw-items-center atw-box-border atw-w-full atw-h-48 atw-bg-gray-50 atw-rounded atw-overflow-hidden">
            <?php if($image_id): ?>
                <?php $image = get_post( $image_id ) ?>
                <img src="<?php echo esc_attr( adverts_get_main_image( get_the_ID(), 'large' ) ) ?>" class="atw-w-full atw-h-48 atw-object-contain atw-block atw-rounded-none atw-border-0 atw-shadow-none" title="<?php echo esc_attr($image->post_excerpt) ?>" alt="<?php echo esc_attr($image->post_content) ?>" />
            <?php else: ?>
                <div class="atw-transform atw-flex-grow atw-text-center atw-rotate-12">
                    <i class="fas fa-image atw-text-9xl atw-text-gray-200 "></i>
                </div>
            <?php endif; ?>
            </div>
        </div>
        
    </div>
       
    <div class="wpa-result-details atw-flex atw-flex-grow">
    
        <div class="wpa-detail-left atw-flex atw-flex-col atw-flex-1  ">

            <div class="wpa-block-list-result-title atw-mb-1 atw-leading-snug atw-flex-grow">
                <a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ) ?>" class="atw-inline-block atw-no-underline ">
                    <span class="atw-inline-block atw-max-h-16 atw-text-gray-700 atw-text-lg atw-leading-tight atw-font-semibold "><?php echo esc_html( get_the_title() ) ?></span>
                    <?php do_action( "adverts_list_after_title", get_the_ID() ) ?>
                </a>
            </div>

            <div class="atw-flex atw-flex-none atw-space-x-3 atw-text-base atw-font-medium atw-text-gray-500">

                <?php $location = get_post_meta( get_the_ID(), "adverts_location", true ) ?>
                <?php if( ! empty( $location ) ): ?>
                <div class=""><?php echo esc_html( $location ) ?></div>
                <?php endif; ?>

                <div class="">
                    <span class="atw-hidden md:atw-inline"><?php echo date_i18n( "d/m/Y", get_post_time( 'U', false, get_the_ID() ) ) ?></span>
                    <span class="md:atw-hidden">7d</span>
                </div>

            </div>


        </div>

        
        <div class="wpa-detail-right atw-flex atw-items-center ">
            <?php $price = get_post_meta( get_the_ID(), "adverts_price", true ) ?>
            <?php if( $price ): ?>
            <div class="atw-text-base atw-font-bold atw-text-red-700 -atw-bg-red-500 atw-text-white atw-text-lg "><?php echo esc_html( adverts_get_the_price( get_the_ID(), $price ) ) ?></div>
            <?php elseif( adverts_config( 'empty_price' ) ): ?>
            <div class="md:atw-text-lg atw-font-bold atw-text-red-800"><?php echo esc_html( adverts_empty_price( get_the_ID() ) ) ?></div>
            <?php endif; ?>
        </div>
        
    </div>
    
</div>