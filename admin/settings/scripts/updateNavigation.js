var navId = 1;

$(document).ready(function() {
    $(".menuStructure .navItem").each(function() {
        if(parseInt($(this).attr("id").split("navItem")[1]) + 1 >= navId) {
            navId = parseInt($(this).attr("id").split("navItem")[1]) + 1;
        }
    });

    //Set Data Levels
    $(".menuStructure ul").each(function() {
        var parentUl = $(this).parents("ul").first();
        var level = parentUl.attr("data-level");

        if(level >= 0) {
            $(this).attr("data-level", parseInt(level) + 1);
        }

        if(level <= 3) {
            $(' <input type="button" name="addChild" value="Add Child" style="margin-left: 4px;">').insertAfter($(this).closest("li:not(#addNav)").find("> div > input[name='imageSearch']"));
        }
    });
});

//Add Top Level Menu Item
$(".menuStructure").on("click", "input[name='addNav']", function() {
    var thisLi = $(this).closest("li");
    var thisUl = $(this).closest("ul");
    var levelCount = 0;

    levelCount = thisUl.children("li:not(#addNav)").length;

    $.ajax({
        url: "settings/scripts/findPostTypes.php",
        method: "POST",
        dataType: "json",
        success: function(data) {
            $(
                "<li class='navItem' id='navItem" + navId + "'>" +
                    "<div>" +
                        "<span id='id'>" + navId + "</span> " +
                        "<span id='position'>" + levelCount + "</span>" +
                        "<select name='postTypes'>" +
                            "<option value='' selected disabled>--Select Page--</option>" +
                            "<option value='customUrl'>Custom Link</option>" +
                            data +
                        "</select> " + 
                        "<div class='hiddenValues' style='display: none; margin: 0.5em 0;'>Displayed Name: <input type='text' name='displayName'> " + 
                        "Link: <input type='text' name='postUrl'><br><br></div>" + 
                        "Image: <input type='text' name='image'> <input type='button' name='imageSearch' value='&#128269;' title='Search Image'>" +
                        " <input type='button' name='addChild' value='Add Child'>" +
                        " <input type='button' name='deleteItem' data-item='" + navId + "' value='Delete'>" +
                    "</div>" +
                    "<ul id='parent" + navId + "' data-level='1'>" +
                    "</ul>" +
                "</li>"
                ).insertBefore(thisLi);

                navId++;
        }
    });            
});

//Add Child Menu Item
$(".menuStructure").on("click", "input[name='addChild']", function() {
    var thisLi = $(this).closest("li");
    var thisUl = thisLi.find("ul").first();
    var levelCount = 0;
    var allowChildren = true;
    var parentLevel = parseInt(thisLi.find("ul").first().attr("data-level")) + 1;

    if(thisLi.find("ul").first().attr("data-level") >= 3) {                
        allowChildren = false;
    }

    levelCount = thisUl.children("li:not(#addNav)").length;

    $.ajax({
        url: "settings/scripts/findPostTypes.php",
        method: "POST",
        dataType: "json",
        success: function(data) {
            var htmlString = 
                "<li class='navItem' id='navItem" + navId + "'>" +
                    "<div>" +
                        "<span id='id'>" + navId + "</span> " +
                        "<span id='position'>" + levelCount + "</span>" +
                        "<select name='postTypes'>" +
                            "<option value='' selected disabled>--Select Page--</option>" +
                            "<option value='customUrl'>Custom Link</option>" +
                            data +
                        "</select> " + 
                        "<div class='hiddenValues' style='display: none; margin: 0.5em 0;'>Displayed Name: <input type='text' name='displayName'> " + 
                        "Link: <input type='text' name='postUrl'></div>" +
                        "Image: <input type='text' name='image'> <input type='button' name='imageSearch' value='&#128269;' title='Search Image'>";

                if(allowChildren == true) {
                    htmlString += 
                        " <input type='button' name='addChild' value='Add Child'>";
                }

            htmlString +=
                        " <input type='button' name='deleteItem' data-item='" + navId + "' value='Delete'>" +
                    "</div>" +
                    "<ul id='parent" + navId + "' data-level='" + parentLevel + "'>" +
                    "</ul>" +
                "</li>";

            $(htmlString).appendTo(thisLi.find("ul").first());

            navId++;
        }
    });            
});

