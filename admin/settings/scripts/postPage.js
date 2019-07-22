//Add New Content
$(".addContent input[type='submit']").click(addContent);

function addContent() {
    event.preventDefault();
    
    var type = $(this).closest("form").attr("id").split("add")[1].toLowerCase();
    
    $.ajax({
        url: "/admin/settings/scripts/addPostPage.php",
        method: "GET",
        dataType: "json",
        data: ({type}),
        success: function(data) {
            if(data[0] == 1) {
                window.location.href = "?p=" + data[1];
            }
            else {
                alert(data[1]);
            }
        }
    });
}

//Search Content
$("#searchPost input[name='search']").on("keyup", searchContent);
$("#searchPage input[name='search']").on("keyup", searchContent);

function searchContent() {
    var searchTerm = $(this).val();
    var type = $(this).closest("form").attr("id").split("search")[1].toLowerCase();
    var limit = $(this).attr("id");
    
    $.ajax({
        url: "/admin/settings/scripts/searchPostPage.php",
        method: "GET",
        dataType: "json",
        data: ({searchTerm, type, limit}),
        success: function(data) {
            $(".content table").html(data);
        }
    });
}

//Change Content Visibility
$(".content table").on("click", ".contentRow #view", changeVisibility);
$(".content table").on("click", ".contentRow #hide", changeVisibility);
$(".actions #view").click(changeVisibility);
$(".actions #hide").click(changeVisibility);
    
function changeVisibility() {
    var icon = $(this);
    var row = icon.closest(".contentRow");
    
    if(row.length <= 0) {
        var id = $(".details .id").text();
        var type = $(".contentWrap").attr("class").split(" ")[0];
    }
    else {
        var id = row.find(".id").text();
        var type = row.attr("class").split("Row")[0];
    }
    
    var action = icon.attr("id");
    
    $.ajax({
        url: "/admin/settings/scripts/visibilityPostPage.php",
        method: "GET",
        dataType: "json",
        data: ({id, type, action}),
        success: function(data) {
            if(data == 0) {
                icon.find("img").attr("src", "/admin/images/icons/hide.png");
                
                icon.attr("id", "hide");
            }
            else {
                icon.find("img").attr("src", "/admin/images/icons/view.png");
                
                icon.attr("id", "view");
            }
        }
    });
}

//Edit Content
$(".content table").on("click", ".contentRow #edit", function() {
    var row = $(this).closest(".contentRow");
    var id = row.find(".id").text();
    var type = row.attr("class").split("Row")[0];
    
    window.location.href = "?p=" + id;
});

//Delete Content
$(".content table").on("click", ".contentRow #delete", deleteContent);
$(".actions #delete").click(deleteContent);

function deleteContent() {
    var icon = $(this);
    var row = icon.closest(".contentRow");
    
    if(row.length <= 0) {
        var id = $(".details .id").text();
        var type = $(".contentWrap").attr("class").split(" ")[0];
    }
    else {
        var id = row.find(".id").text();
        var type = row.attr("class").split("Row")[0];
    }
    
    if(confirm("Are you sure you want to delete this " + type + "?")) {
        $.ajax({
            url: "/admin/settings/scripts/deletePostPage.php",
            method: "GET",
            dataType: "json",
            data: ({id, type}),
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
}

//Delete Featured Image
$(".featuredImage").on("click", ".featuredDelete", function() {
    $(".featuredInner").html("<span>Select Image</span>");
    
    $(".featuredInner").addClass("noFeatured");
});

//Find Featured Image
$(".featuredImage").on("click", ".noFeatured", showMedia);
$(".featuredImage").on("click", "#featuredImage", showMedia);

function showMedia() {
    $(".featuredImage .mediaList.popup").attr("id", "");
}

$(".mediaList.popup .mediaFile").click(function() {
    var extensions = ["jpg", "png", "gif", "svg", "webp"];
    
    if(!$.inArray(copyFile.split(".")[1], extensions)) {
        $(".featuredInner").html(
            "<span class='featuredDelete'><span>X</span></span>" + 
            "<img src='" + copyFile + "' id='featuredImage'>"
        );
        
        $(".featuredInner").removeClass("noFeatured");
    }
    else {
        alert('This does not appear to be an image.');
        
        $(".mediaList.popup").attr("id", "");
    }
});

//Update Content
$("#editContent .actions #apply").click(function() {
    tinyMCE.triggerSave();

    if(productOptions == true) {
        var features = $("textarea[name='featuresOption']").val();
        var output = $("textarea[name='outputOption']").val();
        var spec = "";
        var galleryExist = "";
        var galleryNew = "";
        
        $(".specificationOption tr:not(.headers)").each(function() {
            spec += '\"' + $(this).find("input[name='specName']").val() + '\",\"' + $(this).find("input[name='specValue']").val() + '\";';
        });
        
        $(".galleryItems.current .galleryItem").each(function() {
            galleryExist += '\"' + $(this).find("img").attr("alt") + '\";';
        });
        
        $(".galleryItems.uploaded .galleryItem").each(function() {
            galleryNew += '\"' + $(this).find("img").attr("alt") + '\";';
        });
    }
    else {
        var features = "";
        var output = "";
        var spec = "";
        var galleryExist = "";
        var galleryNew = "";
    }
    
    var id = $("#editContent .id").text();
    var type = $(".contentWrap").attr("class").split(" ")[0];
    var title = $("#editContent input[name='title']").val();
    var desc = $("#editContent input[name='description']").val();
    var url = $("#editContent input[name='url']").val();
    var author = $("#editContent select[name='author']").val();
    var datetime = $("#editContent input[name='date']").val();
    var content = $("#editContent textarea[name='content']").val();
    var category = $("#editContent select[name='categories']").val();
    var imageUrl;
    
    if($(".featuredImage").find("#featuredImage").length) {
        imageUrl = $(".featuredImage").find("#featuredImage").attr("src");
    }
    
    if (title == "") {
        alert("Title is missing.");
        
        return;
    }
    
    if(url == "") {
        alert("Url is missing.");
        
        return;
    }
    
    if(author == null) {
        alert("Author is missing.");
        
        return;
    }
    
    if(datetime == "") {
        var date = new Date;
        var day = "" + date.getDate();
        var month = "" + (date.getMonth() + 1);
        var year = date.getFullYear();
        var hour = "" + date.getHours();
        var min = "" + date.getMinutes();
        var sec = "" + date.getSeconds();
        
        if(day.length < 2) {
            day = "0 "+ day;
        }
        
        if(month.length < 2) {
            month = "0" + month;
        }
        
        if(hour.length < 2) {
            hour = "0" + hour;
        }
        
        if(min.length < 2) {
            min = "0" + min;
        }
        
        if(sec.length < 2) {
            sec = "0" + sec;
        }
        
        var datetime = year + "-" + month + "-" + day + " " + hour + ":" + min + ":" + sec;
    }
    else {
        datetime = datetime.replace("T", " ");
    }
    
    $("#editContent .message").text("");
    
    $.ajax({
        url: "/admin/settings/scripts/editPostPage.php",
        method: "POST",
        dataType: "json",
        data: ({id, type, title, desc, url, author, datetime, content, category, imageUrl, spec, galleryExist, galleryNew, output, features}),
        success: function(data) {
            if(data[0] == 1) {
                $("#editContent .message").text(data[1]);
            }
            else {
                alert(data[1]);
            }
        }
    });
});