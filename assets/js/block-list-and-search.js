jQuery(function($) {

    var wpa_block_list_drag = false;
    document.addEventListener(
        'mousedown', () => wpa_block_list_drag = false
    );

    document.addEventListener(
        'mousemove', () => wpa_block_list_drag = true
    );

    $(".wpa-block-list-results > .wpa-result-item").on("click", function(e) {
        if(!wpa_block_list_drag) {
            window.location = $(this).find(".wpa-result-link").attr("href");
        }
    });
    $(".wpa-block-list-results > .wpa-result-item").on("auxclick", function(e) {
        if(!wpa_block_list_drag) {
            window.location = $(this).find(".wpa-result-link").attr("href");
        }
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

});

var WPADVERTS = WPADVERTS || {};

WPADVERTS.GridGallery = function(item) {
    var $item = jQuery(item);

    this.slider = tns({
        container: "#" + $item.find('.wpa-grid-gallery-slider').attr("id"),
        items: 1,
        slideBy: 1,
        autoplay: false,
        mouseDrag: true,
        center: false,
        controls: false,
        nav: false,
        lazyload: $item.hasClass("wpa-slider-is-lazy"),
        lazyloadSelector: ".wpa-lazy-img"
    });

    this.prev = $item.find(".wpa-grid-gallery-left-btn");
    this.next = $item.find(".wpa-grid-gallery-right-btn");

    this.current = $item.find(".wpa-grid-gallery-current");

    this.slider.events.on('indexChanged', jQuery.proxy(this.IndexChanged, this));

    this.prev.on("click", jQuery.proxy(this.PrevClick, this));
    this.next.on("click", jQuery.proxy(this.NextClick, this));

    this.circles = $item.find(".wpa-block-gallery-circles .wpa-block-gallery-circle");

    this.PaginationVis();
    this.HandleCircles();
};

WPADVERTS.GridGallery.prototype.PrevClick = function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.slider.goTo("prev");
};

WPADVERTS.GridGallery.prototype.NextClick = function(e) {
    e.preventDefault();
    e.stopPropagation();
    this.slider.goTo("next");
};

WPADVERTS.GridGallery.prototype.IndexChanged = function(e) {
    this.current.text(this.slider.getInfo().displayIndex);
    this.PaginationVis();
    this.HandleCircles();
};

WPADVERTS.GridGallery.prototype.PaginationVis = function() {
    if(this.slider.getInfo().displayIndex === 1) {
        this.prev.hide();
    } else {
        this.prev.show();
    }

    if(this.slider.getInfo().displayIndex === this.slider.getInfo().slideCount) {
        this.next.hide();
    } else {
        this.next.show();
    }
};

WPADVERTS.GridGallery.prototype.HandleCircles = function() {
    if(this.circles.length === 0) {
        return;
    }

    this.circles.removeClass("wpa-circle-current");
    this.circles.removeClass("wpa-circle");
    this.circles.removeClass("wpa-circle-sm");
    this.circles.removeClass("wpa-circle-xs");

    var index = this.slider.getInfo().displayIndex;


    jQuery.each(this.circles, function(i, item) {
        var $item = jQuery(item);
        var distance = (i+1) - index;
        var viewport = 3;
        var visible = [];

        for(var j=0; j<viewport; j++) {
            visible.push(index+j);
        }

        console.log(visible);
        $item.addClass("wpa-circle");

        if(distance == 0) {
            // current
            $item.addClass("wpa-circle-current");
            
        }

        

        /*
        if(distance == 0) {
            // current
            $item.addClass("wpa-circle-current");
            
        } else if(distance == -1 || distance == 1) {
            // next to the current
            $item.removeClass("wpa-circle-current");
            $item.addClass("wpa-circle");
        } else if(distance == -2 || distance == 2) {
            $item.removeClass("wpa-circle-current");
            $item.addClass("wpa-circle-sm");
            // small
        } else if(distance == -3 || distance == -3) {
            $item.removeClass("wpa-circle-current");
            $item.addClass("wpa-circle-xs");
        } else {
            $item.removeClass("wpa-circle-current");
            $item.addClass("wpa-circle-xs");
        }
            */
    });

};

jQuery(function($) {
    if($(".wpa-grid-gallery-slider-wrap").length > 0) {
        $(".wpa-grid-gallery-slider-wrap").each(function(index, item) {
            new WPADVERTS.GridGallery(item);
        });
    }
});