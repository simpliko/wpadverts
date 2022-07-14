jQuery(function($) {

    $(".wpadverts-show-contact-form").on("click", function(e) {
        e.preventDefault();

        var $this = $(this);

        if($(".wpadverts-block-contact-box").is(":visible")) {
            $(".wpadverts-block-contact-box").hide();
            //window.location.hash = "";
        } else {
            $(".wpadverts-block-contact-box").show();
            window.location.hash = "#wpadverts-block-contact-box";
        }
    });
    
});