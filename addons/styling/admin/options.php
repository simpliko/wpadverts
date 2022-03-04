<?php
/**
 * Displays Styling Module Options Page
 * 
 * This file is a template for wp-admin / Classifieds / Options / Styling panel. 
 * 
 * It is being loaded by adext_contact_form_page_options function.
 * 
 * @see adext_contact_form_page_options()
 * @since 0.1
 */
?>

<div class="wrap">
    <h2 class="nav-tab-wrapper wpa-styling-tabs">
        <a href="#" class="nav-tab nav-tab-active" data-show-tab="wpa-styling-tab-pb"><?php _e("Primary Button", "wpadverts") ?></a>
        <a href="#" class="nav-tab " data-show-tab="wpa-styling-tab-sb"><?php _e("Secondary Button", "wpadverts") ?></a>
        <a href="#" class="nav-tab " data-show-tab="wpa-styling-tab-form"><?php _e("Forms", "wpadverts") ?></a>
   </h2>

    <?php adverts_admin_flash() ?>

    <?php 

    wp_enqueue_script( 'adverts-admin-styling' );

    wp_enqueue_style( 'wpadverts-blocks' ) ;
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );

    ?>
  
    <style type="text/css">
    .wpa-admin-styling-color-picker .wp-picker-holder {
        position: absolute;
    }
    </style>

    <?php $button = array( "class_wrap" => "wpa-style-primary-button", "name" => "primary_button" ) ?>

    <div class="wpa-styling-tab-pb atw-flex">
        <div class="atw-flex-1 atw-w-1/2">
        
        <div class="<?php echo esc_attr($button["class_wrap"]) ?>">

            <form action="" method="get" data-param="<?php echo esc_attr( $button["name"] ) ?>">

                <h3 class="atw-mt-6 atw-mb-3"><?php _e("Common Settings","wpadverts" ) ?></h3>

                <div class="atw-flex">
                    <div>
                        <div class="atw-block atw-mb-1"><?php _e("Border Radius", "wpadverts" ) ?></div>
                        <select name="border_radius" class="border-radius" data-default-value="<?php echo esc_attr( $pd["border_radius"] ) ?>">
                            <option value="0" data-value="atw-rounded-none" <?php selected( $pb["border_radius"], "0",) ?>><?php _e("None", "wpadverts" ) ?></option>
                            <option value="1" data-value="atw-rounded-sm" <?php selected( $pb["border_radius"], "1",) ?>><?php _e("Extra Small", "wpadverts" ) ?></option>
                            <option value="2" data-value="atw-rounded" <?php selected( $pb["border_radius"], "2",) ?>><?php _e("Small", "wpadverts" ) ?></option>
                            <option value="3" data-value="atw-rounded-md" <?php selected( $pb["border_radius"], "3",) ?>><?php _e("Medium", "wpadverts" ) ?></option>
                            <option value="4" data-value="atw-rounded-lg" <?php selected( $pb["border_radius"], "4",) ?>><?php _e("Large", "wpadverts" ) ?></option>
                            <option value="5" data-value="atw-rounded-xl" <?php selected( $pb["border_radius"], "5",) ?>><?php _e("Extra Large", "wpadverts" ) ?></option>
                            <option value="6" data-value="atw-rounded-full" <?php selected( $pb["border_radius"], "6",) ?>><?php _e("Full", "wpadverts" ) ?></option>
                        </select>
                    </div>

                    <div>
                        <div class="atw-block atw-mb-1"><?php _e("Border Width", "wpadverts" ) ?></div>
                        <select name="border_width" class="border-width" data-default-value="<?php echo esc_attr( $pd["border_width"] ) ?>">
                            <option value="0" data-value="atw-border-0" <?php selected( $pb["border_width"], "0",) ?>><?php _e("None", "wpadverts" ) ?></option>
                            <option value="1" data-value="atw-border" <?php selected( $pb["border_width"], "1",) ?>><?php _e("Thin", "wpadverts" ) ?></option>
                            <option value="2" data-value="atw-border-2" <?php selected( $pb["border_width"], "2",) ?>><?php _e("Thick", "wpadverts" ) ?></option>
                            <option value="3" data-value="atw-border-4" <?php selected( $pb["border_width"], "3",) ?>><?php _e("Extra Thick", "wpadverts" ) ?></option>
                        </select>
                    </div>

                    <div>
                        <div class="atw-block atw-mb-1"><?php _e("Font Weight", "wpadverts" ) ?></div>
                        <select name="font_weight" class="font-weight" data-default-value="<?php echo esc_attr( $pd["font_weight"] ) ?>">
                            <option value="atw-font-thin" <?php selected( $pb["font_weight"], "atw-font-thin",) ?>><?php _e("Thin", "wpadverts" ) ?></option>
                            <option value="atw-font-normal" <?php selected( $pb["font_weight"], "atw-font-normal",) ?>><?php _e("Normal", "wpadverts" ) ?></option>
                            <option value="atw-font-semibold" <?php selected( $pb["font_weight"], "atw-font-semibold",) ?>><?php _e("Semi Bold", "wpadverts" ) ?></option>
                            <option value="atw-font-bold" <?php selected( $pb["font_weight"], "atw-font-bold",) ?>><?php _e("Bold", "wpadverts" ) ?></option>
                        </select>
                    </div>

                </div>

                <div class="wpa-colors-normal">
                    <h3 class="atw-mt-6 atw-mb-3"><?php _e("Colors Normal", "wpadverts" ) ?></h3>

                    <div class="atw-flex wpa-admin-styling-color-picker">
                        <div>
                            <div class="atw-block atw-mb-1"><?php _e("Text", "wpadverts" ) ?></div>
                            <input type="text" name="color_text" value="<?php echo esc_attr( $pb["color_text"] ) ?>" class="color-text" data-default-color="<?php echo esc_attr( $pd["color_text"] ) ?>" />

                        </div>

                        <div>
                            <div class="atw-block atw-mb-1"><?php _e("Background", "wpadverts" ) ?></div>
                            <input type="text" name="color_bg" value="<?php echo esc_attr( $pb["color_bg"] ) ?>" class="color-bg" data-default-color="<?php echo esc_attr( $pd["color_bg"] ) ?>" />

                        </div>

                        <div>
                            <div class="atw-block atw-mb-1"><?php _e("Border", "wpadverts" ) ?></div>
                            <input type="text" name="color_border" value="<?php echo esc_attr( $pb["color_border"] ) ?>" class="color-border" data-default-color="<?php echo esc_attr( $pd["color_border"] ) ?>" />

                        </div>  
                    </div>
                    
                </div>          
                
                <div class="wpa-colors-hover">

                    <h3 class="atw-mt-6 atw-mb-3"><?php _e("Colors Hover", "wpadverts" ) ?></h3>

                    <div class="atw-flex wpa-admin-styling-color-picker">
                        <div>
                            <div class="atw-block atw-mb-1"><?php _e("Text", "wpadverts" ) ?></div>
                            <input type="text" name="color_text_h" value="<?php echo esc_attr( $pb["color_text_h"] ) ?>" class="color-text" data-default-color="<?php echo esc_attr( $pd["color_text_h"] ) ?>" />

                        </div>

                        <div>
                            <div class="atw-block atw-mb-1"><?php _e("Background", "wpadverts" ) ?></div>
                            <input type="text" name="color_bg_h" value="<?php echo esc_attr( $pb["color_bg_h"] ) ?>" class="color-bg" data-default-color="<?php echo esc_attr( $pd["color_bg_h"] ) ?>" />

                        </div>

                        <div>
                            <div class="atw-block atw-mb-1"><?php _e("Border", "wpadverts" ) ?></div>
                            <input type="text" name="color_border_h" value="<?php echo esc_attr( $pb["color_border_h"] ) ?>" class="color-border" data-default-color="<?php echo esc_attr( $pd["color_border_h"] ) ?>" />

                        </div>
                    </div>

                </div>

                <p class="submit">
                    <input type="submit" value="<?php echo __("Save Primary Button Settings", "wpadverts") ?>" class="button-primary wpa-save-primary-button" name="Submit"/>
                    <a href="#" class="wpa-button-settings-reset atw-mx-2"><?php _e("Reset to defaults", "wpadverts" ) ?></a>
                    <span class="spinner atw-float-none atw-my-0"></span>
                </p>
            </form>
        </div>

        </div>


        <div class="atw-flex atw-flex-1 atw-flex-col atw-justify-center atw-w-full atw-h-auto atw-border-0 atw-border-solid atw-border-gray-500 atw-bg-white">
            
            <div class="atw-flex atw-place-content-evenly atw-pb-9">
                <span class="atw-text-2xl">Button Preview</span>
            </div>

            <div class="atw-flex atw-place-content-evenly">

                <button class="wpa-preview-button-primary wpa-preview-button-primary-normal atw-inline-block hover:atw-bg-none atw-bg-none atw-text-white atw-w-auto atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-4 atw-py-2 <?php //echo "$border_radius $border_width $leading" ?>">
                    <span class=""><?php _e("Primary Button", "wpadverts" ) ?></span>
                </button>

                <button class="wpa-preview-button-primary wpa-preview-button-primary-hover atw-inline-block atw-bg-none atw-w-auto atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-4 atw-py-2 <?php //echo "$border_radius $border_width $leading" ?>">
                    <span class=""><?php _e("Primary Button Hover", "wpadverts" ) ?></span>
                </button>
                
            </div>

        </div>


    </div>    
    
    <?php $button = array( "class_wrap" => "wpa-style-secondary-button", "name" => "secondary_button" ) ?>

    <div class="wpa-styling-tab-sb atw-flex" style="display:none">
        
        <div class="atw-flex-1 atw-w-1/2">
        
            <div class="<?php echo esc_attr($button["class_wrap"]) ?>">
                <form action="" method="get" data-param="<?php echo esc_attr( $button["name"] ) ?>">

                    <h3 class="atw-mt-6 atw-mb-3"><?php _e("Common Settings","wpadverts" ) ?></h3>

                    <div class="atw-flex">
                        <div>
                            <div class="atw-block atw-mb-1"><?php _e("Border Radius", "wpadverts" ) ?></div>
                            <select name="border_radius" class="border-radius" data-default-value="<?php echo esc_attr( $sd["border_radius"] ) ?>">
                                <option value="0" data-value="atw-rounded-none" <?php selected( $sb["border_radius"], "0",) ?>><?php _e("None", "wpadverts" ) ?></option>
                                <option value="1" data-value="atw-rounded-sm" <?php selected( $sb["border_radius"], "1",) ?>><?php _e("Extra Small", "wpadverts" ) ?></option>
                                <option value="2" data-value="atw-rounded" <?php selected( $sb["border_radius"], "2",) ?>><?php _e("Small", "wpadverts" ) ?></option>
                                <option value="3" data-value="atw-rounded-md" <?php selected( $sb["border_radius"], "3",) ?>><?php _e("Medium", "wpadverts" ) ?></option>
                                <option value="4" data-value="atw-rounded-lg" <?php selected( $sb["border_radius"], "4",) ?>><?php _e("Large", "wpadverts" ) ?></option>
                                <option value="5" data-value="atw-rounded-xl" <?php selected( $sb["border_radius"], "5",) ?>><?php _e("Extra Large", "wpadverts" ) ?></option>
                                <option value="6" data-value="atw-rounded-full" <?php selected( $sb["border_radius"], "6",) ?>><?php _e("Full", "wpadverts" ) ?></option>
                            </select>
                        </div>

                        <div>
                            <div class="atw-block atw-mb-1"><?php _e("Border Width", "wpadverts" ) ?></div>
                            <select name="border_width" class="border-width" data-default-value="<?php echo esc_attr( $sd["border_width"] ) ?>">
                                <option value="0" data-value="atw-border-0" <?php selected( $sb["border_width"], "0",) ?>><?php _e("None", "wpadverts" ) ?></option>
                                <option value="1" data-value="atw-border" <?php selected( $sb["border_width"], "1",) ?>><?php _e("Thin", "wpadverts" ) ?></option>
                                <option value="2" data-value="atw-border-2" <?php selected( $sb["border_width"], "2",) ?>><?php _e("Thick", "wpadverts" ) ?></option>
                                <option value="3" data-value="atw-border-4" <?php selected( $sb["border_width"], "3",) ?>><?php _e("Extra Thick", "wpadverts" ) ?></option>
                            </select>
                        </div>

                        <div>
                            <div class="atw-block atw-mb-1"><?php _e("Font Weight", "wpadverts" ) ?></div>
                            <select name="font_weight" class="font-weight" data-default-value="<?php echo esc_attr( $sd["font_weight"] ) ?>">
                            <option value="atw-font-thin" <?php selected( $sb["font_weight"], "atw-font-thin",) ?>><?php _e("Thin", "wpadverts" ) ?></option>
                                <option value="atw-font-normal" <?php selected( $sb["font_weight"], "atw-font-normal",) ?>><?php _e("Normal", "wpadverts" ) ?></option>
                                <option value="atw-font-semibold" <?php selected( $sb["font_weight"], "atw-font-semibold",) ?>><?php _e("Semi Bold", "wpadverts" ) ?></option>
                                <option value="atw-font-bold" <?php selected( $sb["font_weight"], "atw-font-bold",) ?>><?php _e("Bold", "wpadverts" ) ?></option>
                            </select>
                        </div>

                    </div>

                    <div class="wpa-colors-normal">
                        <h3 class="atw-mt-6 atw-mb-3"><?php _e("Colors Normal", "wpadverts" ) ?></h3>

                        <div class="atw-flex wpa-admin-styling-color-picker">
                            <div>
                                <div class="atw-block atw-mb-1"><?php _e("Text", "wpadverts" ) ?></div>
                                <input type="text" name="color_text" value="<?php echo esc_attr( $sb["color_text"] ) ?>" class="color-text" data-default-color="<?php echo esc_attr( $sd["color_text"] ) ?>" />

                            </div>

                            <div>
                                <div class="atw-block atw-mb-1"><?php _e("Background", "wpadverts" ) ?></div>
                                <input type="text" name="color_bg" value="<?php echo esc_attr( $sb["color_bg"] ) ?>" class="color-bg" data-default-color="<?php echo esc_attr( $sd["color_bg"] ) ?>" />

                            </div>

                            <div>
                                <div class="atw-block atw-mb-1"><?php _e("Border", "wpadverts" ) ?></div>
                                <input type="text" name="color_border" value="<?php echo esc_attr( $sb["color_border"] ) ?>" class="color-border" data-default-color="<?php echo esc_attr( $sd["color_border"] ) ?>" />

                            </div>  
                        </div>
                    </div>          
                    
                    <div class="wpa-colors-hover">

                        <h3 class="atw-mt-6 atw-mb-3"><?php _e("Colors Hover", "wpadverts" ) ?></h3>

                        <div class="atw-flex wpa-admin-styling-color-picker">
                            <div>
                                <div class="atw-block atw-mb-1"><?php _e("Text", "wpadverts" ) ?></div>
                                <input type="text" name="color_text_h" value="<?php echo esc_attr( $sb["color_text_h"] ) ?>" class="color-text" data-default-color="<?php echo esc_attr( $sd["color_text_h"] ) ?>" />

                            </div>

                            <div>
                                <div class="atw-block atw-mb-1"><?php _e("Background", "wpadverts" ) ?></div>
                                <input type="text" name="color_bg_h" value="<?php echo esc_attr( $sb["color_bg_h"] ) ?>" class="color-bg" data-default-color="<?php echo esc_attr( $sd["color_bg_h"] ) ?>" />

                            </div>

                            <div>
                                <div class="atw-block atw-mb-1"><?php _e("Border", "wpadverts" ) ?></div>
                                <input type="text" name="color_border_h" value="<?php echo esc_attr( $sb["color_border_h"] ) ?>" class="color-border" data-default-color="<?php echo esc_attr( $sd["color_border_h"] ) ?>" />

                            </div>
                        </div>

                    </div>

                    <p class="submit">
                        <input type="submit" value="<?php echo __("Save Secondary Button Settings", "wpadverts") ?>" class="button-primary wpa-save-secondary-button" name="Submit"/>
                        
                        <a href="#" class="wpa-button-settings-reset atw-mx-2"><?php _e("Reset to defaults", "wpadverts" ) ?></a>
                        <span class="spinner atw-float-none atw-my-0"></span>
                    </p>

                </form>
            </div>
        
        </div>


        <div class="atw-flex atw-flex-1 atw-flex-col atw-justify-center atw-w-full atw-h-auto atw-border-0 atw-border-solid atw-border-gray-500 atw-bg-white">
            
            <div class="atw-flex atw-place-content-evenly atw-pb-9">
                <span class="atw-text-2xl">Button Preview</span>
            </div>

            <div class="atw-flex atw-place-content-evenly">

                <button class="wpa-preview-button-secondary wpa-preview-button-secondary-normal atw-inline-block hover:atw-bg-none atw-bg-none atw-text-white atw-w-auto atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-4 atw-py-2 <?php //echo "$border_radius $border_width $leading" ?>">
                    <span class=""><?php _e("Secondary Button", "wpadverts" ) ?></span>
                </button>

                <button class="wpa-preview-button-secondary wpa-preview-button-secondary-hover atw-inline-block atw-bg-none atw-w-auto atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-4 atw-py-2 <?php //echo "$border_radius $border_width $leading" ?>">
                    <span class=""><?php _e("Secondary Button Hover", "wpadverts" ) ?></span>
                </button>

            </div>

        </div>

    </div>

    <?php $button = array( "class_wrap" => "wpa-style-form", "name" => "form" ) ?>

    <div class="wpa-styling-tab-form atw-flex" style="display:none">

    <div class="atw-flex-1 atw-w-1/2">
        
        <div class="<?php echo esc_attr($button["class_wrap"]) ?>">
            <form action="" method="get" data-param="<?php echo esc_attr( $button["name"] ) ?>">

                <h3 class="atw-mt-6 atw-mb-3"><?php _e("Common Settings","wpadverts" ) ?></h3>

                <div class="atw-flex">

                    <div>
                        <div class="atw-block atw-mb-1"><?php _e("Color Palette", "wpadverts" ) ?></div>
                        <select name="palette" class="palette" data-default-value="<?php echo esc_attr( $frmd["palette"] ) ?>">
                            <option value="blue-gray" <?php selected( $frm["palette"], "blue-gray",) ?>><?php _e("Blue Gray", "wpadverts" ) ?></option>
                            <option value="cool-gray" <?php selected( $frm["palette"], "cool-gray",) ?>><?php _e("Cool Gray", "wpadverts" ) ?></option>
                            <option value="gray" <?php selected( $frm["palette"], "gray",) ?>><?php _e("Gray", "wpadverts" ) ?></option>
                            <option value="true-gray" <?php selected( $frm["palette"], "true-gray",) ?>><?php _e("True Gray", "wpadverts" ) ?></option>
                            <option value="warm-gray" <?php selected( $frm["palette"], "warm-gray",) ?>><?php _e("Warm Gray", "wpadverts" ) ?></option>
                        </select>
                    </div>

                    <div>
                        <div class="atw-block atw-mb-1"><?php _e("Style", "wpadverts" ) ?></div>
                        <select name="style" class="style" data-default-value="<?php echo esc_attr( $frmd["style"] ) ?>">
                            <option value="wpa-unstyled" <?php selected( $frm["style"], "wpa-unstyled",) ?>><?php _e("None (default styling)", "wpadverts" ) ?></option>
                            <option value="wpa-flat" <?php selected( $frm["style"], "wpa-flat",) ?>><?php _e("Flat", "wpadverts" ) ?></option>
                            <option value="wpa-solid" <?php selected( $frm["style"], "wpa-solid",) ?>><?php _e("Solid", "wpadverts" ) ?></option>
                            <option value="wpa-bottom-border" <?php selected( $frm["style"], "wpa-bottom-border",) ?>><?php _e("Bottom Border", "wpadverts" ) ?></option>
                        </select>
                    </div>

                   <div>
                        <div class="atw-block atw-mb-1"><?php _e("Shadow", "wpadverts" ) ?></div>
                        <select name="shadow" class="shadow" data-default-value="<?php echo esc_attr( $frmd["shadow"] ) ?>">
                            <option value="wpa-shadow-none" <?php selected( $frm["shadow"], "wpa-shadow-none",) ?>><?php _e("None (default styling)", "wpadverts" ) ?></option>
                            <option value="wpa-shadow-sm" <?php selected( $frm["shadow"], "wpa-shadow-sm",) ?>><?php _e("Small", "wpadverts" ) ?></option>
                            <option value="wpa-shadow-md" <?php selected( $frm["shadow"], "wpa-shadow-md",) ?>><?php _e("Medium", "wpadverts" ) ?></option>
                            <option value="wpa-shadow-inside" <?php selected( $frm["shadow"], "wpa-shadow-inside",) ?>><?php _e("Inside", "wpadverts" ) ?></option>
                        </select>
                    </div>



                </div>

                <h3 class="atw-mt-6 atw-mb-3"><?php _e("Border Settings","wpadverts" ) ?></h3>
                
                <div class="atw-flex">
                    <div>
                        <div class="atw-block atw-mb-1"><?php _e("Border Radius", "wpadverts" ) ?></div>
                        <select name="rounded" class="rounded" data-default-value="<?php echo esc_attr( $frmd["rounded"] ) ?>">
                            <option value="0" data-value="wpa-rounded-none" <?php selected( $frm["rounded"], "0",) ?>><?php _e("None", "wpadverts" ) ?></option>
                            <option value="1" data-value="wpa-rounded-sm" <?php selected( $frm["rounded"], "1",) ?>><?php _e("Extra Small", "wpadverts" ) ?></option>
                            <option value="2" data-value="wpa-rounded" <?php selected( $frm["rounded"], "2",) ?>><?php _e("Small", "wpadverts" ) ?></option>
                            <option value="3" data-value="wpa-rounded-md" <?php selected( $frm["rounded"], "3",) ?>><?php _e("Medium", "wpadverts" ) ?></option>
                            <option value="4" data-value="wpa-rounded-lg" <?php selected( $frm["rounded"], "4",) ?>><?php _e("Large", "wpadverts" ) ?></option>
                            <option value="5" data-value="wpa-rounded-xl" <?php selected( $frm["rounded"], "5",) ?>><?php _e("Extra Large", "wpadverts" ) ?></option>
                            <option value="6" data-value="wpa-rounded-full" <?php selected( $frm["rounded"], "6",) ?>><?php _e("Full", "wpadverts" ) ?></option>
                        </select>
                    </div>

                    <div>
                        <div class="atw-block atw-mb-1"><?php _e("Border Width", "wpadverts" ) ?></div>
                        <select name="border" class="border" data-default-value="<?php echo esc_attr( $frmd["border"] ) ?>">
                            <option value="0" data-value="wpa-border-none" <?php selected( $frm["border"], "0",) ?>><?php _e("None", "wpadverts" ) ?></option>
                            <option value="1" data-value="wpa-border-thin" <?php selected( $frm["border"], "1",) ?>><?php _e("Thin", "wpadverts" ) ?></option>
                            <option value="2" data-value="wpa-border-thick" <?php selected( $frm["border"], "2",) ?>><?php _e("Thick", "wpadverts" ) ?></option>
                            <option value="3" data-value="wpa-border-thick-x2" <?php selected( $frm["border"], "3",) ?>><?php _e("Extra Thick", "wpadverts" ) ?></option>
                        </select>
                    </div>
                </div>

                <h3 class="atw-mt-6 atw-mb-3"><?php _e("Other","wpadverts" ) ?></h3>
                
                <div class="atw-flex">
                    <div>
                        <div class="atw-block atw-mb-1"><?php _e("Interline", "wpadverts" ) ?></div>
                        <select name="interline" class="interline" data-default-value="<?php echo esc_attr( $frmd["interline"] ) ?>">
                            <option value="0" data-value="wpa-form-interline-disabled" <?php selected( $frm["interline"], "0",) ?>><?php _e("Hide", "wpadverts" ) ?></option>
                            <option value="1" data-value="wpa-form-interline" <?php selected( $frm["interline"], "1",) ?>><?php _e("Show", "wpadverts" ) ?></option>
                        </select>
                    </div>

                    <div>
                        <div class="atw-block atw-mb-1"><?php _e("Row Spacing", "wpadverts" ) ?></div>
                        <select name="spacing" class="spacing" data-default-value="<?php echo esc_attr( $frmd["spacing"] ) ?>">
                            <option value="0" data-value="wpa-spacing-0" <?php selected( $frm["spacing"], "0",) ?>><?php _e("None", "wpadverts" ) ?></option>
                            <option value="1" data-value="wpa-spacing-1" <?php selected( $frm["spacing"], "1",) ?>><?php _e("Small", "wpadverts" ) ?></option>
                            <option value="2" data-value="wpa-spacing-2" <?php selected( $frm["spacing"], "2",) ?>><?php _e("Medium", "wpadverts" ) ?></option>
                            <option value="3" data-value="wpa-spacing-3" <?php selected( $frm["spacing"], "3",) ?>><?php _e("Large", "wpadverts" ) ?></option>
                        </select>
                    </div>
                </div>

                <p class="submit">
                    <input type="submit" value="<?php echo __("Save Form Styling Settings", "wpadverts") ?>" class="button-primary wpa-save-secondary-button" name="Submit"/>
                    
                    <a href="#" class="wpa-button-settings-reset atw-mx-2"><?php _e("Reset to defaults", "wpadverts" ) ?></a>
                    <span class="spinner atw-float-none atw-my-0"></span>
                </p>

            </form>
        </div>
        
    </div>

        <div class="wpa-styling-form-preview atw-flex atw-flex-1 atw-items-center atw-justify-around atw-w-full atw-h-auto  atw-border-0 atw-border-solid atw-border-gray-500 atw-bg-white atw-p-6">
            <style type="text/css">
                <?php $palette = array( "blue-gray", "cool-gray", "gray", "true-gray", "warm-gray" ) ?>
                <?php foreach( $palette as $p ): ?>
                .wpadverts-block <?php echo sprintf(".wpadverts-form.%s", $p) ?> {
                    <?php wpadverts_print_grays_variables( array( "customize" => 1, "palette" => $p ) ) ?>
                }
                <?php endforeach; ?>
            </style>
            <?php include ADVERTS_PATH . "/templates/block-partials/form.php"; ?>

        </div>

    </div>


</div>
