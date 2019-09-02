jQuery(function($) {
    
    $(".adverts-update-row").each(function(index, item) {
        var update = $(item);
        var plugin = $(this).prev();
        
        if(update.length == 0) {
            return;
        }
        
        update.addClass(plugin.attr("class"));
        
        if(update.hasClass("adverts-update-row-license")) {
            plugin.children().css("box-shadow", "0 0 0 transparent");
        }
        
    });
    
    $(".adverts-update-activate-button").click(function(e) {
        e.preventDefault();
        
        var row = $(this).closest(".adverts-update-row");
        
        row.find(".adverts-update-loader").removeClass("adverts-off");
        
        $.ajax(ajaxurl, {
            data: {
                action: "adverts_license_validate_" + row.data("slug") ,
                license: row.find("input[name=license]").val()
            },
            dataType: 'json',
            type: 'post',
            success: function(response) {
                if(response.status == 200) {
                    window.location.reload();
                } else {
                    row.find(".adverts-update-loader").addClass("adverts-off");
                    alert(response.message);
                }
            }
        });
    });
    
    $(".adverts-license-link").click(function(e) {
        e.preventDefault();
        
        var tr = $(this).closest("tr");
        
        if(tr.hasClass("update")) {
            tr.next().children().css("box-shadow", "0 0 0 transparent");
        } else {
            tr.children().css("box-shadow", "0 0 0 transparent");
        }
        
        tr.next(".adverts-update-row-license-change").removeClass("adverts-off");
    });
    
    $(".adverts-update-button-cancel").click(function(e) {
        e.preventDefault();

        $(this).closest("tr").prev().children().attr("style", "");
        $(this).closest("tr").addClass("adverts-off");
        
        var input = $(this).closest(".adverts-inline-edit").find("input[name=license]");
        
        input.val( input.data("value") );
    });
    
    $(".adverts-update-row-license-change").prev();
    
});
