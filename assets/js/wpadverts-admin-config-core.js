jQuery(function($) {
    $("input[name=expired_ad_status]").on("click", function(e) {
        var value = $("input[name=expired_ad_status]:checked").val();
        
        if(value === "301") {
            $("#expired_ad_redirect_url").closest("tr").show();
        } else {
            $("#expired_ad_redirect_url").closest("tr").hide();
        }
    });
    
    $("input[name=expired_ad_status]:checked").click();
});