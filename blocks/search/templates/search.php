<?php

/*

OPTIONS:
- INPUT STYLE: unstyled, simple, underline, solid
- INPUT MOOD:  simple, playful, elegant

*/

$form_mood = "none";
$form_mood_arr = array( "none" => "", "simple" => "atw-rounded-lg", "playful" => "atw-rounded-full", "elegant" => "atw-rounded-none") ;

$form_style = "none";
$form_style_arr = array(
    "none" => "",
    "unstyled" => "atw-outline-none atw-box-border",
    "simple" => "atw-outline-none atw-box-border atw-border-gray-300 atw-shadow-sm focus:atw-border-indigo-300 focus:atw-ring focus:atw-ring-indigo-200 focus:atw-ring-opacity-50",
    "underline" => array(),
    "solid" => "atw-outline-none atw-box-border atw-bg-gray-100 atw-border-transparent focus:atw-ring-2 focus:atw-ring-blue-300"
);

$form_padding = "none";
$form_padding_arr = array(
    "none" => "",
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

$form_styles = join( " ", array(
    $atts["form_style"],
    $atts["form_input_padding"],
    $atts["form_input_corners"],
    $atts["form_input_focus"]
) );

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
    
    $(".js-wpa-view-list").click();
});
</script>

<div class="wpadverts-blocks wpadverts-block-search atw-flex atw-flex-col">

    <form action="" method="get" class="wpadverts-form <?php echo $form_styles ?>  atw-block atw-py-0">
        
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>
        <?php endforeach; ?>
        
        
        <div class="atw-flex atw-flex-col md:atw-flex-row">
            
            <div class="md:atw-flex-grow atw--mx-1">
                <?php if( !empty( $fields_visible ) ): ?>
                <div class="atw-flex atw-flex-wrap atw-items-end atw-justify-between atw-py-0 atw-px-0">
                    <?php foreach( $fields_visible as $field ): ?>
                    <?php $width = $this->_get_field_width( $field ) ?>
                    <?php $pr = $pl = ""; ?>
                    <div class="atw-relative atw-items-end atw-box-border atw-pb-3 atw-px-1 <?php echo esc_attr( $width ) ?>">
                        <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                        <span class="atw-block atw-w-full atw-box-border atw-px-2 atw-py-0 atw-pb-1 atw-text-base atw-text-gray-600 adverts-search-input-label"><?php echo esc_html( $field["label"] ) ?></span>
                        <?php endif; ?>
                        <?php $field["class"] = isset( $field["class"] ) ?  $field["class"] : ""; ?>
                        <?php $field["class"] .= " atw-text-base atw-w-full  "; ?>
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
                    <?php $width = $this->_get_field_width( $field ) ?>
                    <div class="atw-relative atw-items-end atw-box-border atw-pb-3 atw-px-1 <?php echo esc_attr( $width ) ?>">
                        <?php if( isset( $field["label"] ) && ! empty( $field["label"] ) ): ?>
                        <span class="atw-block atw-w-full atw-box-border atw-px-2 atw-py-0 atw-pb-1 atw-text-base atw-text-gray-700 adverts-search-input-label"><?php echo esc_html( $field["label"] ) ?></span>
                        <?php endif; ?>
                        <?php $field["class"] = isset( $field["class"] ) ?  $field["class"] : ""; ?>
                        <?php $field["class"] .= " atw-text-base atw-w-full "; ?>
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
                <button id="js-wpa-filter" class="atw-w-full atw-text-base atw-outline-none atw-border-1 atw-border-solid atw-border-gray-300 atw-bg-white hover:atw-bg-blue-700 atw-text-gray-500 atw-font-semibold atw-px-4 atw-rounded-lg">
                    <i class="fas fa-sliders-h atw-text-gray-500 atw-text-base "></i>
                    <span class="md:atw-hidden">Filters</span>
                </button> 
                </div>
                
                <?php endif; ?>
                
                <div class="atw-flex-auto">
                <button class="atw-w-full atw-text-base atw-outline-none atw-border atw-border-solid atw-border-primary-main atw-bg-primary-main hover:atw-bg-blue-700 atw-text-white atw-font-semibold atw-px-4 atw-rounded-lg">
                    <i class="fas fa-search atw-text-white atw-text-base "></i> 
                    <span class="md:atw-hidden">Search</span>
                </button>
                </div>

            </div>
            
        </div>
        
        
    </form>

</div>