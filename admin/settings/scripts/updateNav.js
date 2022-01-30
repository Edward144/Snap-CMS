var pageSelectHTML;
var j;

$(document).ready(function() {
    pageSelectHTML = $("#pageSelectorMain").html();
    j = 1;
    
    $("#navLayout .level .pageSelector").each(function() {
        $(this).attr("id", "parent" + j);
        
        j++;
    });
});

//Add New Navigation Item
$("#navLayout").on("click", "input[name='addNext']", function() {
    var thisButton = $(this).closest("li");
    var i = parseInt($(this).closest(".level").attr("id").split("level")[1]) + 1;
    
    $(pageSelectHTML).insertBefore(thisButton);
    
    $(this).closest(".level").find(".level").attr("id", "level" + i);
    $(this).closest("#levelAddition").prev(".pageSelector").attr("id", "parent" + j);
    
    i++;
    j++;
});

//Delete Navigation Item
$("#navLayout").on("click", "input[name='delete']", function() { 
    $(this).closest(".pageSelector").remove();
});

//Update Naviagation
$("#navLayout input[type='submit']").click(function() {
    event.preventDefault();
    
    var levels = [];
    
    $("#navLayout .message").text("");
    
    $(".level").each(function() {
        var level = $(this).attr("id").split("level")[1];
        
        if(levels.includes(level) == false) {
            levels.push(level);
        }
    });
    
    $.ajax({
        url: "settings/scripts/updateNav.php",
        method: "POST",
        dataType: "json",
        data: ({truncate: 1}),
        success: function(data) {
            if(levels.length > 0) {
                $("#navLayout .message").append(data + "<br>");

                levels.forEach(function(level) {
                    var position = 0;
                    
                    $("#level" + level + " > li:not(#levelAddition)").each(function() {
                        var pageId = $(this).find("select[name='pages']").val();
                        var prevLevel = parseInt(level) - 1;
                        var customName = $(this).find("input[name='customNav']").val();
                        var customUrl = $(this).find("input[name='customUrl']").val();
                        var parentId = $(this).parents(".pageSelector").attr("id");
                        var customId = $(this).attr("id").split("parent")[1];
                        
                        if(parentId == undefined && level == 0) {
                            parentId = 0;
                        }
                        else {
                            parentId = parentId.split("parent")[1];
                        }
                        
                        position++;

                        $.ajax({
                            url: "settings/scripts/updateNav.php",
                            method: "POST",
                            dataType: "json",
                            data: ({level, pageId, parentId, position, customName, customId, customUrl}),
                            success: function(data) {
                                $("#navLayout .message").append(data + "<br>");
                            }
                        });
                    });
                });
            }
        }
    });
});