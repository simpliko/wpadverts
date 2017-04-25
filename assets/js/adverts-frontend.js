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
   
   if( $(".rslides").length > 0 ) {
       $(".rslides").responsiveSlides({
        auto: false,
        pagination: true,
        nav: true,
        fade: 500,
        maxwidth: 800
      });
   }
   
    $(".adverts-show-contact").click(function(e) {
        
        e.preventDefault();
        
        $(".adverts-loader").show();
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
                    $('.adverts-contact-box').slideToggle("fast");
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
            $('.adverts-contact-box').slideToggle("fast");
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
    
});
