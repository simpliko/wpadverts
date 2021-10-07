<?php

/*

OPTIONS:
- INPUT STYLE: unstyled, simple, underline, solid
- INPUT MOOD:  simple, playful, elegant

*/

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


echo "<pre>"; print_r($atts);echo "</pre>";
?>
<link href="/wpadverts/wp-content/plugins/wpadverts/assets/css/all.min.css" rel="stylesheet">

<style>
[type=checkbox]:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='gray' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
}
[type=radio]:checked {
 background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='gray' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='4'/%3e%3c/svg%3e");
  }
  
  .wpadverts-form.wpa-solid select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  
  }
  


.wpadverts-blocks {
    --wpa-color-primary-light: 147, 197, 253; /*165, 180, 252;*/
    --wpa-color-primary-main: 29, 78, 216; /*67, 56, 202;*/
    --wpa-color-primary-dark: 30, 58, 138; /*49, 46, 129;*/
}
  
</style>

<script type="text/javascript">
jQuery(function($) {
    $("#js-wpa-sort").on("click", function(e) {
        e.preventDefault();
        $("#js-wpa-sort-options").toggle();
        return false;
    });
    
    $(".js-wpa-view-list").on("click", function(e) {
        e.preventDefault(); 
        
        $(".js-wpa-view-list").addClass("wpa-selected");
        $(".js-wpa-view-grid").removeClass("wpa-selected");
        
        var results = $(".wpa-results");
        results.addClass("wpa-list-view");
        results.removeClass("wpa-grid-view");
        
    });
    $(".js-wpa-view-grid").on("click", function(e) {
        e.preventDefault(); 
        
        $(".js-wpa-view-list").removeClass("wpa-selected");
        $(".js-wpa-view-grid").addClass("wpa-selected");
        
        var results = $(".wpa-results");
        results.removeClass("wpa-list-view");
        results.addClass("wpa-grid-view");
    });
    
});

</script>

<div class="wpadverts-blocks wpadverts-block-list atw-flex atw-flex-col">
    
    <div class="atw-flex atw-flex-col md:atw-flex-row-reverse md:atw-justify-between">
    
        <div class="atw-flex atw-flex-grow atw-pb-3 md:atw-flex-grow-0 md:atw-space-x-4 atw-justify-between">


            <div class="atw-flex atw-flex-none">
                <?php if( $switch_views ): ?>
                <div class="atw-flex atw-align-baseline atw-leading-none atw-space-x-2">
                    <div class="atw-align-baseline">
                        <a href="#" class="js-wpa-view-list <?php echo $display == "wpa-list-view" ? "wpa-selected" : "" ?>"><i class="fas fa-th-list atw-text-gray-400 atw-text-2xl atw-leading-1 atw-align-baseline atw-block atw-transition atw-duration-100 hover:atw-text-blue-500"></i></a>
                    </div>
                    <div class="atw-align-baseline">
                        <a href="#" class="js-wpa-view-grid <?php echo $display == "wpa-grid-view" ? "wpa-selected" : "" ?>"><i class="fas fa-th-large atw-text-gray-400 atw-text-2xl atw-leading-1 atw-align-baseline"></i></a>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <div class="atw-flex atw-flex-none atw-space-x-3">



                <?php if( $allow_sorting ): ?>
                <div class="atw-align-baseline">

                        <span class="atw-text-gray-500 atw-text-sm">Sort By:</span>

                        <span class="atw-relative atw-inline-block atw-text-gray-700 atw-text-sm">
                            <span id="js-wpa-sort">
                                <span><?php echo esc_html( $sort_current_title ) ?></span>
                                <i class="fas fa-chevron-down atw-text-gray-500 atw-text-sm"></i>
                            </span>

                            <div id="js-wpa-sort-options" class="atw-hidden atw-z-50 atw-origin-top-right atw-absolute atw-right-0 atw-mt-3 atw-w-56 atw-rounded-sm atw-shadow-lg atw-bg-white atw-ring-1 atw-ring-black atw-ring-opacity-5 -atw-divide-solid atw-divide-y atw-divide-gray-100 focus:atw-outline-none">
                            <?php foreach( $sort_options as $sort_group): ?>
                                <div class="py-1">
                                    <span class="atw-text-gray-500 atw-block atw-px-4 atw-py-2 atw-text-sm">
                                        <strong><?php echo esc_html( $sort_group["label"] ) ?></strong>
                                    </span>
                                    <?php foreach( $sort_group["items"] as $sort_item_key => $sort_item): ?>
                                        <a href="<?php echo esc_html( add_query_arg( "adverts_sort", $sort_item_key ) ) ?>" class="atw-text-gray-700 atw-block atw-px-4 atw-py-2 atw-text-sm atw-no-underline">
                                            <?php echo esc_html( $sort_item ) ?>
                                            <?php if($adverts_sort==$sort_item_key): ?><span class="adverts-icon-asterisk" style="opacity:0.5"></span><?php endif; ?>
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
        <div class="atw-flex atw-flex-grow atw-pb-3">

            <div class="">
                <span>
                    <strong class="atw-text-gray-700 atw-text-sm md:atw-text-base"><?php echo $loop->found_posts ?></strong> 
                    <span class="atw-text-sm atw-text-gray-500 md:atw-text-base">results found.</span>
                </span>
            </div>

        </div>
        <?php endif; ?>
        
    </div>


    <?php if( $show_results ): ?>
    <div class="wpa-block-list-results wpa-results atw-grid atw-p-0 atw-m-0 <?php echo $display ?>">
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

</div>