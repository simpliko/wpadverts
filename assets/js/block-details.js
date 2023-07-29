jQuery(function($) {

    $(".wpadverts-contact-reveal").click(function(e) {
        
        e.preventDefault();
        
        if(!$(".wpadverts-contact-reveal-box").hasClass("atw-hidden")) {
            return;
        }

        $(".wpadverts-contact-reveal-box").removeClass("atw-hidden");
        
        var data = {
            action: 'adverts_show_contact',
            mode: 'block',
            security: 'nonce',
            id: $(this).data("id")
        };
        
        $.ajax(wpadverts_block_details.ajaxurl, {
            data: data,
            dataType: 'json',
            type: 'post',
            context: $(".wpadverts-contact-reveal-box"),
            success: function(response) {
                
                $(".wpadverts-contact-reveal").hide();

                this.find(".wpadverts-reveal-loader").addClass("atw-hidden");
                this.find(".wpadverts-reveal-inner").removeClass("atw-hidden");

                jQuery.each(response.data, jQuery.proxy( function(key, item) {
                    var css = ".wpadverts-reveal--" + key + " .wpadverts-reveal-value";

                    if(typeof item.html !== 'undefined' && item.html.length > 0)  {
                        this.find(css).html(item.html);
                    } else {
                        this.find(css).text(item.value);
                    }

                }, this ));
                
            }
        });
        

    });

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