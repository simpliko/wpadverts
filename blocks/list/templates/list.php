<?php

$show_results_counter   = ( isset( $atts['show_results_counter'] ) && $atts['show_results_counter'] ) ? true : false;
$allow_sorting          = ( isset( $atts['allow_sorting'] ) && $atts['allow_sorting'] ) ? true : false;
$switch_views           = ( isset( $atts['switch_views'] ) && $atts['switch_views'] ) ? true : false;
$show_pagination        = ( isset( $atts['show_pagination'] ) && $atts['show_pagination'] ) ? true : false;

if( $switch_views && adverts_request( "display" ) ) {
    $atts['display'] = adverts_request( "display" );
}

$display = ( isset( $atts["display"] ) && $atts["display"] === "grid" ) ? "wpa-grid-view" : "wpa-list-view";

$sort_current_title = "Publish Date";

$show_image_column      = ( isset( $atts['show_image_column'] ) && $atts['show_image_column'] ) ? true : false;
$show_price_column      = ( isset( $atts['show_price_column'] ) && $atts['show_price_column'] ) ? true : false;

$grid_cols_arr = array(
    1 => "atw-grid-cols-1",
    2 => "atw-grid-cols-2",
    3 => "atw-grid-cols-3",
    4 => "atw-grid-cols-4",
    5 => "atw-grid-cols-5",
    6 => "atw-grid-cols-6",
    7 => "atw-grid-cols-7",
    8 => "atw-grid-cols-8",
    9 => "atw-grid-cols-9",
);

$grid_cols_md_arr = array(
    1 => "md:atw-grid-cols-1",
    2 => "md:atw-grid-cols-2",
    3 => "md:atw-grid-cols-3",
    4 => "md:atw-grid-cols-4",
    5 => "md:atw-grid-cols-5",
    6 => "md:atw-grid-cols-6",
    7 => "md:atw-grid-cols-7",
    8 => "md:atw-grid-cols-8",
    9 => "md:atw-grid-cols-9",
);

if( $loop->found_posts === 0 ) {
    $atts["grid_columns_mobile"] = 1;
    $atts["grid_columns"] = 1;
}

$grid_cols = sprintf("%s %s", $grid_cols_arr[ $atts["grid_columns_mobile"] ], $grid_cols_md_arr[ $atts["grid_columns"] ] );

//echo "<pre>"; print_r($atts);print_r($params);echo "</pre>";
?>

<style type="text/css">
    <?php if( $atts["color_title"]): ?>
    .wpa-result-title-text {
        color: <?php echo $atts["color_title"] ?>;
    }
    <?php endif; ?>
    <?php if( $atts["color_price"]): ?>
    .wpa-result-last-text {
        color: <?php echo $atts["color_price"] ?>;
    }
    <?php endif; ?>
    <?php if( $atts["color_bg_featured"]): ?>
    .wpadverts-block-list .advert-is-featured {
        background-color: <?php echo $atts["color_bg_featured"] ?>;
    }
    .wpadverts-block-list .advert-is-featured:hover {
        background-color: <?php echo $atts["color_bg_featured"] ?>;
        filter: brightness(0.975);
    }
    <?php endif; ?>
</style>

