<style type="text/css">
    <?php wpadverts_block_button_css( "primary", isset( $atts["primary_button"] ) ? $atts["primary_button"] : array(), ".wpadverts-blocks.wpadverts-modal" ) ?>
    <?php wpadverts_block_button_css( "secondary", isset( $atts["secondary_button"] ) ? $atts["secondary_button"] : array(), ".wpadverts-blocks.wpadverts-modal" ) ?>
</style>

<script type="text/html" id="tmpl-wpadverts-modal">
<!-- This example requires Tailwind CSS v2.0+ -->
<div class="wpadverts-blocks wpadverts-modal atw-fixed atw-z-10 atw-inset-0 atw-overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="atw-flex atw-items-end atw-justify-center atw-min-h-screen atw-pt-4 atw-px-4 atw-pb-20 atw-text-center sm:atw-block sm:atw-p-0 atw-box-border">
    <!--
      Background overlay, show/hide based on modal state.

      Entering: "ease-out duration-300"
        From: "opacity-0"
        To: "opacity-100"
      Leaving: "ease-in duration-200"
        From: "opacity-100"
        To: "opacity-0"
    -->
    <div class="atw-fixed atw-inset-0 atw-bg-gray-500 atw-bg-opacity-75 atw-transition-opacity" aria-hidden="true"></div>

    <!-- This element is to trick the browser into centering the modal contents. -->
    <span class="atw-hidden sm:atw-inline-block sm:atw-align-middle sm:atw-h-screen" aria-hidden="true">&#8203;</span>

    <!--
      Modal panel, show/hide based on modal state.

      Entering: "ease-out duration-300"
        From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        To: "opacity-100 translate-y-0 sm:scale-100"
      Leaving: "ease-in duration-200"
        From: "opacity-100 translate-y-0 sm:scale-100"
        To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    -->
    <div class="atw-relative atw-inline-block atw-align-bottom atw-bg-white atw-rounded-lg atw-text-left atw-overflow-hidden atw-shadow-xl atw-transform atw-transition-all sm:atw-my-8 sm:atw-align-middle sm:atw-max-w-lg sm:atw-w-full">
      <div class="atw-bg-white atw-px-4 atw-pt-5 atw-pb-4 sm:atw-p-6 sm:atw-pb-4">
        <div class="sm:atw-flex sm:atw-items-start">
          <div class="wpa-icon-{{ data.icon }} atw-mx-auto atw-flex-shrink-0 atw-flex atw-items-center atw-justify-center atw-h-12 atw-w-12 atw-rounded-full sm:atw-mx-0 sm:atw-h-10 sm:atw-w-10">
            <# if( data.icon === "question" ) { #>
                <svg class="atw-h-6 atw-w-6 atw-text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            <# } else if( data.icon === "success" ) { #>
                <svg class="atw-h-6 atw-w-6 atw-text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            <# } #>
          </div>
          <div class="atw-mt-3 atw-text-center sm:atw-mt-0 sm:atw-ml-4 sm:atw-text-left">
            <h3 class="js-wpa-modal-title atw-text-lg atw-leading-6 atw-font-medium atw-text-gray-900" id="modal-title">{{ data.title }}</h3>
            <div class="atw-mt-2">
              <p class="js-wpa-modal-text atw-text-sm atw-text-gray-500">{{{ data.text }}}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="atw-bg-gray-50 atw-px-4 atw-py-3 sm:atw-px-6 sm:atw-flex sm:atw-flex-row-reverse ">
      
        <div class="wpa-progress wpadverts-hidden atw-p-4 atw-text-center">
            <svg class=" atw-animate-spin atw-h-8 atw-w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="atw-opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="atw-opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <# if( typeof data.confirm !== 'undefined' ) { #>
        <div class="wpa-confirm atw-flex atw-flex-row atw-items-center atw-pt-2 sm:atw-ml-3">
            <?php echo wpadverts_block_button( array( "html" => '<span class="wpa-confirm-text">{{ data.confirm.title }}</span>', "type" => "primary" ), array() ) ?>
        </div>        
        <# } #>
        
        <# if( typeof data.cancel !== 'undefined' ) { #>
        <div class="wpa-cancel atw-flex atw-flex-row atw-items-center atw-pt-2 sm:atw-ml-3">
            <?php echo wpadverts_block_button( array( "html" => '<span class="wpa-cancel-text">{{ data.cancel.title }}</span>', "type" => "secondary" ), array() ) ?>
        </div>
        <# } #>

      </div>
    </div>
  </div>
</div>
</script>