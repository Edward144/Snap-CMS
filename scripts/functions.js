//Toggle Sidebar
$(".sidebarToggle").click(function() {    
    var sidebarWidth = $(".sidebar").outerWidth();
    
    if($(this).attr("id") == "hidden") {
        $(".sidebarToggle").attr("id", "visible");
        
        $(".sidebar").animate({
           "left" : "0" 
        });
        
        $(".sidebarToggle").animate({
            "right" : ""
        });
    }
    else {
        $(".sidebarToggle").attr("id", "hidden");
        
        $(".sidebar").animate({
           "left" : "-" + sidebarWidth + "px" 
        });
        
        $(".sidebarToggle").animate({
            "right" : "-51px"
        });
    }
});

//Hide Sidebar On Resize
$(window).resize(function() {
    $(".sidebarToggle").attr("id", "hidden");
    
    $(".sidebarToggle").css({
       "right" : "" 
    });
    
    $(".sidebar").css({
        "left" : ""
    });
});

//Sidebar Submenu Toggle
$(".sidebarCategory > a").click(function() {
    event.preventDefault();
    
    if($(this).attr("id") == "hidden") {
        $(this).attr("id", "visible");
        
        $(this).closest(".sidebarCategory").find("ul.sub").css({
           display: "block" 
        });
    }
    else {
        $(this).attr("id", "hidden");
        
        $(this).closest(".sidebarCategory").find("ul.sub").css({
           display: "" 
        });
    }
});

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

//Set Page Height
function setPageHeight() {
    var headerH = $("header").outerHeight();
    var footerH = $("footer").outerHeight();
    var windowH = $(window).height();
    var mainMargin = 16;
    
    var totalH = windowH - (headerH + footerH) - mainMargin;
    
    $(".main").css("min-height", totalH);
}

//Set Sidebar Visibility
function setSidebarVisibility() {          
    if($.trim($(".sidebarInner").html()).length == 0) {
        $(".sidebar").css("display", "none");
        $(".main .content").css("width", "100%");
    }
}

//Side Options Show/Hide
$(".sideOptions .sideOptionInner").click(function() {
    return;
});
    
$(".sideOptions").on("click", "li#active > h3", function() {
    $(".sideOptions li").attr("id", "inactive");
});

$(".sideOptions").on("click", "li#inactive", function() {
    $(".sideOptions li").attr("id", "inactive");
    $(this).attr("id", "active");
});