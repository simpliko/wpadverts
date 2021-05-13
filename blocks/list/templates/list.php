<?php

/*

OPTIONS:
- INPUT STYLE: unstyled, simple, underline, solid
- INPUT MOOD:  simple, playful, elegant

*/



$form_mood = "simple";
$form_mood_arr = array( "simple" => "atw-rounded-lg", "playful" => "atw-rounded-full", "elegant" => "atw-rounded-none") ;

$form_style = "solid";
$form_style_arr = array(
    "unstyled" => "atw-outline-none atw-box-border",
    "simple" => "atw-outline-none atw-box-border atw-border-gray-300 atw-shadow-sm focus:atw-border-indigo-300 focus:atw-ring focus:atw-ring-indigo-200 focus:atw-ring-opacity-50",
    "underline" => array(),
    "solid" => "atw-outline-none atw-box-border atw-bg-gray-100 atw-border-transparent focus:atw-ring-2 focus:atw-ring-blue-300"
);

$form_padding = "small";
$form_padding_arr = array(
    "small" => "atw-py-3 atw-px-3",
    "medium" => "atw-py-6 atw-px-6"
);

$form_focus = array(
    
);

$cl_form_mood = $form_mood_arr[ $form_mood ];
$cl_form_style = $form_style_arr[ $form_style ];
$cl_form_padding = $form_padding_arr[ $form_padding ];
    
$switch_views = $allow_sorting = true;
$sort_current_title = "Publish Date";

function _get_field_width( $field ) {
    $arr = array(
        "full" => "atw-w-full",
        "half" => "atw-w-full md:atw-w-2/4",
        "third" => "atw-w-full md:atw-w-1/3",
        "fourth" => "atw-w-full md:atw-w-1/4"
    );
    
    return $arr[ $field['meta']['search_type'] ];
}

function adverts_field_checkbox_block( $field, $form = null, $type = "checkbox" ) {
    
    $opts = "";
    $i = 1;
    
    if( !isset( $field["rows"] ) ) {
        $field["rows"] = 1;
    }
    
    if( !isset( $field["value"] ) ) {
        $value = array();
    } elseif( !is_array( $field["value"] ) ) {
        $value = (array)$field["value"];
    } else {
        $value = $field["value"];
    }
    
    if(isset($field["options_callback"]) && !empty($field["options_callback"])) {
        $opt = call_user_func( $field["options_callback"], $field );
    } elseif(isset($field["options"])) {
        $opt = $field["options"];
    } else {
        trigger_error("You need to specify options source for field [{$field['name']}].", E_USER_ERROR);
        $opt = array();
    }

    foreach($opt as $v) {
        
        if( isset( $v["id"] ) ) {
            $id = $v["id"];
        } else {
            $id = $field["name"];
        }
        
        $id = apply_filters( "adverts_form_field_option_id", $id.'_'.$i, $v, $field, $i );

        $checkbox = new Adverts_Html("input", array(
            "type" => $type,
            "name" => $field["name"].'[]',
            "id" => $id,
            "value" => $v["value"],
            "class" => " atw-flex-none atw-self-center",
            "checked" => in_array($v["value"], $value) ? "checked" : null
        ));

        $label = new Adverts_Html("label", array(
            "for" => $id,
            "class" => "atw-flex-1 atw-inline-block atw-py-0 atw-px-2 atw-text-sm atw-text-gray-700 atw-align-baseline atw-truncate atw-cursor-pointer"
        ),  $v["text"]);
        
        if( isset( $field["class"] ) ) {
            $class = $field["class"];
        } else {
            $class = null;
        }
        
        if( isset( $v["depth"] ) ) {
            $depth = $v["depth"];
        } else {
            $depth = 0;
        }

        if( $field["rows"] == 1 ) {
            $padding = str_repeat("&nbsp; &nbsp;", $depth * 2);
        } else {
            $padding = "";
        }
        
        $wrap = new Adverts_Html("div", array(
            "class" => "atw-flex atw-flex-row atw-flex-grow atw-align-baseline",
        ), $padding . $checkbox->render() . $label->render() );
        
        $opts .= $wrap->render();
        
        $i++;
    }
    
    //$field["rows"] = 3;

    $wrap_classes = array();
    if( absint( $field["rows"] ) >= 1 ) {
        $wrap_classes[] = sprintf( "atw-grid atw-grid-cols-%d atw-gap-3", absint( $field["rows"] ) );
    } else {
        $wrap_classes[] = "atw-flex atw-flex-wrap atw-flex-row atw-content-evenly";
    }
    
    echo Adverts_Html::build("div", array("class"=> join( " ", $wrap_classes ) ), $opts);
}

function adverts_field_radio_block( $field, $form = null ) {
    adverts_field_checkbox_block($field, $form, "radio");
}

?>
<link href="/wpadverts/wp-content/plugins/wpadverts/assets/css/all.min.css" rel="stylesheet">

