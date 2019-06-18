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
            "<td><input type='button' class='badButton' name='delete' value='Delete'></td>" + 
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
        
        if(name == "") {
            $("#customPosts .message").append("Row " + i + " skipped, missing name.<br>");
        }
        else {
            $.ajax({
                url: "scripts/updateCustomPosts.php",
                method: "POST",
                dataType: "json",
                data: ({name}),
                success: function(data) {
                    $("#customPosts .message").append(data);
                }
            });
        }
        
        i++;
    });    
});