<div class="wpadverts-blocks wpadverts-block-list atw-flex atw-flex-col">
    
    <div class="atw-flex atw-flex-col md:atw-flex-row-reverse md:atw-justify-between">
    
        <div class="atw-flex atw-grow atw-pb-3 md:atw-grow-0 md:atw-space-x-4 atw-justify-between atw-items-center">


            <div class="atw-flex atw-flex-none">
                <?php if( $switch_views ): ?>
                <div class="atw-flex atw-align-baseline atw-leading-none atw-space-x-2">
                    <div class="atw-align-baseline">
                        <a href="<?php echo esc_html( add_query_arg( "display", "list" ) ) ?>" class="js-wpa-view-list <?php echo $display == "wpa-list-view" ? "wpa-selected" : "" ?>"><i class="fas fa-th-list atw-text-gray-400 atw-text-2xl md:atw-text-xl atw-leading-1 atw-align-baseline atw-block atw-transition atw-duration-100"></i></a>
                    </div>
                    <div class="atw-align-baseline">
                        <a href="<?php echo esc_html( add_query_arg( "display", "grid" ) ) ?>" class="js-wpa-view-grid <?php echo $display == "wpa-grid-view" ? "wpa-selected" : "" ?>"><i class="fas fa-th-large atw-text-gray-400 atw-text-2xl md:atw-text-xl atw-leading-1 atw-align-baseline"></i></a>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <div class="atw-flex atw-flex-none atw-space-x-3">

                <?php if( $allow_sorting ): ?>
                <div class="">

                        <span class="atw-text-gray-500 atw-text-base"><?php _e("Sort By", "wpadverts") ?>:</span>

                        <span class="atw-relative atw-inline-block atw-text-gray-700 atw-text-base">
                            <span id="js-wpa-sort" class=" atw-cursor-pointer">
                                <span><?php echo esc_html( $sort_current_text ) ?></span>
                                <i class="fas fa-chevron-down atw-text-gray-500 atw-text-base"></i>
                            </span>

                            <div id="js-wpa-sort-options" class="atw-hidden atw-z-50 atw-origin-top-right atw-absolute atw-right-0 atw-mt-3 atw-w-56 atw-rounded-sm atw-shadow-lg atw-bg-white atw-ring-1 atw-ring-black atw-ring-opacity-5 atw-divide-y atw-divide-gray-100 focus:atw-outline-none">
                            <?php foreach( $sort_options as $sort_group): ?>
                                <div class="py-1">
                                    <span class="atw-text-gray-500 atw-block atw-px-4 atw-py-2 atw-text-sm">
                                        <strong><?php echo esc_html( $sort_group["label"] ) ?></strong>
                                    </span>
                                    <?php foreach( $sort_group["items"] as $sort_item_key => $sort_item): ?>
                                        <a href="<?php echo esc_html( add_query_arg( "adverts_sort", $sort_item_key ) ) ?>" class="atw-text-gray-700 atw-block atw-px-4 atw-py-2 atw-text-sm atw-no-underline">
                                            <?php echo esc_html( $sort_item ) ?>
                                            <?php if($adverts_sort==$sort_item_key): ?><i class="fa-solid fa-asterisk atw-pl-2 atw-text-gray-700"></i><?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                            </div>

                        </span>

                </div>
                <?php endif; ?>

            </div>
        </div>

        <?php if( $show_results_counter ): ?>
        <div class="atw-flex atw-grow atw-pb-3">

            <div class="">
                <span>
                    <strong class="atw-text-gray-700 atw-text-base"><?php echo $loop->found_posts ?></strong> 
                    <span class="atw-text-gray-500 atw-text-base">results found.</span>
                </span>
            </div>

        </div>
        <?php endif; ?>
        
    </div>


    <?php if( $show_results ): ?>
    <div class="wpa-block-list-results wpa-results atw-grid atw-p-0 atw-m-0 <?php echo  $grid_cols . " " . $display; ?>">
        <?php if( $loop->have_posts() ): ?>
        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
        <?php include apply_filters( "adverts_template_load", $this->path . '/templates/list-item.php' ) ?>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="atw-flex atw-flex-col  atw-items-center atw-mb-8 atw-p-8 atw-border-b-2 atw-border-t-2 atw-border-solid atw-border-gray-100 ">
            <div class="atw-flex atw-justify-center atw-shadow-inner atw-h-12 atw-w-12 atw-rounded atw-p-4 atw-bg-gray-50 atw-text-center atw-items-center">
                <i class="fas fa-search atw-text-4xl atw-text-center atw-text-gray-500"></i>
            </div>
            <div class="">
                <span class="atw-inline-block atw-w-full atw-text-center atw-text-lg atw-font-bold atw-p-0 atw-pt-2 atw-text-gray-700">No results found.</span>
                <span class="atw-inline-block atw-w-full atw-text-center ate-text-base atw-p-0">There aren't any results matching your search query.</span>
            </div>
        </div>
        <?php endif; ?>
        <?php wp_reset_query(); ?>
    </div>

    <?php if( $show_pagination ): ?>
    <div class="wpadverts-pagination">
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

</div>