<div class="wpadverts-cpt <?php echo sprintf( "wpadverts-cpt-", $atts["post_type"] ) ?> wpadverts-cpt-single-contact atw-w-full atw-flex atw-flex-col">

    <div class="wpa-cpt-contact-details atw-my-6 atw--mx-1">

    <div class="wpadverts-flash wpa-style-info wpa-layout-big">
        <div class="wpa-flash-content atw-flex">

            <span class="wpa-flash-icon"><i class="fa-solid fa-lock"></i></span>

            <div class="atw-flex-1 atw-flex atw-flex-col atw-items-center">
                
                <span class="wpa-flash-message atw-flex-1 atw-font-bold atw-py-3">
                    <?php echo esc_html( $message_header ) ?>
                </span>
                <span class="wpa-flash-message atw-flex-1 atw-pb-3 atw-text-center"><?php echo $message ?></span>  
                
                <span class="atw-flex atw-flex-row atw-pb-3 atw-w-full atw-flex-col md:atw-flex-row">

                    <form action="<?php echo esc_attr( $url_login ) ?>" class="atw-p-3 atw-flex-grow">
                        <?php wpadverts_block_button( array( "text" => __("Login","wpadverts"), "type" => "secondary", "action" => "submit" )); ?>
                    </form>

                    <form action="<?php echo esc_attr( $url_register ) ?>" method="get" class="atw-p-3 atw-flex-grow">
                        <?php wpadverts_block_button( array( "text" => __("Register", "wpadverts"), "type" => "secondary", "action" => "submit" )); ?>
                    </form>
                </span>
                
            </div>
        </div>
    </div>



    </div>

    <div class="wpa-more-bg atw-z-30 atw-bg-black atw-inset-0 atw-absolute atw-opacity-60" style="display:none"></div>


</div>