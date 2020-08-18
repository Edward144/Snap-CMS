//Toggle Sidebar
$("#sidebarToggle").click(function() {
    var wrapper = $(this).parents(".wrapper").first();

    if(wrapper.attr("id") == "sidebarClosed") {
        wrapper.attr("id", "");
    }
    else {
        wrapper.attr("id", "sidebarClosed");
    }
});

function setSidebarState(initialLoad) {
    if($(window).width() < 1200) {
        $(".wrapper").attr("id", "sidebarClosed");

        if(initialLoad == 1) {
            $(".sidebar, .content").addClass("noTransition");

            setTimeout(function() {
                $(".sidebar, .content").removeClass("noTransition");
            }, 500);
        }
    }
    else {
        $(".wrapper").attr("id", "");
    }
}

$(document).ready(function () {                
    setSidebarState(1);
});

$(window).resize(function() {
    setSidebarState(0);
});

//Sidebar Tooltips            
function showTooltip(item) {
    var offset = item.offset();
    var width = item.width();
    var height = item.height();
    
    if(!item.find(".ttip").length) {
        $('<span class="ttip">' + item.text() + '</span>').appendTo(item);
    }
    
    var offsetY = offset.top + (height / 2) - (item.find(".ttip").height() / 2);

    item.find(".ttip").css("transform", "translate(" + width + "px, " + offsetY + "px)");
}

$(".sidebar li").hover(function() {
    showTooltip($(this))
});

$(".sidebar li").on("touchstart", function() {
    showTooltip($(this))
});

$(".sidebar").scroll(function() {
    $(this).find(".ttip").remove();
});

$(function() {
    $("[data-toggle='tooltip']").tooltip();
});