$("#generalSettings input[type='submit']").click(function() {
    event.preventDefault();
    
    var homepage = $("#generalSettings select[name='homepage']").val();
    
    if($("#generalSettings input[name='hidePosts']").is(":checked")) {
        var hidePosts = 1;
    }
    else {
        var hidePosts = 0;
    }
    
    $.ajax({
        url: "scripts/generalSettings.php",
        method: "POST",
        dataType: "json",
        data: ({homepage, hidePosts}),
        success: function(data) {
            $("#generalSettings .message").text(data);
        }
    });
});

//Add Post Type
$("#customPosts input[name='addRow']").click(function() {
    $("#customPosts table").append(
        "<tr>" + 
            "<td><input type='text' name='postTypeName' placeholder='Name'></td>" + 
            "<td>" +
                "<select name='postTypeType'>" +
                    "<option value=''>Standard</option>" +
                    "<option value='product'>Product</option>" +
                "</select>" +
            "</td>" + 
            "<td></td>" +
        "</tr>"
    );
});

//Update Post Types
$("#customPosts input[type='submit']").click(function() {
    event.preventDefault();
    
    var i = 1;
    
    $("#customPosts .message").text("");
    
    $("#customPosts table tr:not(.headers)").each(function() {
        var name = $(this).find("input[name='postTypeName']").val();
        var type = $(this).find("select[name='postTypeType']").val();
        
        if(name == "") {
            $("#customPosts .message").append("Row " + i + " skipped, missing name.<br>");
        }
        else {
            $.ajax({
                url: "scripts/updateCustomPosts.php",
                method: "POST",
                dataType: "json",
                data: ({name, type}),
                success: function(data) {
                    $("#customPosts .message").append(data);
                }
            });
        }
        
        i++;
    });    
});

//Delete Post Type
$("#customPosts").on("click", "input[name='delete']", function() {
    var row = $(this).closest("tr");
    var name = $(this).closest("tr").find("input[name='postTypeName']").val();
    
    if(name == '') {
        $("#customPosts .message").text("Cannot delete empty post type.");
        
        return;
    }
    
    if(confirm("Are you sure you want to delete this post type? All associated posts and categories will be removed.")) {
        $.ajax({
            url: "scripts/deleteCustomPosts.php",
            method: "POST",
            dataType: "json",
            data: ({name}),
            success: function(data) {
                $("#customPosts .message").text(data[1]);

                if(data[0] == 1) {
                    row.remove();
                }
            }
        });
    }
});
