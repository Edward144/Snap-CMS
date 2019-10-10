//Force Page Height
function pageHeight() {
    setTimeout(function() {
        var headerH = $("#adminHeader").outerHeight();
        var footerH = $("#adminFooter").outerHeight();
        var totalH = $(window).height() - (headerH + footerH);

        $(".adminWrap").css({
            "height" : totalH
        });
    }, 50);
}

//Open Close Hamburger Menu
function hamburgerMenu() {
    $("#adminNav").css({
        "height" : ""
    });
    
    var oHeight = $("#adminNav").outerHeight();
    
    if($(this).attr("id") == "hidden") {
        $(this).attr("id", "visible");  
        
        $("#adminNav").css({
            "height" : "0",
            "display" : "block",
            "overflow" : "visible"
        });
        
        $("#adminNav").animate({
            "min-height" : oHeight,
            "height" : ""
        }, 500);
    }
    else {
        $(this).attr("id", "hidden");
        $("#adminNav .subMenu").removeClass("active");
        $("#adminNav .hasChildren > a").removeClass("active");
        
        $("#adminNav").css({
            "overflow" : "hidden"
        });
        
        $("#adminNav").animate({
            "height" : "0",
            "min-height" : "0"
        }, 500);
    }
}

$(window).resize(function() {
    $("#adminNav .subMenu").removeClass("active");
    
    if($(".navToggle").attr("id") == "visible") {
        $("#adminNav").animate({
            "height" : "0",
            "min-height" : "0"
        }, 500, function() {
            $("#adminNav").css({
                "height" : "",
                "min-height" : "",
                "display" : ""
            });
        });

        $(".navToggle").attr("id", "hidden");
    }
    else {
        $("#adminNav").css({
            "height" : "",
            "min-height" : "",
            "display" : ""
        });
    }
});

//Force Full Width Form When Too Small
function formWidth() {
    $(".formBlock").each(function() {
        var input = $(this).find("input:not([type='button']):not([type='submit'])");
        
        if($(this).outerWidth() <= 450) {
            input.css({
                "display" : "block",
                "width" : "100%"
            });
        }
        else {
            input.css({
                "display" : "",
                "width" : ""
            });
        }
    });
    
}

$(document).ready(function() {
    pageHeight(); 
    formWidth();
    
    $(".navToggle").click(hamburgerMenu);
    
    //Expand Hamburger Sub Menus
    $("#adminNav .hasChildren > a").click(function() {    
        if($(window).width() <= 1170) {
            event.preventDefault();

            var submenu = $(this).closest(".hasChildren").find(".subMenu").first();

            submenu.toggleClass("active");
            $(this).toggleClass("active");

            $(".submenu.active").css({
                "visiblility" : "visible",
                "opacity" : "1"
            });
        }
    });
});

$(window).resize(function() {
    pageHeight();
    formWidth();
});