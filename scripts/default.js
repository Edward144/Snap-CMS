//Check For Retina Display
function isRetina() {
    if(window.devicePixelRatio >= 1.5) {
        return 1;
    }
    else {
        return 0;
    }
}

//Replace Images with Retina Versions If They Exist
/*$(document).ready(function() {
    console.log("Attempting to replace images with retina versions, please ignore 404s");
    
    var retina = isRetina();
    
    if(retina == 0) {
        $("img").each(function() {
            var standardImg = $(this).attr("src");
            var thisImg = $(this);
            var retinaImg = "";

            $(this).attr("src", function(x, src) {
                var lastPeriod = src.lastIndexOf(".");
                var extension = src.substr(lastPeriod);
                var path = src.substr(0, lastPeriod);

                retinaImg = path + "@2x" + extension;
            });

            $.ajax({
                url: retinaImg,
                data: ({standardImg, retinaImg}),
                success: function(data) {
                    thisImg.attr("src", retinaImg.split("?")[0]);
                }
            });
        });
    }
});*/

//Force Page Height
function pageHeight() {
    setTimeout(function() {
        var headerH = $("#header").outerHeight();
        var footerH = $("#footer").outerHeight();
        var totalH = $(window).height() - footerH;

        $(".main").css({
            "min-height" : totalH
        });
        
        $(".main").css("padding-top", headerH);
        //$(".main .hero").css("margin-top", -headerH);
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

$(".expander").click(function() {
    if($(this).attr("id") == "open") {
        $(this).attr("id", "closed");
        $(this).closest("li").find(".bottom").attr("id", "");
    }
    else {
        $(this).attr("id", "open");
        $(this).closest("li").find(".bottom").attr("id", "expanded");
    }
});

$(document).ready(function() {
    pageHeight();
    
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
});