jQuery(function($) {

    $(".wpa-block-list-results > .wpa-result-item").on("click", function(e) {
        window.location = $(this).find(".wpa-result-link").attr("href");
    });
    $(".wpa-block-list-results > .wpa-result-item").addClass("atw-cursor-pointer");

    $(".js-wpa-filters > button").on("click", function(e) {
        e.preventDefault();
        if($("#js-wpa-filters-wrap").hasClass("wpadverts-hidden")) {
            // show
            $("#js-wpa-filters-wrap").removeClass("wpadverts-hidden");
            if($("#reveal_hidden").length < 1) {
                var rh = $('<input type="hidden" name="reveal_hidden" id="reveal_hidden" value="1" />');
                $(".wpadverts-block-search .wpadverts-form").prepend(rh);
            }
        } else {
            // hide
            $("#js-wpa-filters-wrap").addClass("wpadverts-hidden");
            $("#reveal_hidden").remove();

        }
        return false;
    });
    if($("#reveal_hidden").length > 0) {
        $("#js-wpa-filters-wrap").removeClass("wpadverts-hidden");
    }

    $("#js-wpa-sort").on("click", function(e) {
        e.preventDefault();
        $("#js-wpa-sort-options").toggle();
        return false;
    });

    /*
    $(".js-wpa-view-list").on("click", function(e) {
        e.preventDefault(); 
        
        $(".js-wpa-view-list").addClass("wpa-selected");
        $(".js-wpa-view-grid").removeClass("wpa-selected");
        
        var results = $(".wpa-results");
        results.addClass("wpa-list-view");
        results.removeClass("wpa-grid-view");
        
    });
    $(".js-wpa-view-grid").on("click", function(e) {
        e.preventDefault(); 
        
        $(".js-wpa-view-list").removeClass("wpa-selected");
        $(".js-wpa-view-grid").addClass("wpa-selected");
        
        var results = $(".wpa-results");
        results.removeClass("wpa-list-view");
        results.addClass("wpa-grid-view");
    });
    */
});