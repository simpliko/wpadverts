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

    $(".wpadverts-reveal-phone").on("click", function(e) {
        e.preventDefault();

        if($(this).find(".wpadverts-phone-reveal").length > 0) {
            return;
        }

        var phone = $(this).data("ph1") + "" + $(this).data("ph2");
        location.href='tel:'+phone;
    });
    
    $(".wpadverts-phone-reveal").on("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        var wrap = $(this).closest(".wpadverts-reveal-phone");
        var phone = wrap.find(".wpadverts-phone").data("ph1") + "" + wrap.data("ph2");

        wrap.find(".wpadverts-phone").hide().text(phone).fadeIn("slow");
        $(this).remove();
    });
    
});