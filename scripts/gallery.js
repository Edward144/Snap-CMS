$(".gallery.owl-carousel").owlCarousel({
    responsive: {
        0 : {
            items: 3
        },
        600: {
            items: 5
        },
        1000: {
            items: 10
        }
    },
    rtl: true,
    margin: 10
});

var active = false;

$(".gallery img").click(function() {                                
    var src = $(this).attr("src");

    if(active == false && src != $(".heroImage").attr("src")) {
        active = true;

        if($("#heroBlur").length > 0) {
            $("#heroBlur").animate({
                "opacity" : 0
            }, 350, function() {
                $("#heroBlur").remove();

                changeImage(src);
            });
        }
        else {
            changeImage(src);
        }

    }
    else {
        return;
    }
});

function changeImage(src) {
    $(".heroImage").after("<img class='heroImage' src='" + src + "' style='z-index: -1; position: absolute; top: 0; left: 0; right: 0; bottom: 0;'>");

    $(".hero").append("<div id='heroBlur' style='opacity: 0; position: absolute; width: 100%; height: 100%; top: 0; left: 0; right: 0; bottom: 0; backdrop-filter: blur(10px);'><img src='" + src + "' style='position: absolute; top: 0; bottom: 0; left: 0; right: 0; object-fit: contain; width: 100%; height: 100%;'></div>");

    $(".heroImage:first-child").animate({
        "opacity" : "0" 
    }, 1000, function() {
        $(".heroImage:first-child").remove();
        $(".heroImage").css("z-index", "");

        active = false;
        console.log(active);
    });

    $("#heroBlur").animate({
        "opacity" : 1
    }, 1000);
}