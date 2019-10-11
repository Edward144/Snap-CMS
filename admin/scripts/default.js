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
        var input = $(this).find("input:not([type='button']):not([type='submit']), select, textarea");
        
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

//Update Visibility
function changeVisibility(btn, tableName) {
    var id = btn.attr("data-id");
    var name = btn.attr("name");
    var value = btn.val();
    
    if(btn.attr("name") == "hide") {
        var visibility = 0;
        name = "show";
        value = "Hidden";
    }
    else if(btn.attr("name") == "show") {
        var visibility = 1;
        name = "hide";
        value = "Visible";
    }
    
    $.ajax({
        url: root_dir + "admin/scripts/changeVisibility.php",
        method: "POST",
        dataType: "json",
        data: ({id, visibility, tableName}),
        success: function(data) {
            if(data == 1) {            
                btn.attr("name", name);
                btn.val(value);
            }
        }
    });
}

//Delete Content
function deleteContent(btn, tableName) {
    var id = btn.attr("data-id");
    
    if(confirm("Are you sure you want to delete this item?")) {
        $.ajax({
            url: root_dir + "admin/scripts/deleteContent.php",
            method: "POST",
            dataType: "json",
            data: ({id, tableName}),
            success: function(data) {
                if(data == 1) {
                    window.location.reload();
                }
                else {
                    alert("Error: Item could not be deleted");
                }
            }
        });
    }
}

//Edit Content
function editContent(btn, tableName) {
    var id = btn.attr("data-id");
    var cleanUrl = window.location.href.split("/page-")[0];
        cleanUrl = cleanUrl.split("/category-")[0];
        cleanUrl = cleanUrl.split("/id-")[0];
    
    window.location.href = cleanUrl + "/id-" + id;
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
            skin: "snapcms"
        });
    })
});

$(window).resize(function() {
    pageHeight();
    formWidth();
});