<style>
[type=checkbox]:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='gray' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
}
[type=radio]:checked {
 background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='gray' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='4'/%3e%3c/svg%3e");
  }
  
  .wpadverts-blocks select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
  
  }
</style>

<script type="text/javascript">
jQuery(function($) {
    $("#js-wpa-filter").on("click", function(e) {
        e.preventDefault();
        //$("#js-wpa-filters-wrap").toggle();
        if($("#js-wpa-filters-wrap").is(":visible")) {
            $("#js-wpa-filters-wrap").addClass("atw-hidden");
        } else {
            $("#js-wpa-filters-wrap").removeClass("atw-hidden");
        }
        return false;
    });
    $("#js-wpa-sort").on("click", function(e) {
        e.preventDefault();
        $("#js-wpa-sort-options").toggle();
        return false;
    });
    $(".js-wpa-view-list").on("click", function(e) {
        e.preventDefault(); 
        
        var results = $(".wpa-results");
        results.addClass("wpa-list-view");
        results.removeClass("wpa-grid-view");
        
        // done !
        
        //var alist = $(".wpa-block-list-results");

        //alist.removeClass( "atw-grid-cols-2" );
        //alist.addClass( "atw-grid-cols-1" );
        //alist.removeClass( "atw-gap-x-4" );
        //alist.removeClass( "atw-gap-y-4" );
        
        //var imagel = $(".wpa-block-list-view-list");
        //var imageg = $(".wpa-block-list-view-grid");
        
        //imagel.removeClass("atw-hidden");
        //imageg.addClass("atw-hidden");
        
        //var ritem = $(".wpa-block-list-result-item");
        
        //ritem.removeClass("atw-flex-col");
        //ritem.removeClass("atw-py-0");
        //ritem.removeClass("atw-pb-2");
        //ritem.removeClass("atw-border-none");
        
        //ritem.addClass("atw-py-4");
        
        var rtitle = $(".wpa-block-list-result-title");
        
        rtitle.removeClass("atw-flex-grow");
        rtitle.addClass("atw-flex-none");
        
        var rdetails = $(".wpa-block-list-result-details");
        rdetails.addClass("atw-place-content-center");
        
    });
    $(".js-wpa-view-grid").on("click", function(e) {
        e.preventDefault(); 
        
        var results = $(".wpa-results");
        results.removeClass("wpa-list-view");
        results.addClass("wpa-grid-view");
        
        //var alist = $(".wpa-results");
        
        //alist.addClass( "atw-grid-cols-2" );
        //alist.removeClass( "atw-grid-cols-1" );
        //alist.addClass( "atw-gap-x-4" );
        //alist.addClass( "atw-gap-y-4" );
        
        //var imagel = $(".wpa-block-list-view-list");
        //var imageg = $(".wpa-block-list-view-grid");
        
        //imagel.addClass("atw-hidden");
        //imageg.removeClass("atw-hidden");
        
        //var ritem = $(".wpa-result-item");
        
        //ritem.addClass("atw-flex-col atw-py-0 atw-pb-2 atw-border-none");
        //ritem.removeClass("atw-py-4");
        
        var rtitle = $(".wpa-block-list-result-title");
        
        rtitle.addClass("atw-flex-grow");
        rtitle.removeClass("atw-flex-none");
        
        var rdetails = $(".wpa-block-list-result-details");
        rdetails.removeClass("atw-place-content-center");
    });
    
    $(".js-wpa-view-list").click();
});
</script>

