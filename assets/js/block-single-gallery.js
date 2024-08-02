"use strict"

class WPAdverts_Block_Single_Gallery {
    gallery;

    slider;
    slider_left;
    slider_left_btn;
    slider_right;
    slider_right_btn;
    slider_current;

    lightbox;

    nav;
    nav_container;
    nav_items;
    nav_left;
    nav_left_btn;
    nav_right;
    nav_right_btn;

    pos = { top: 0, left: 0, x: 0, y: 0 };
    listeners = {};



    constructor(gallery) {
        this.gallery = gallery;
        this.nav = gallery.querySelector(".wpa-block-gallery-nav");
        this.nav_container = gallery.querySelector(".wpa-block-gallery-nav-container");
        this.nav_left = gallery.querySelector(".wpa-block-gallery-nav-left");
        this.nav_left_btn = gallery.querySelector(".wpa-block-gallery-nav-left-btn");
        this.nav_right = gallery.querySelector(".wpa-block-gallery-nav-right");
        this.nav_right_btn = gallery.querySelector(".wpa-block-gallery-nav-right-btn");
        this.nav_items = gallery.querySelectorAll(".wpa-block-gallery-nav-item");

        this.nav_container.addEventListener('mousedown', (e) => this.mouseDownHandler(e));
        this.nav_container.addEventListener("scroll", (e) => this.onScroll(e));
        this.nav_right_btn.addEventListener("click", (e) => this.onRightBtnClick(e));
        this.nav_left_btn.addEventListener("click", (e) => this.onLeftBtnClick(e));

        for(let i=0; i<this.nav_items.length; i++) {
            this.nav_items[i].addEventListener("click", (e,i) => this.onNavItemClick(e, i));
        }

        if(this.nav_items.length > 0) {
            this.nav_items[0].classList.add("wpa-current");
        }

        this.toggleNavInterface();

        this.slider = tns({
            container: ".wpa-block-gallery-slider",
            items: 1,
            slideBy: 1,
            autoplay: false,
            mouseDrag: true,
            center: false,
            controls: false,
            nav: false,
            lazyload: this.gallery.querySelector(".wpa-block-gallery-slider").classList.contains("wpa-slider-is-lazy"),
            lazyloadSelector: ".wpa-lazy-img"
        });
        this.slider_current = gallery.querySelector(".wpa-block-gallery-current");
        this.slider_left = gallery.querySelector(".wpa-block-gallery-left")
        this.slider_left_btn = gallery.querySelector(".wpa-block-gallery-left-btn");
        this.slider_right = gallery.querySelector(".wpa-block-gallery-right")
        this.slider_right_btn = gallery.querySelector(".wpa-block-gallery-right-btn");
        this.slider.events.on("indexChanged", (e) => this.onSliderIndexChanged(e));

        this.slider_right_btn.addEventListener("click", (e) => this.onSliderRightBtnClick(e));
        this.slider_left_btn.addEventListener("click", (e) => this.onSliderLeftBtnClick(e));

        this.lightbox = GLightbox({
            selector: ".wpa-glightbox"
        });

        this.lightbox.on("slide_changed", ({ prev, current }) => this.onSlideChange(prev,current) );

        this.sliderIndexChange(1);

    }

    onSlideChange(prev,current) {
        this.slider.goTo(current.index-1);
    }

    mouseDownHandler = function (e) {
        //this.nav_container.style.cursor = 'grabbing';
        this.nav_container.style.userSelect = 'none';

        this.pos = {
            left: this.nav_container.scrollLeft,
            top: this.nav_container.scrollTop,
            // Get the current mouse position
            x: e.clientX,
            y: e.clientY,
        };

        this.listeners.mouseMoveHandler = (e) => this.mouseMoveHandler(e);
        this.listeners.mouseUpHandler = (e) => this.mouseUpHandler(e);

        document.addEventListener('mousemove', this.listeners.mouseMoveHandler);
        document.addEventListener('mouseup', this.listeners.mouseUpHandler);
    }

    mouseMoveHandler = function (e) {
        // How far the mouse has been moved
        const dx = e.clientX - this.pos.x;
        const dy = e.clientY - this.pos.y;

        // Scroll the element
        this.nav_container.scrollTop = this.pos.top - dy;
        this.nav_container.scrollLeft = this.pos.left - dx;
    }

    mouseUpHandler = function () {
        //this.nav_container.style.cursor = 'grab';
        this.nav_container.style.removeProperty('user-select');

        document.removeEventListener('mousemove', this.listeners.mouseMoveHandler);
        document.removeEventListener('mouseup', this.listeners.mouseUpHandler);
    }

    onNavItemClick(e, index) {
        let goto = e.currentTarget.dataset.item;
        this.slider.goTo(goto);
    }

    onRightBtnClick(e) {
        e.preventDefault();
        this.scrollContainer(200);
    }

    onLeftBtnClick(e) {
        e.preventDefault();
        this.scrollContainer(-200);
    }

    onSliderLeftBtnClick(e) {
        e.preventDefault();
        this.slider.goTo("prev");
    }

    onSliderRightBtnClick(e) {
        e.preventDefault();
        this.slider.goTo("next");
    }

    scrollContainer(value) {
        this.nav_container.scrollBy(value, 0);
    }

    onScroll(e) {
        this.toggleNavInterface();
    }

    onSliderIndexChanged(e) {
        this.sliderIndexChange(e.displayIndex);
    }

    sliderIndexChange(index) {
        this.slider_current.textContent = index;

        if(index === 1) {
            this.slider_left.style.display = "none"
        } else {
            this.slider_left.style.display = "flex";
        }

        if(index == this.slider.getInfo().slideCount) {
            this.slider_right.style.display = "none";
        } else {
            this.slider_right.style.display = "flex";
        }

        for(let i=0; i<this.nav_items.length; i++) {
            if(index === i+1) {
                this.nav_items[i].classList.add("wpa-current");
            } else {
                this.nav_items[i].classList.remove("wpa-current");
            }
        } 
    }

    toggleNavInterface() {
        const nav = this.nav_container;
        
        const scrollEnd = nav.scrollLeft + nav.clientWidth >= nav.scrollWidth;
        
        if(scrollEnd) {
            this.nav_right.style.display = "none";
        } else {
            this.nav_right.style.display = "flex";
        }
    
        if(nav.scrollLeft === 0) {
            this.nav_left.style.display = "none";
        } else {
            this.nav_left.style.display = "flex";
        }
    }
}

class WPAdverts_Block_Single_Gallery_Lightbox_Interface {

}

 
document.addEventListener('DOMContentLoaded', function() {
    const block_gallery = document.querySelector(".wpa-block-gallery");
    if(block_gallery !== null) {
        new WPAdverts_Block_Single_Gallery(block_gallery);
    }
});
