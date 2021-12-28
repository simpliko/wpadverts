var WPADVERTS = WPADVERTS || {};

WPADVERTS.ListSort = function() {
    this.element = { };
    this.element.button = jQuery(".adverts-list-sort-button");
    this.element.options = jQuery(".adverts-list-sort-options");
    this.element.button.on("click", jQuery.proxy(this.button_clicked, this));
};

WPADVERTS.ListSort.prototype.button_clicked = function(e) {
    if( typeof e !== 'undefined' ) {
        e.preventDefault();
    }
    this.element.options.toggle();
};

jQuery(function($) {
    if($(".adverts-list-sort-button").length > 0) {
        new WPADVERTS.ListSort();
    }
});

jQuery(function($) {
    var currentTallest = 0,
        currentRowStart = 0,
        rowDivs = new Array(),
        $el,
        topPosition = 0;

   $(".adverts-js").show();
   $(".adverts-no-js").hide();

   $('.advert-post-title a').css('overflow', 'initial').css("height", 'auto');
   
   $(".adverts-form-submit").click(function(e) {
       e.preventDefault();
       $(this).closest("form").submit();
   });
   
   $('.advert-item .advert-link').each(function() {

    $el = $(this);
    // added closest(...)
    var topPostion = $el.closest('.advert-item').position().top;

    if($el.closest('.advert-item').hasClass('advert-item-col-1')) {
        return;
    }

    if (currentRowStart != topPostion) {
        // we just came to a new row.  Set all the heights on the completed row
        for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
            rowDivs[currentDiv].height(currentTallest);
        }

        // set the variables for the new row
        rowDivs.length = 0; // empty the array
        currentRowStart = topPostion;
        currentTallest = $el.height();
        rowDivs.push($el);

    } else {
        // another div on the current row.  Add it to the list and check if it's taller
        rowDivs.push($el);
        currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
   }

   // do the last row
    for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
        rowDivs[currentDiv].height(currentTallest);
    }
   
   }); 
  
   if( $(".wpadverts-slides").length > 0 ) {
       
        if(adverts_frontend_lang.lightbox === "1") {
            $( '.wpadverts-swipe' ).swipebox({
                afterMedia: function(e) {
                    $( '#swipebox-container .wpadverts-video-player' ).each(function(index, item) {
                        var $this = jQuery(item);
                        
                        if($this.data("wpadverts-single-player") != "1") {
                            new WPADVERTS.Single.Player($this);
                            $this.data("wpadverts-single-player", "1");
                        }
                    });
                }
            });
        }
   }
   
   
    $(".adverts-show-contact").click(function(e) {
        
        e.preventDefault();
        
        $(".adverts-loader").css("display", "inline-block");
        //$(".adverts-loader").addClass("animate-spin");
        
        var data = {
            action: 'adverts_show_contact',
            security: 'nonce',
            id: $(this).data("id")
        };
        
        $.ajax(adverts_frontend_lang.ajaxurl, {
            data: data,
            dataType: 'json',
            type: 'post',
            success: function(response) {
                
                var phone = "\u2014";
                var email = "\u2014";
                
                if(response.phone) {
                    phone = $("<a></a>").attr("href", "tel:"+response.phone).text(response.phone);
                }
                
                if(response.email) {
                    email = $("<a></a>").attr("href", "mailto:"+response.email).text(response.email);
                }
                
                if(response.result == 1) {
                    $('.adverts-contact-phone').html(phone);
                    $('.adverts-contact-email').html(email);
                    $('.adverts-contact-box-toggle').slideToggle("fast");
                } else {
                    alert(response.error);
                }
                

                $('.adverts-loader').hide();
                //$(".adverts-loader").removeClass("animate-spin");
                
            }
        });
        

    });
    
    if($(".adverts-show-contact-form").length > 0) {
        $(".adverts-show-contact-form").click(function(e) {
            e.preventDefault();
            $('.adverts-contact-box-toggle').slideToggle("fast");
        });
    }
    
    if($(".adverts-filter-money").length > 0) {
        $(".adverts-filter-money").autoNumeric('init', adverts_currency);
    }
    
    if($(".adverts-form-filters").length > 0) {
        $(".adverts-form-filters").click(function(e) {
            e.preventDefault();

            if($(".adverts-advanced-search-icon").hasClass("adverts-icon-down-open")) {
                $(".adverts-advanced-search-icon").addClass("adverts-icon-up-open");
                $(".adverts-advanced-search-icon").removeClass("adverts-icon-down-open");
                if($("#reveal_hidden").length < 1) {
                    var rh = $('<input type="hidden" name="reveal_hidden" id="reveal_hidden" value="1" />');
                    $(".adverts-search-form").prepend(rh);
                }
            } else {
                $(".adverts-advanced-search-icon").removeClass("adverts-icon-up-open");
                $(".adverts-advanced-search-icon").addClass("adverts-icon-down-open");
                $("#reveal_hidden").remove();
            }
            
            $(".adverts-search-hidden").slideToggle("fast");
        });
    }
    
    if($("#reveal_hidden").length > 0) {
        $(".adverts-advanced-search-icon").addClass("adverts-icon-up-open");
        $(".adverts-advanced-search-icon").removeClass("adverts-icon-down-open");
        $(".adverts-search-hidden").show();
    }
    
    if($(".adverts-search-form").length > 0) {
        $(".adverts-search-form").submit(function(e) {
            $(this).find(":input").filter(function(){ 
                return !this.value; 
            }).attr("name", "");
            
            return true; // ensure form still submits
        });
    }
    
    if($(".wpadverts-reveal-phone").length > 0) {
        $(".wpadverts-reveal-phone .wpadverts-reveal-button").on("click", function(e) {
            e.preventDefault();
            
            var p1 = $(".wpadverts-reveal-partial-1").text();
            var p2 = $(".wpadverts-reveal-final").data("partial");
            var p = p1 + p2;
            
            $(".wpadverts-reveal-wrap").hide();
            $(".wpadverts-reveal-final").attr("href", "tel:"+p);
            $(".wpadverts-reveal-final").text(p);
            $(".wpadverts-reveal-final").fadeIn("fast");
            
            
        });
    }
    
});