<div class="wpadverts-blocks wpadverts-block-list atw-flex atw-flex-col">

    <form action="" method="get" class="wpadverts-form wpa-form-solid atw-block atw-py-0">
        
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>
        <?php endforeach; ?>
        
        
        <div class="atw-flex atw-flex-col md:atw-flex-row -atw-space-y-1">
            
            <div class="md:atw-flex-grow atw--mx-1">
                <?php if( !empty( $fields_visible ) ): ?>
                <div class="atw-flex atw-flex-wrap atw-items-end atw-justify-between atw-py-0 atw-px-0">
                    <?php foreach( $fields_visible as $field ): ?>
                    <?php $width = _get_field_width( $field ) ?>
                    <?php $pr = $pl = ""; ?>
                    <div class="atw-relative atw-items-end atw-box-border atw-pb-3 atw-px-1 <?php echo esc_attr( $width ) ?>">
                        <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                        <span class="atw-block atw-w-full atw-box-border atw-px-2 atw-py-0 atw-pb-1 atw-text-base atw-text-gray-600 adverts-search-input-label"><?php echo esc_html( $field["label"] ) ?></span>
                        <?php endif; ?>
                        <?php $field["class"] = isset( $field["class"] ) ?  $field["class"] : ""; ?>
                        <?php //$field["class"] .= stripos( $field['adverts_list_classes'], "advert-input-type-half-left" ) !== false ? " -atw-pr-1 " : "" ?>
                        <?php //$field["class"] .= stripos( $field['adverts_list_classes'], "advert-input-type-half-right" ) !== false ? " -atw-pl-1 " : "" ?>
                        <?php $field["class"] .= " atw-text-base atw-w-full $cl_form_style $cl_form_mood $cl_form_padding "; ?>
                        <div class="atw-block atw-w-full">
                            <?php $r = adverts_field_get_renderer($field); ?>
                            <?php $r = function_exists( $r . "_block" ) ? $r . "_block" : $r; ?>
                            <?php call_user_func( $r, $field, $form ) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if( !empty( $fields_hidden ) ): ?>
                <div id="js-wpa-filters-wrap" class="atw-hidden atw-flex atw-flex-wrap atw-items-end atw-justify-between atw-py-0 atw-px-0">
                    <?php foreach( $fields_hidden as $field ): ?>
                    <?php $width = _get_field_width( $field ) ?>
                    <div class="atw-relative atw-items-end atw-box-border atw-pb-3 atw-px-1 <?php echo esc_attr( $width ) ?>">
                        <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                        <span class="atw-block atw-w-full atw-box-border atw-px-2 atw-py-0 atw-pb-1 atw-text-base atw-text-gray-700 adverts-search-input-label"><?php echo esc_html( $field["label"] ) ?></span>
                        <?php endif; ?>
                        <?php $field["class"] = isset( $field["class"] ) ?  $field["class"] : ""; ?>
                        <?php $field["class"] .= " atw-text-base atw-w-full $cl_form_style $cl_form_mood $cl_form_padding "; ?>
                        <div class="atw-block atw-w-full">
                            <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="atw-flex <?php echo ( empty( $fields_hidden ) ? "atw-grid-cols-1" : "atw-grid-cols-2" ) ?> atw-gap-2 atw-pb-3 md:atw-flex-none md:atw-ml-2 ">
                 
                <?php if( ! empty( $fields_hidden ) ): ?>
                <div class="atw-flex-auto atw-pr-2">
                <button id="js-wpa-filter" class="atw-w-full atw-text-base atw-outline-none atw-border-1 atw-border-solid atw-border-gray-300 atw-bg-white hover:atw-bg-blue-700 atw-text-gray-500 atw-font-semibold atw-py-3 atw-px-4 atw-rounded-lg">
                    <i class="fas fa-sliders-h atw-text-gray-500 atw-text-base "></i>
                    <span class="md:atw-hidden">Filters</span>
                </button> 
                </div>
                
                <?php endif; ?>
                
                <div class="atw-flex-auto">
                <button class="atw-w-full atw-text-base atw-outline-none atw-border atw-border-solid atw-border-blue-500 atw-bg-blue-500 hover:atw-bg-blue-700 atw-text-white atw-font-semibold atw-py-3 atw-px-4 atw-rounded-lg">
                    <i class="fas fa-search atw-text-white atw-text-base "></i> 
                    <span class="md:atw-hidden">Search</span>
                </button>
                </div>

            </div>
            
        </div>
        
        
    </form>
    
    <div class="atw-flex atw-flex-col md:atw-flex-row-reverse md:atw-justify-between">
    
        <div class="atw-flex atw-flex-grow atw-pb-3 md:atw-flex-grow-0 md:atw-space-x-4 atw-justify-between">


            <div class="atw-flex atw-flex-none">
                <?php if( $switch_views ): ?>
                <div class="atw-flex atw-align-baseline atw-leading-none atw-space-x-2">
                    <div class="atw-align-baseline">
                        <a href="#" class="js-wpa-view-list"><i class="fas fa-th-list atw-text-gray-500 atw-text-2xl atw-leading-1 atw-align-baseline atw-block atw-transition atw-duration-100 hover:atw-text-blue-500"></i></a>
                    </div>
                    <div class="atw-align-baseline">
                        <a href="#" class="js-wpa-view-grid"><i class="fas fa-th-large atw-text-gray-500 atw-text-2xl atw-leading-1 atw-align-baseline"></i></a>
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


        <div class="atw-flex atw-flex-grow atw-pb-3">

            <div class="">
                <span>
                    <strong class="atw-text-gray-700 atw-text-sm md:atw-text-base"><?php echo $loop->found_posts ?></strong> 
                    <span class="atw-text-sm atw-text-gray-500 md:atw-text-base">results found.</span>
                </span>
            </div>

        </div>
        
    </div>


    <?php if( $show_results ): ?>
    <div class="wpa-block-list-results wpa-results atw-grid atw-p-0 atw-m-0">
        <?php if( $loop->have_posts() ): ?>
        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
        <?php include apply_filters( "adverts_template_load", $this->path . '/templates/list-item.php' ) ?>
        <?php endwhile; ?>
        <?php else: ?>
        <div class="adverts-list-empty"><em><?php _e("There are no ads matching your search criteria.", "wpadverts") ?></em></div>
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