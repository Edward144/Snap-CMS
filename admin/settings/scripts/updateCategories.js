var pageSelectHTML;
var j;

$(document).ready(function() {
    pageSelectHTML = $("#catSelectorMain").html();
    j = 1;
    
    $("#catLayout .level .catSelector").each(function() {
        $(this).attr("id", "parent" + j);
        
        j++;
    });
});

//Add New Category
$("#catLayout").on("click", "input[name='addNext']", function() {
    var thisButton = $(this).closest("li");
    var i = parseInt($(this).closest(".level").attr("id").split("level")[1]) + 1;
    
    $(pageSelectHTML).insertBefore(thisButton);
    
    $(this).closest(".level").find(".level").attr("id", "level" + i);
    $(this).closest("#levelAddition").prev(".catSelector").attr("id", "parent" + j);
    
    i++;
    j++;
});

//Delete Category
$("#catLayout").on("click", "input[name='delete']", function() { 
    $(this).closest(".catSelector").remove();
});

//Update Category
$("#catLayout input[type='submit']").click(function() {
    event.preventDefault();
    
    var levels = [];
    
    $("#catLayout .message").text("");
    
    $(".level").each(function() {
        var level = $(this).attr("id").split("level")[1];
        
        if(levels.includes(level) == false) {
            levels.push(level);
        }
    });
    
    $.ajax({
        url: "settings/scripts/updateCategories.php",
        method: "POST",
        dataType: "json",
        data: ({truncate: 1}),
        success: function(data) {
            if(levels.length > 0) {
                $("#catLayout .message").append(data + "<br>");

                levels.forEach(function(level) {
                    var position = 0;
                    
                    $("#level" + level + " > li:not(#levelAddition)").each(function() {
                        var name = $(this).find("input[name='catName']").val();
                        var prevLevel = parseInt(level) - 1;
                        var description = $(this).find("input[name='catDesc']").val();
                        var image = $(this).find("input[name='catImage']").val();
                        var parentId = $(this).parents(".catSelector").attr("id");
                        var customId = $(this).attr("id").split("parent")[1];
                        
                        if(parentId == undefined && level == 0) {
                            parentId = 0;
                        }
                        else {
                            parentId = parentId.split("parent")[1];
                        }
                        
                        position++;

                        $.ajax({
                            url: "settings/scripts/updateCategories.php",
                            method: "POST",
                            dataType: "json",
                            data: ({level, name, parentId, position, description, customId, image}),
                            success: function(data) {
                                $("#catLayout .message").append(data + "<br>");
                            }
                        });
                    });
                });
            }
        }
    });
});