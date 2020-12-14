    <div class="<?php echo adverts_css_classes( 'advert-item advert-item-col-'.(int)$columns, get_the_ID() ) ?>">

        <?php $image_id = adverts_get_main_image_id( get_the_ID() ) ?>
        <div class="advert-img">
            <?php if($image_id): ?>
                <?php $image = get_post( $image_id ) ?>
                <img src="<?php echo esc_attr( adverts_get_main_image( get_the_ID() ) ) ?>" class="advert-item-grow" title="<?php echo esc_attr($image->post_excerpt) ?>" alt="<?php echo esc_attr($image->post_content) ?>" />
            <?php endif; ?>
        </div>
     
        <div class="advert-post-title">
            <span title="<?php echo esc_attr( get_the_title() ) ?>" class="advert-link">
                <span class="advert-link-text"><?php echo esc_html( get_the_title() ) ?></span>
                <?php do_action( "adverts_list_after_title", get_the_ID() ) ?>
            </span>
            <a href="<?php the_permalink() ?>" title="<?php echo esc_attr( get_the_title() ) ?>" class="advert-link-wrap"></a>
        </div>

        <div class="advert-published ">

            <span class="advert-date"><?php echo date_i18n( get_option( 'date_format' ), get_post_time( 'U', false, get_the_ID() ) ) ?></span>

            <?php $location = get_post_meta( get_the_ID(), "adverts_location", true ) ?>
            <?php if( ! empty( $location ) ): ?>
            <span class="advert-item-col-1-only advert-location adverts-icon-location"><?php echo esc_html( $location ) ?></span>
            <?php endif; ?>

            <?php $price = get_post_meta( get_the_ID(), "adverts_price", true ) ?>
            <?php if( $price ): ?>
            <div class="advert-price"><?php echo esc_html( adverts_get_the_price( get_the_ID(), $price ) ) ?></div>
            <?php elseif( adverts_config( 'empty_price' ) ): ?>
            <div class="advert-price adverts-price-empty"><?php echo esc_html( adverts_empty_price( get_the_ID() ) ) ?></div>
            <?php endif; ?>
        </div>

    </div>
