//Force Page Height
function pageHeight() {
    setTimeout(function() {
        var headerH = $("#header").outerHeight();
        var footerH = $("#footer").outerHeight();
        var totalH = $(window).height() - (headerH + footerH);

        $(".main").css({
            "min-height" : totalH
        });
    }, 50);
}

//Open Close Hamburger Menu
function hamburgerMenu() {
    $(".navigation#menu0").css({
        "height" : ""
    });
    
    var oHeight = $(".navigation#menu0").outerHeight();
    
    if($(this).attr("id") == "hidden") {
        $(this).attr("id", "visible");  
        
        $(".navigation#menu0").css({
            "height" : "0",
            "display" : "block",
            "overflow" : "visible"
        });
        
        $(".navigation#menu0").animate({
            "min-height" : oHeight,
            "height" : ""
        }, 350);
    }
    else {
        $(this).attr("id", "hidden");
        $(".navigation#menu0 div").removeClass("active");
        $(".navigation#menu0 .hasChildren > a").removeClass("active");
        
        $(".navigation#menu0").css({
            "overflow" : "hidden"
        });
        
        $(".navigation#menu0").animate({
            "height" : "0",
            "min-height" : "0"
        }, 500);
    }
}

$(window).resize(function() {
    $(".navigation#menu0 div").removeClass("active");
    
    if($(".navToggle").attr("id") == "visible") {
        $(".navigation#menu0").animate({
            "height" : "0",
            "min-height" : "0"
        }, 500, function() {
            $(".navigation#menu0").css({
                "height" : "",
                "min-height" : "",
                "display" : ""
            });
        });

        $(".navToggle").attr("id", "hidden");
    }
    else {
        $(".navigation#menu0").css({
            "height" : "",
            "min-height" : "",
            "display" : ""
        });
    }
});

//Home Blocks Size
function squareBlock(element) {
    setTimeout(function() {
        height = 0;

        $(element).each(function() {
            if($(this).outerWidth() > height) {
                height = $(this).outerWidth();
            }
        });

        $(element).css("height", height);
        
    }, 100);
}

//Set Elements To Same Height
function matchHeight(element) {
    setTimeout(function() {    
        $(element).css("height", "");

        height = 0;
        
        $(element).each(function() {
            if($(this).outerHeight() > height) {
                height = $(this).outerHeight();
            }
        });

        $(element).css("height", height);
        
    }, 100);
}

$(document).ready(function() {
    pageHeight();
    matchHeight(".listItem");
    
    $(".navToggle").click(hamburgerMenu);
    
    //Expand Hamburger Sub Menus
    $(".navigation#menu0 .hasChildren > a > span").click(function() {    
        if($(window).width() <= 1024) {
            event.preventDefault();

            var submenu = $(this).closest(".hasChildren").find("div").first();

            submenu.toggleClass("active");
            $(this).closest("a").toggleClass("active");
        }
    });
});

$(window).resize(function() {
    pageHeight();
    matchHeight(".listItem");
});