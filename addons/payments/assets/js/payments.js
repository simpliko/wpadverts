jQuery(function($) {
    
    /**
     * Enable AJAX tab switching in [adverts_add] shortcode third step
     */
    $(".adverts-tab-link").click(function(e) {
        e.preventDefault();
        $(".adverts-tab-link").removeClass("current");
        $(".adverts-tab-content").css("opacity", 0.5);

        $(this).addClass("current");

        var data = {
            action: "adext_payments_render",
            gateway: $(this).data("tab"),
            page_id: $(".adverts-payment-data").data("page-id"),
            listing_id: $(".adverts-payment-data").data("listing-id"),
            object_id: $(".adverts-payment-data").data("object-id")
        }
        
        $.ajax({
            url: adverts_frontend_lang.ajaxurl,
            context: this,
            type: "post",
            dataType: "json",
            data: data,
            success: function(response) {
                $(".adverts-tab-content").css("opacity", 1).html(response.html);
            }
        });
        
    });
    
    /**
     * Place order in [adverts_add] shortcode third step
     */
    $(".adext-payments-place-order").click(function(e) {
        e.preventDefault();
        $(".adverts-tab-content").css("opacity", 0.5);
        
        var data = {
            action: "adext_payments_render",
            gateway: $(".adverts-tab-link.current").data("tab"),
            page_id: $(".adverts-payment-data").data("page-id"),
            listing_id: $(".adverts-payment-data").data("listing-id"),
            object_id: $(".adverts-payment-data").data("object-id"),
            form: $(".adverts-tab-content form").serializeArray()
        }
        
        $.ajax({
            url: adverts_frontend_lang.ajaxurl,
            context: this,
            type: "post",
            dataType: "json",
            data: data,
            success: function(response) {
                $(".adverts-tab-content").css("opacity", 1).html(response.html);
                
                if(response.result == 1) {
                    $(".adext-payments-place-order").fadeOut();
                    $("ul.adverts-tabs li").unbind("click").css("cursor", "default");
                }
                
                if(response.execute == "click") {
                    $(response.execute_id).click();
                } else if(response.execute == "submit") {
                    $(response.execute_id).submit();
                }
                
            }
        });
    });
    
    $(".adverts-tab-link.current").click();
});