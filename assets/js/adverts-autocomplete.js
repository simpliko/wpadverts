jQuery(function($) {
    if($("input.adverts-autocomplete").length == 0) {
        return;
    }
    
    $("input.adverts-autocomplete").each(function(index, item) {
        var $this = $(item);
        var holder = $("<div></div>");
        holder.addClass("adverts-autocomplete-holder");

        $this.attr("autocomplete", "off");
        $this.after(holder);
        holder.append($this);

        var suggest = $("<div></div>");
        suggest.addClass("adverts-autocomplete-options");
        holder.append(suggest);
        
        
        $this.keyup(function(e) {
            
            var $this = $(this);
            

            var caret = adverts_autocomplete_caret_position($this.get(0));
            var text = $.trim(adverts_autocomplete_extract_text($this.val(), caret, $this.data("separator")));
            
            if(parseInt($this.data("start")) > text.length) {
                return;
            }
            
            var data = {
                action: $this.data("ajax-action"),
                text: text
            }
            
            if(WPAdvertsAcRequest) {
                WPAdvertsAcRequest.abort();
            }
            
            var $parent = $this.parent();
            var suggest = $parent.find(".adverts-autocomplete-options");
            
            var span = $("<span></span>");
            span.addClass("adverts-loader");
            span.addClass("adverts-icon-spinner");
            span.addClass("animate-spin");
            span.css("display", "inline");
            span.css("font-size", "32px");

            suggest.html(span);
            suggest.css("width", $this.outerWidth()-1);
            suggest.show();
            
            WPAdvertsAcRequest = $.ajax(adverts_autocomplete_lang.ajaxurl, {
                input: $this,
                data: data,
                dataType: 'json',
                type: 'post',
                success: function(response) {
                    
                    var $parent = $this.parent();
                    var suggest = $parent.find(".adverts-autocomplete-options");
                    
                    suggest.empty();
                    
                    if(response.length == 0) {
                        var sitem = $("<div></div>");
                        sitem.addClass("adverts-autocomplete-none");
                        sitem.html(adverts_autocomplete_lang.no_results);
                        suggest.append(sitem);
                    } else {
                        $.each(response, function(index, item) {
                            var sitem = $("<div></div>");
                            sitem.addClass("adverts-autocomplete-item");
                            sitem.data("value", item.value);
                            sitem.html(item.html);
                            sitem.click(function(e) {
                                var holder = $(this).closest(".adverts-autocomplete-holder");
                                var input = holder.find("input.adverts-autocomplete");
                                var value = $(this).data("value");

                                adverts_autocomplete_update_text(input, value)
                                


                                holder.find(".adverts-autocomplete-options").hide();

                            });
                            suggest.append(sitem);
                        });
                    }
                    
                    WPAdvertsAcRequest = null;
                }
            });
        });

    });
    
    $(document).mouseup(function(e) {
        var container = $(".adverts-autocomplete-options");

        if (container.length == 0) {
            return;
        }

        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.hide();
        }
    });
});

function adverts_autocomplete_extract_text(text, caret, sep) {
    var parsed = "";
    var cut = 0;
    
    parsed = text.substr(0, caret);
    cut = parsed.lastIndexOf(sep);
    
    if(cut >= 0) {
        parsed = parsed.substr(cut + sep.length);
    }
    
    return parsed;
}

//console.log("New York: " + adverts_autocomplete_extract_text("New York", 8, "; "));
//console.log("Chicago: " + adverts_autocomplete_extract_text("New York; Chicago", 17, "; "));
//console.log("Chicago: " + adverts_autocomplete_extract_text("New York; Chicago", 15, "; "));
//console.log("Kraków: " + adverts_autocomplete_extract_text("New York; Chicago; Kraków", 25, "; "));
//console.log("New York: " + adverts_autocomplete_extract_text("New York; Chicago; Kraków", 8, "; "));

function adverts_autocomplete_update_text(input, value) {
    
    var sep = "; ";
    var caret = adverts_autocomplete_caret_position(input.get(0));
    var parsed = "";
    
    var text = input.val();
    var text1 = text.substr(0, caret);
    var text2 = text.substr(caret);
    
    var cut1 = text1.lastIndexOf(sep);
    var cut2 = text2.indexOf(sep);
    
    if(cut1 >= 0) {
        parsed += text1.substr(0, cut1) + sep;
    }
    
    parsed += value + sep;
    
    if(cut2 >= 0) {
        parsed += text2.substr(cut2+sep.length);
    }
    
    input.val(parsed);
    input.focus();
}

function adverts_autocomplete_caret_position (oField) {

  // Initialize
  var iCaretPos = 0;

  // IE Support
  if (document.selection) {

    // Set focus on the element
    oField.focus();

    // To get cursor position, get empty selection range
    var oSel = document.selection.createRange();

    // Move selection start to 0 position
    oSel.moveStart('character', -oField.value.length);

    // The caret position is selection length
    iCaretPos = oSel.text.length;
  }

  // Firefox support
  else if (oField.selectionStart || oField.selectionStart == '0')
    iCaretPos = oField.selectionStart;

  // Return results
  return iCaretPos;
}

var WPAdvertsAcRequest = null;