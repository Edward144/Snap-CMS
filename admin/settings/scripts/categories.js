//Change Post Type
$("select[name='categoryType']").on("change", function() {
    var url = window.location.href;
    var postType = $(this).val();
    var urlCount = url.split("://")[1].split("/").length;
    
    if(urlCount == 3) {
        window.location.replace(url + "/" + postType);
    }
    else if(urlCount >= 4) {
        window.location.replace(postType);
    }   
});

//Browse Images
$("input[name='catImageSelect']").click(function() {
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        oninsert: function(args) {
            var image = args.files[0].url;
            
            $("input[name='catImage']").val(image);
        }
    });
});

//Add Category
$("#createCategory input[type='submit']").click(function() {
    event.preventDefault();
    
    var postType = $("#createCategory input[name='catPostType']").val();
    var name = $("#createCategory input[name='catName']").val();
    var desc = $("#createCategory input[name='catDesc']").val();
    var image = $("#createCategory input[name='catImage']").val();
    var parent = $("#createCategory select[name='catParent']").val();
    
    if(name == "") {
        $("#createCategory .message").text("Name is required.")
        
        return;
    }
    
    $.ajax({
        url: "/admin/settings/scripts/addCategory.php",
        method: "POST",
        dataType: "json",
        data: ({name, desc, image, parent, postType}),
        success: function(data) {
            if(data[0] == 1) {
                location.reload();
            }
            else {
                $("#createCategory .message").text(data);
            }
        }
    });
});

//Delete Category
$("input[name='deleteCategory']").click(function() {
    id = $(this).attr("id").split("category")[1];
    
    if(confirm("Are you sure you want to delete this category?")) {
        $.ajax({
            url: "/admin/settings/scripts/deleteCategory.php",
            method: "POST",
            dataType: "json",
            data: ({id}),
            success: function(data) {
                if(data[0] == 1) {
                    location.reload();
                }
                else {
                    alert(data[1]);
                }
            }
        });  
    }
});

//Go To Edit Form
$("input[name='editCategory']").click(function() {
    var url = window.location.href;
    var postType = $(this).val();
    var urlCount = url.split("://")[1].split("/").length;
    var id = $(this).attr("id").split("category")[1];
    
    if(urlCount == 3) {
        window.location.replace("?id=" + id);
    }
    else if(urlCount >= 4) {
        window.location.replace(url + "&id=" + id);
    }     
});

//Edit Category
$("#updateCategory input[type='submit']").click(function() {
    event.preventDefault();
    
    var id = $("#updateCategory input[name='catId']").val();
    var postType = $("#updateCategory input[name='catPostType']").val();
    var name = $("#updateCategory input[name='catName']").val();
    var desc = $("#updateCategory input[name='catDesc']").val();
    var image = $("#updateCategory input[name='catImage']").val();
    var parent = $("#updateCategory select[name='catParent']").val();
    
    if(name == "") {
        $("#updateCategory .message").text("Name is required.")
        
        return;
    }
    
    $.ajax({
        url: "/admin/settings/scripts/updateCategory.php",
        method: "POST",
        dataType: "json",
        data: ({id, name, desc, image, parent, postType}),
        success: function(data) {
            if(data[0] == 1) {
                window.location.replace("./" + postType);
            }
            else {
                $("#updateCategory .message").text(data);
            }
        }
    });
});