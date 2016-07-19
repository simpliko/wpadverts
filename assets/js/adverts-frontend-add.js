jQuery(function($) {
    function adverts_delete_on_unload() {
        if($("#_post_id").val() == "") {
            return;
        }

        var data = {
            action: 'adverts_delete_tmp',
            security: 'nonce',
            id: $("#_post_id").val()
        };

        $.ajax(adverts_frontend_lang.ajaxurl, {
            data: data,
            dataType: 'json',
            type: 'post',
            success: function(response) { }
        });
    }

    $(window).bind("beforeunload", adverts_delete_on_unload);
    $(".adverts-cancel-unload").click(function(e) {
        $(window).unbind("beforeunload", adverts_delete_on_unload)
    });
    
});