jQuery(function($) {
    $("#js-wpa-filter").on("click", function(e) {
        e.preventDefault();
        //$("#js-wpa-filters-wrap").toggle();
        if($("#js-wpa-filters-wrap").is(":visible")) {
            $("#js-wpa-filters-wrap").addClass("atw-hidden");
        } else {
            $("#js-wpa-filters-wrap").removeClass("atw-hidden");
        }
        return false;
    });
    
    $("#js-wpa-sort").on("click", function(e) {
        e.preventDefault();
        $("#js-wpa-sort-options").toggle();
        return false;
    });
    
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
    
    //$(".js-wpa-view-list").click();
});