/* BLOCK CONTACT DETAILS */

jQuery(function($) {
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

    $(".wpadverts-show-contact-form").on("click", function(e) {
        e.preventDefault();

        var $this = $(this);
        var bar = $this.closest( ".wpa-cpt-contact-details" );

        //$(".wpa-utility-sticky-bg").removeClass("atw-opacity-0");
        //$(".wpa-utility-sticky").removeClass("atw-translate-x-full");

        $(".wpa-utility-sticky-wrap").show();
        var bg = $(".wpa-utility-sticky-bg");
        var st = $(".wpa-utility-sticky");

        bg.removeClass("atw-hidden").show().css("opacity", "1");//.addClass("atw-opacity-100");
        st.removeClass("atw-translate-x-full");

        
    });

    $(".wpa-sticky-close").on("click", function(e) {
        e.preventDefault();

        
        var bg = $(".wpa-utility-sticky-bg");
        var st = $(".wpa-utility-sticky");

        bg.css("opacity", "0");//.addClass("atw-opacity-100");
        st.addClass("atw-translate-x-full");

        $(".wpa-utility-sticky-wrap").hide();

    });

    $(".wpadverts-more").on("click", function(e) {
        e.preventDefault();
        $(".wpa-contact-more").addClass("atw-bottom-0");
        $(".wpa-more-bg").show();
    });

    $(".wpa-more-bg").on("click",function() {
        $(".wpa-contact-more").removeClass("atw-bottom-0");
        $(".wpa-more-bg").hide();
    });
});

function wpadverts_close_more() {

}

jQuery(function($) {
    $(".wpadverts-block-cf-send").on("click", function(e) {
        e.preventDefault();

        var spinner = $(this).find(".wpa-utility-spinner");
        var btn = spinner.closest("button");

        spinner.removeClass("atw-hidden");
        btn.addClass("atw-opacity-60");
        btn.attr("disabled", "disabled");
        btn.blur();

        var data = {
            action: "wpadverts-contact-form-submit",
            post_id: 5737,
            data: {}
        };
        jQuery.each(jQuery('form.wpadverts-form').serializeArray(), function() {
            data.data[this.name] = this.value;
        });

        jQuery.ajax({
            url: adverts_frontend_lang.ajaxurl,
            data: data,
            type: "post", 
            dataType: "json",
            success: function( result ) {
                var spinner = $(".wpadverts-block-cf-send").find(".wpa-utility-spinner");
                var btn = spinner.closest("button");
        
                spinner.addClass("atw-hidden");
                btn.removeClass("atw-opacity-60");
                btn.attr("disabled", false);

                var $form = jQuery('form.wpadverts-form');
                $form.find(".wpa-field-wrap").removeClass("wpa-field-error");
                $form.find(".wpa-field-wrap").find(".wpa-errors-list").remove();

                jQuery.each(result.errors, function( index, item ) {
                    var $form = jQuery('form.wpadverts-form');
                    var wrap = jQuery(".wpa-field-wrap.wpa-field--"+index);
                    
                    wrap.addClass("wpa-field-error");


                    var ul = jQuery("<ul></ul>").addClass("wpa-errors-list");
                    jQuery.each(item, function(j, error_message ) {
                        var li = jQuery("<li></li>").addClass("wpa-error-item").html(error_message);
                        ul.append(li);
                    });
                    
                    wrap.append(ul);


                });

                $form.find(".wpa-feedback .wpa-feedback-error .wpa-feedback-title").text(result.message);
                $form.find(".wpa-feedback .wpa-feedback-error").show();

                var d = $('.wpa-utility-sticky');
                d.scrollTop(d.prop("scrollHeight"));

            },
            error: function( result ) {
                var spinner = $(".wpadverts-block-cf-send").find(".wpa-utility-spinner");
                var btn = spinner.closest("button");
        
                spinner.addClass("atw-hidden");
                btn.removeClass("atw-opacity-60");
                btn.attr("disabled", false);


                
            }
        });

        return false;
    });
});