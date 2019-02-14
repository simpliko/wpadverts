var WPADVERTS = WPADVERTS || {};

WPADVERTS.Payments = {
    DefaultSuccess: function(response) {
        jQuery(".adverts-tab-content").css("opacity", 1).html(response.html);
    },
    
    DefaultError: function(response) {
        
    },
    
    PlaceOrder: function(e) {

        var $ = jQuery;
        var data = {
            action: "adext_payments_render",
            gateway: $(".adverts-tab-link.current").data("tab"),
            page_id: $(".adverts-payment-data").data("page-id"),
            listing_id: $(".adverts-payment-data").data("listing-id"),
            object_id: $(".adverts-payment-data").data("object-id"),
            payment_id: $(".adverts-payment-data").data("payment-id"),
            form: $(".adverts-tab-content form").serializeArray()
        };
        
        $(".adverts-tab-content").css("opacity", 0.5);
        
        $.ajax({
            url: adverts_frontend_lang.ajaxurl,
            context: this,
            type: "post",
            dataType: "json",
            data: data,
            success: function(response) {
                var tab = $(".adverts-tab-link.current").data("tab");
                var successCallback = WPADVERTS.Payments.DefaultSuccess;

                if( typeof WPADVERTS.Payments.Engine[tab] !== 'undefined' ) {
                    successCallback = jQuery.proxy(WPADVERTS.Payments.Engine[tab].success, WPADVERTS.Payments.Engine[tab]);
                }

                successCallback(response);

                if(response.result == 1) {
                    $(".adext-payments-place-order").fadeOut();
                    $("ul.adverts-tabs li").unbind("click").css("cursor", "default");
                }
                
                if(response.execute == "click") {
                    $(response.execute_id).click();
                } else if(response.execute == "submit") {
                    $(response.execute_id).submit();
                }
                
            },
            error: function(response) {
                var tab = $(".adverts-tab-link.current").data("tab");
                var errorCallback = WPADVERTS.Payments.DefaultError;

                if( typeof WPADVERTS.Payments.Engine[tab] !== 'undefined' ) {
                    errorCallback = jQuery.proxy(WPADVERTS.Payments.Engine[tab].error, WPADVERTS.Payments.Engine[tab]);
                }

                errorCallback(response);
            }
        });
    },
    
    Engine: []
};

jQuery(function($) {
    
    /**
     * Enable AJAX tab switching in [adverts_add] shortcode third step
     */
    $(".adverts-tab-link").click(function(e) {
        e.preventDefault();
        
        if(!$(".adext-payments-place-order").is(":visible")) {
            return;
        }
        
        $(".adverts-tab-link").removeClass("current");
        $(".adverts-tab-content").css("opacity", 0.5);

        $(this).addClass("current");

        var data = {
            action: "adext_payments_render",
            gateway: $(this).data("tab"),
            page_id: $(".adverts-payment-data").data("page-id"),
            listing_id: $(".adverts-payment-data").data("listing-id"),
            object_id: $(".adverts-payment-data").data("object-id"),
            payment_id: $(".adverts-payment-data").data("payment-id")
        };
        
        var tab = $(this).data("tab");
        var successCallback = WPADVERTS.Payments.DefaultSuccess;
        
        if( typeof WPADVERTS.Payments.Engine[tab] !== 'undefined' ) {
            successCallback = jQuery.proxy(WPADVERTS.Payments.Engine[tab].success, WPADVERTS.Payments.Engine[tab]);
        }
        
        $.ajax({
            url: adverts_frontend_lang.ajaxurl,
            context: this,
            type: "post",
            dataType: "json",
            data: data,
            success: successCallback
        });
        
    });
    
    /**
     * Place order in [adverts_add] shortcode third step
     */
    $(".adext-payments-place-order").click(function(e) {
        e.preventDefault();
        
        
        var tab = $(".adverts-tab-link.current").data("tab");
        var place_order = null;
        if( typeof WPADVERTS.Payments.Engine[tab] !== 'undefined' ) {
            place_order = jQuery.proxy(WPADVERTS.Payments.Engine[tab].place_order, WPADVERTS.Payments.Engine[tab]);
        } else {
            place_order = WPADVERTS.Payments.PlaceOrder;
        }
        
        place_order();
        
    });
    
    $(".adverts-tab-link.current").click();
});