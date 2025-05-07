"use strict";

class WPAdverts_Block_Author_Phone {

    xhr = null;

    constructor(wrap) {

        this.btn = wrap.querySelector(".wpa-reveal-btn");
        this.spinner = wrap.querySelector(".wpa-reveal-spinner");
        this.box = wrap.querySelector(".wpa-reveal-data");
        this.phoneNumber = wrap.querySelector(".wpa-reveal-phone-number");
        this.phoneCopy = wrap.querySelector(".wpa-phone-copy");
        this.phoneCall = wrap.querySelector(".wpa-phone-call");

        this.btn.addEventListener("click", (e) => this.onBtnClick(e));

        if(navigator.clipboard === undefined) {
            this.phoneCopy.classList.add("atw-hidden");
        } else {
            this.phoneCopy.addEventListener("click", (e) => this.onCopyClick(e));
        }
    }

    onBtnClick(e) {
        e.preventDefault();

        this.btn.classList.add("atw-hidden");
        this.spinner.classList.remove("atw-hidden");


        fetch(wpadverts_block_single_author.ajaxurl, {
            method: "POST",
            headers: {
                "content-type": "application/x-www-form-urlencoded"
            },
            //make sure to serialize your JSON body
            body: "action=adverts_show_contact&mode=block&id=" + this.btn.dataset.postid + "&security=" + this.btn.dataset.nonce
        })
        .then( (response) => { return response.json() })
        .then( (response) => this.onBtnClickLoad(response));
    }

    onBtnClickLoad(json) {

        this.spinner.classList.add("atw-hidden");
        this.box.classList.remove("atw-hidden");

        this.phoneNumber.textContent = json.data.adverts_phone.value;
        this.phoneCall.setAttribute("href", "tel:" + json.data.adverts_phone.value.replace(" ", "") );
    }

    onCopyClick(e) {
        e.preventDefault();

         // Copy the text inside the text field
        window.navigator.clipboard.writeText(this.phoneNumber.textContent);

    }
}

addEventListener("DOMContentLoaded", function() {

    let reveal_phone = document.querySelector(".wpa-block-contact-reveal-phone");
    if(reveal_phone !== null) {
        let wpadverts_block_author_phone = new WPAdverts_Block_Author_Phone(reveal_phone);
    }
});