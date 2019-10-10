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

//Postcode Formatting
function formatPostcode(postcode) {
    var pCode = postcode.replace(/[^0-9a-zA-Z]/g, "").toUpperCase();
    var len = pCode.length;
    
    if(len == 5) {
       pCode = pCode.slice(0, 2) + " " + pCode.slice(2, 5);
    }
    else if(len == 6) {
       pCode = pCode.slice(0, 3) + " " + pCode.slice(3, 6);
    }
    else if(len == 7) {
       pCode = pCode.slice(0, 4) + " " + pCode.slice(4, 7);
    }
    else if(len == 8) {
       pCode = pCode.slice(0, 5) + " " + pCode.slice(5, 8);
    }
    
    return pCode;
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
    
    //Navigation Open Media Browser
    $("#adminNav #mediaBrowser").click(function() {
        moxman.browse({
            path: "<?php echo ROOT_DIR; ?>useruploads/",
        });
    })
});

$(window).resize(function() {
    pageHeight();
    formWidth();
});