//Delete Item
$(".menuStructure").on("click", "input[name='deleteItem']", function() {
    id = $(this).attr("data-item");

    if(confirm("Are you sure you want to delete this item? All child items will also be removed.")) {
        $(this).closest("li").remove();
    }
});

//Select Post
$(".menuStructure").on("change", "select[name='postTypes']", function() {
    var thisLi = $(this).closest("li");
    var postValues = $(this).val();

    if(postValues == "customUrl") {
        thisLi.find(".hiddenValues").first().css("display", "block");

        thisLi.find("input[name='displayName']").val("Item " + thisLi.attr("id").split("navItem")[1]);
        thisLi.find("input[name='postUrl']").val("/");
    }
    else if(postValues != null && postValues != "") {
        postValues = postValues.split(";");
            var postType = postValues[0];
            var postName = postValues[1];
            var postUrl  = postValues[2];

        thisLi.find(".hiddenValues").first().css("display", "none");

        thisLi.find("input[name='displayName']").val(postName);
        thisLi.find("input[name='postUrl']").val("/post-type/" + postType + "/" + postUrl);
    }
});

//Save Menu Structure
$("input[name='saveMenu']").click(function() {
    var menuArray = {};
    var menuId = $("select[name='menuSelect']").val();

    $(".menuStructure ul").each(function() {
        var parentId = $(this).attr("id").split("parent")[1];

        $(this).children("li:not(#addNav)").each(function() {
            var itemId = $(this).attr("id").split("navItem")[1]; 

            //Remove any items with no link and recount position from 0
            if($(this).find("input[name='displayName']").first().val() == "") {
                var i = 0;

                $(this).remove();

                $(".menuStructure ul#parent" + parentId).children("li:not(#addNav)").each(function() {
                    $(this).find("#position").first().text(i);

                    i++;
                });
            }
            else {
                menuArray[itemId] = {
                    "parent_id" : parentId,
                    "position" : $(this).find("#position").first().text(),
                    "page_url" : $(this).find("input[name='postUrl']").first().val(),
                    "display_name" : $(this).find("input[name='displayName']").first().val(),
                    "image_url" : $(this).find("input[name='image']").first().val()
                }
            }
        });
    });

    console.log("MENU: " + menuId);
    console.log(menuArray);

    $.ajax({
        url: "settings/scripts/updateNavigation.php",
        method: "POST",
        dataType: "json",
        data: ({menuArray, menuId}),
        success: function(data) {
            console.log(data);

            $(".menuStructure .message").text("Menu structure has been saved.");
        }
    });
});

//Change Menu
$("select[name='menuSelect']").on("change", function() {
    window.location.href = "?menu=" + $(this).val();
});

//Create New Menu
$("#createMenu input[type='submit']").click(function() {
    event.preventDefault();

    var menuName = $("#createMenu input[name='menuName']").val();

    if(menuName == "") {
        $("#createMenu .message").text("Menu name is required.");

        return;
    }

    $("#createMenu .message").text("");

    $.ajax({
        url: "settings/scripts/addNavMenu.php",
        method: "POST",
        dataType: "json",
        data: ({menuName}),
        success: function(data) {
            if(data[0] == 1) {
                window.location.href = '/admin/navigation?menu=' + data[1]; 
            }
            else {
                $("#createMenu .message").text(data[1]);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            alert(textStatus + " " + jqXHR.status + ": " + errorThrown);
        }
    });
});

//Search For Image 
$(".menuStructure").on("click", "input[name='imageSearch']", function() {
    var thisButton = $(this);
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        oninsert: function(args) {
            var image = args.files[0].url;

            thisButton.siblings("input[name='image']").val(image);
        }
    });
});