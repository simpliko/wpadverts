<?php

/*

OPTIONS:
- INPUT STYLE: unstyled, simple, underline, solid
- INPUT MOOD:  simple, playful, elegant

*/


$form_border = array(
    0 => "wpa-border-none",
    1 => "wpa-border-thin",
    2 => "wpa-border-thick",
    3 => "wpa-border-thick-x2"
);
$form_rounded = array(
    0 => "wpa-rounded-none",
    1 => "wpa-rounded-sm",
    2 => "wpa-rounded",
    3 => "wpa-rounded-md",
    4 => "wpa-rounded-lg",
    5 => "wpa-rounded-xl",
    6 => "wpa-rounded-2xl",
);

$switch_views = $allow_sorting = true;
$sort_current_title = "Publish Date";

$form_styles = join( " ", array(
    isset( $atts["form"]["style"] ) ? $atts["form"]["style"] : "",
    isset( $atts["form"]["shadow"] ) ? $atts["form"]["shadow"] : "",
    isset( $atts["form"]["border"] ) ? $form_border[ $atts["form"]["border"] ] : $form_border[0],
    isset( $atts["form"]["rounded"] ) ? $form_rounded[ $atts["form"]["rounded"] ] : $form_rounded[0],
    "wpa-padding-sm"
) );

//echo "<pre>";print_r($params); print_r( $atts ); echo "</pre>";

if( isset( $atts["primary_button"] ) ) {
    $pb = $atts["primary_button"];
} else {
    $pb = array();
}


$color_text = isset( $pb["color_text"] ) ? $pb["color_text"] : null;
$color_bg = isset( $pb["color_bg"] ) ? $pb["color_bg"] : null;
$color_border = isset( $pb["color_border"] ) ? $pb["color_border"] : null;

$color_text_h = isset( $pb["color_text_h"] ) ? $pb["color_text_h"] : null;
$color_bg_h = isset( $pb["color_bg_h"] ) ? $pb["color_bg_h"] : null;
$color_border_h = isset( $pb["color_border_h"] ) ? $pb["color_border_h"] : null;

$buttons_position = "atw-flex-row";

if( isset( $atts["buttons_pos"] ) ) {
    $buttons_position = $atts["buttons_pos"];
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
.wpadverts-form.wpa-solid select {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
}
  


.wpadverts-blocks.wpadverts-block-search {
<?php wpadverts_print_grays_variables( $atts["form"]["palette"] ) ?>
}

.wpa-btn-primary {
    color: <?php echo $color_text ?>;
    background-color: <?php echo $color_bg ?>;
    border-color: <?php echo $color_border ?>;
    --wpa-btn-shadow-color: <?php echo $color_border ?>;
 }
 .wpa-btn-primary > span > i.fas {
    color: <?php echo $color_text ?>;
 }
 .wpa-btn-primary:hover {
    color: <?php echo $color_text_h ?>;
    background-color: <?php echo $color_bg_h ?>;
    border-color: <?php echo $color_border_h ?>;
    --wpa-btn-shadow-color: <?php echo $color_border_h ?>;
 }
 .wpa-btn-primary:hover > span > i.fas {
    color: <?php echo $color_text_h ?>;
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
});

</script>



<div class="wpadverts-blocks wpadverts-block-search atw-flex atw-flex-col">

    <form action="" method="get" class="wpadverts-form <?php echo $form_styles ?>  atw-block atw-py-0">
        
        <?php foreach($form->get_fields( array( "type" => array( "adverts_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( adverts_field_get_renderer($field), $field, $form ) ?>
        <?php endforeach; ?>
        
        
        <div class="atw-flex atw-flex-col md:<?php echo $buttons_position ?>">
            
            <div class="md:atw-flex-grow md:atw--mx-1">
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
                        <?php $field["class"] .= " atw-text-base atw-w-full atw-max-w-full"; ?>
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
            
            <div class="atw-flex <?php echo ( empty( $fields_hidden ) ? "atw-grid-cols-1" : "atw-grid-cols-2" ) ?> atw-gap-2 atw-pb-3 md:atw-flex-none <?php echo $buttons_position == "atw-flex-row" ? "md:atw-ml-2" : "" ?>">
                 
                <?php if( ! empty( $fields_hidden ) ): ?>
                <div class="atw-flex-auto atw-pr-2">
                <button id="js-wpa-filter" class="atw-w-full atw-text-base atw-outline-none atw-border-1 atw-border-solid atw-border-gray-300 atw-bg-white hover:atw-bg-blue-700 atw-text-gray-500 atw-font-semibold atw-px-4 atw-rounded-lg">
                    <i class="fas fa-sliders-h atw-text-gray-500 atw-text-base "></i>
                    <span class="md:atw-hidden">Filters</span>
                </button> 
                </div>
                
                <?php endif; ?>
                
                <div class="atw-flex-auto">
                <?php echo wpadverts_block_button( array(), $atts["primary_button"] ) ?>
                <!--button class="atw-w-full atw-text-base atw-outline-none atw-border atw-border-solid atw-border-primary-main atw-bg-primary-main hover:atw-bg-blue-700 atw-text-white atw-font-semibold atw-px-4 atw-rounded-lg">
                    <i class="fas fa-search atw-text-white atw-text-base "></i> 
                    <span class="md:atw-hidden">Search</span>
                </button-->
                </div>

            </div>
            
        </div>
        
        
    </form>

</div>