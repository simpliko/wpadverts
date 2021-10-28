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

<?php wp_enqueue_script( 'adverts-admin-styling' ); ?>

<div class="wrap">
    <h2 class="">
        <?php _e("Default Styles", "wpadverts") ?>
    </h2>

    <?php adverts_admin_flash() ?>

    <?php 
    
    wp_enqueue_style( 'wpadverts-blocks' ) ;
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    
    $buttons = array(
        array(
            "class_wrap" => "wpa-style-primary-button",
            "name" => "primary_button"
        )

    )

    ?>
  

    
    <div class="atw-flex">
        <div class="atw-flex-grow ">
        <?php foreach( $buttons as $button ): ?>
        <div class="<?php echo esc_attr($button["class_wrap"]) ?>">
            <div class="atw-flex">
            <div>
                Border Radius
                <br/>
                <select name="<?php echo $button["name"] ?>[border_radius]" class="border-radius">
                    <option value="atw-rounded-none">None</option>
                    <option value="atw-rounded-sm">Extra Small</option>
                    <option value="atw-rounded">Small</option>
                    <option value="atw-rounded-md">Medium</option>
                    <option value="atw-rounded-lg">Large</option>
                    <option value="atw-rounded-xl">Extra Large</option>
                    <option value="atw-rounded-full">Full</option>
                </select>
            </div>

            <div>
                Border Width
                <br/>
                <select name="<?php echo $button["name"] ?>[border_width]" class="border-width">
                    <option value="atw-border-0">None</option>
                    <option value="atw-border">Thin</option>
                    <option value="atw-border-2">Thick</option>
                    <option value="atw-border-4">Extra Thick</option>
                </select>
            </div>

            </div>

            <div class="wpa-colors-normal">
                <h3>Colors Normal</h3>

                <div class="atw-flex">
                    <div>
                        Text Color
                        <br/>
                        <input type="text" name="<?php echo $button["name"] ?>[color_text]" value="#bada55" class="color-text" data-default-color="#effeff" />

                    </div>

                    <div>
                        Background Color
                        <br/>
                        <input type="text" name="<?php echo $button["name"] ?>[color_bg]" value="#bada55" class="color-bg" data-default-color="#effeff" />

                    </div>

                    <div>
                        Border Color
                        <br/>
                        <input type="text" name="<?php echo $button["name"] ?>[color_border]" value="#bada55" class="color-border" data-default-color="#effeff" />

                    </div>  
                </div>
            </div>          
            
            <div class="wpa-colors-hover">

                <h3>Colors Hover</h3>

                <div class="atw-flex">
                    <div>
                        Text Color
                        <br/>
                        <input type="text" name="<?php echo $button["name"] ?>[color_text_h]" value="#bada55" class="color-text" data-default-color="#effeff" />

                    </div>

                    <div>
                        Background Color
                        <br/>
                        <input type="text" name="<?php echo $button["name"] ?>[color_bg_h]" value="#bada55" class="color-bg" data-default-color="#effeff" />

                    </div>

                    <div>
                        Border Color
                        <br/>
                        <input type="text" name="<?php echo $button["name"] ?>[color_border_h]" value="#bada55" class="color-border" data-default-color="#effeff" />

                    </div>
                </div>

            </div>

        </div>
        <?php endforeach; ?>
        </div>


        <div class="atw-flex atw-flex-1 atw-items-center atw-justify-around atw-w-full atw-h-48 atw-border atw-border-solid atw-border-gray-800 atw-bg-white">
            
            <button class="wpa-preview-button-primary wpa-preview-button-primary-normal atw-inline-block hover:atw-bg-none atw-bg-none atw-text-white atw-w-auto atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-4 atw-py-2 <?php //echo "$border_radius $border_width $leading" ?>">
                <span class=""><?php _e("Button", "wpadverts" ) ?></span>
            </button>

            <button class="wpa-preview-button-primary wpa-preview-button-primary-hover atw-inline-block atw-bg-none atw-w-auto atw-text-base atw-outline-none atw-border-solid atw-font-semibold atw-px-4 atw-py-2 <?php //echo "$border_radius $border_width $leading" ?>">
                <span class=""><?php _e("Button Hover", "wpadverts" ) ?></span>
            </button>

        </div>

    </div>
</div>


<form action="" method="post" class="adverts-form">
    <?php /*
        <table class="form-table">
            <tbody>
            <?php echo adverts_form_layout_config($form) ?>
            </tbody>
        </table>
*/ ?>
        <p class="submit">
            <input type="submit" value="<?php esc_attr_e($button_text) ?>" class="button-primary" name="Submit"/>
        </p>

    </form>