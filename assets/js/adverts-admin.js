jQuery(function($) {

    // Suggest User
    if( $("#post_author_override").length ) {
        var paoText = $('<input type="text" />')
            .attr("id", "post_author_override_suggest")
            .attr("name", "post_author_override_suggest")
            .blur(function() {
                var pao = $("#post_author_override");
                
                if(pao.data("blur") == null) {
                    return;
                }
                
                $(this).val(pao.data("user-name"));
                pao.val(pao.data("user-id"));
                
                pao.data("blur", null);
                pao.data("user-name", null);
                pao.data("user-id", null);
                
            })
            .focus(function(e) {
                var pao = $("#post_author_override");
                pao.data("user-name", $("#post_author_override_suggest").val());
                pao.data("user-id", pao.val());
                pao.data("blur", 1);
                pao.val("");
            })
            .keyup(function(e) {
                
            })
            .suggest(ajaxurl + "?action=adverts_author_suggest", { 
                maxCacheSize: 0, 
                delimiter: '<!-- suggest delimeter -->' ,
                resultsClass: 'adverts_ac_results',
                onSelect: function(e) {
                    var pao = $("#post_author_override");
                    
                    pao.val("");
                    
                    pao.data("blur", null);
                    pao.data("user-name", null);
                    pao.data("user-id", null);
                    
                    var id = $(".adverts_ac_results .ac_over > span").data("id");
                    
                    $("#post_author_override").val(id);
                    $("#post_author").val(id);
                    
                    if(id == "0") {
                        $("#post_author_override_suggest").val("");
                    }
                }
            } )
            .val("");
            
        if( $("#post_author").val() != "0" ) {
            paoText.val($("#post_author_override option:selected").text());
        } else if( $("#post_author").val() == "0" ) {
            $("#post_author").val("0+");
        }
            
        var paoId = $('<input type="hidden" />')
            .attr("id", "post_author_override")
            .attr("name", "post_author_override")
            .val($("#post_author").val());
            
        var info = $('<span></span>').html(adverts_admin_lang.suggest_box_info);
            
        $("#post_author_override").before(info).after(paoId).after(paoText).remove();
    }

    // Handle Expire Timestamp
    
    if( $("#never_expires").length ) {
        $("#never_expires").click(function(e) {
            if($(this).is(":checked")) {
                $(".adverts-timestamp-disabler").show();
            } else {
                $(".adverts-timestamp-disabler").hide();
            }
            $(this).data("changed", "1");
        });
        
        if($("#never_expires").is(":checked")) {
            $(".adverts-timestamp-disabler").show();
        } else {
            $(".adverts-timestamp-disabler").hide();
        }
    }
    
    if( $("#timestamp-expire-div").length ) {
    
        var $tsediv = $("#timestamp-expire-div");
    
        advertsUpdateText = function(e) {

            if ( ! $tsediv.length )
                    return true;

            var attemptedDate, originalDate, currentDate, publishOn, 
            aa = $('#adverts-aa').val(),
            mm = $('#adverts-mm').val(), 
            jj = $('#adverts-jj').val(), 
            hh = $('#adverts-hh').val(), 
            mn = $('#adverts-mn').val();

            attemptedDate = new Date( aa, mm - 1, jj, hh, mn );
            originalDate = new Date( $('#adverts_hidden_aa').val(), $('#adverts_hidden_mm').val() -1, $('#adverts_hidden_jj').val(), $('#adverts_hidden_hh').val(), $('#adverts_hidden_mn').val() );
            currentDate = new Date( $('#adverts_cur_aa').val(), $('#adverts_cur_mm').val() -1, $('#adverts_cur_jj').val(), $('#adverts_cur_hh').val(), $('#adverts_cur_mn').val() );

            if ( attemptedDate.getFullYear() != aa || (1 + attemptedDate.getMonth()) != mm || attemptedDate.getDate() != jj || attemptedDate.getMinutes() != mn ) {
                $tsediv.find('.timestamp-wrap').addClass('form-invalid');
                return false;
            } else {
                $tsediv.find('.timestamp-wrap').removeClass('form-invalid');
            }

            if ( attemptedDate > currentDate || $("#never_expires").is(":checked") ) {
                publishOn = adverts_admin_lang.expires_on;
                $("input#publish").val($("input#publish").val().replace(" (" + adverts_admin_lang.expired + ")", ""));
                $("#adverts-post-status-option").prop("selected", false);
                $('#post-status-display').html($('select#post_status option:selected').text());
            } else {
                publishOn = adverts_admin_lang.expired_on;
                var exptxt = " (" + adverts_admin_lang.expired + ")";
                $("input#publish").val($("input#publish").val().replace(exptxt, "") + exptxt);
                
                if( e === null || !$(e.target).hasClass("save-post-status") ) {
                    $("select#post_status option:selected").prop("selected", false).attr("selected", null);
                    
                    $("#adverts-post-status-option").prop("selected", true).attr("selected", "selected");
                    $("#post-status-display").text(adverts_admin_lang.expired);
                }
            }


            if ( $("#never_expires").is(":checked") ) {
                $('#timestamp_expire').html( publishOn + ' <b>' +$("label[for='never_expires']").html() + '</b> ' );
            } else if ( originalDate.toUTCString() == attemptedDate.toUTCString() && $("#never_expires").data("changed") != "1" ) { //hack
                    // do nothing
            } else {
                $('#timestamp_expire').html(
                    publishOn + ' <b>' +
                    postL10n.dateFormat.replace( '%1$s', $('option[value="' + $('#adverts-mm').val() + '"]', '#adverts-mm').text() )
                        .replace( '%2$s', jj )
                        .replace( '%3$s', aa )
                        .replace( '%4$s', hh )
                        .replace( '%5$s', mn ) +
                        '</b> '
                );
            }

            return true;
        };


        $tsediv.siblings('a.edit-timestamp').click( function( event ) {
            if ( $tsediv.is( ':hidden' ) ) {
                $tsediv.slideDown('fast');
                $('#adverts-mm').focus();
                $(this).hide();
            }
            event.preventDefault();
        });

        $tsediv.find('.cancel-expiry-timestamp').click( function( event ) {
            $tsediv.slideUp('fast').siblings('a.edit-timestamp').show().focus();
            $('#adverts-mm').val($('#adverts_hidden_mm').val());
            $('#adverts-jj').val($('#adverts_hidden_jj').val());
            $('#adverts-aa').val($('#adverts_hidden_aa').val());
            $('#adverts-hh').val($('#adverts_hidden_hh').val());
            $('#adverts-mn').val($('#adverts_hidden_mn').val());
            advertsUpdateText( event );
            event.preventDefault();
        });

        $tsediv.find('.save-expiry-timestamp').click( function( event ) { // crazyhorse - multiple ok cancels
            if ( advertsUpdateText( event ) ) {
                $tsediv.slideUp('fast');
                $tsediv.siblings('a.edit-timestamp').show();
            }
            event.preventDefault();
        });
        
        $("a.save-timestamp, a.cancel-timestamp, a.save-post-status, a.cancel-post-status").click(advertsUpdateText);

        
    }
    
    $("#adverts_price").autoNumeric('init', adverts_currency);
    
    $("select#post_status").append($("<option></option>")
        .attr("id", "adverts-post-status-option")
        .addClass("adverts-post-status")
        .attr("value", "expired")
        .css("display", "none")
        .html("&nbsp;" + adverts_admin_lang.expired)
    );
    
    $("form#post").submit(function() {
        if($('select#post_status option:selected').hasClass("adverts-post-status")) {
            $("input#publish").attr("name", "publish_adverts");
        }
    });
    
    if( $("#timestamp-expire-div").length ) {
        advertsUpdateText(null);
    }

});