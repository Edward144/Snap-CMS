//Add New Banner
$(".addContent input[type='submit']").click(addContent);

function addContent() {
    event.preventDefault();
    
    $.ajax({
        url: "/admin/settings/scripts/addBanner.php",
        method: "GET",
        dataType: "json",
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

//Search Banners
$("#searchBanner input[name='search']").on("keyup", searchContent);

function searchContent() {
    var searchTerm = $(this).val();
    var limit = $(this).attr("id");
    
    $.ajax({
        url: "/admin/settings/scripts/searchBanner.php",
        method: "GET",
        dataType: "json",
        data: ({searchTerm, limit}),
        success: function(data) {
            $(".content table").html(data);
        }
    });
}

//Change Banner Visibility
$(".content table").on("click", ".contentRow #view", changeVisibility);
$(".content table").on("click", ".contentRow #hide", changeVisibility);
$(".actions #view").click(changeVisibility);
$(".actions #hide").click(changeVisibility);
    
function changeVisibility() {
    var icon = $(this);
    var row = icon.closest(".contentRow");
    
    if(row.length <= 0) {
        var id = $(".details .id").text();
    }
    else {
        var id = row.find(".id").text();
    }
    
    var action = icon.attr("id");
    
    $.ajax({
        url: "/admin/settings/scripts/visibilityBanner.php",
        method: "GET",
        dataType: "json",
        data: ({id, action}),
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

//Edit Banner
$(".content table").on("click", ".contentRow #edit", function() {
    var row = $(this).closest(".contentRow");
    var id = row.find(".id").text();
    var type = row.attr("class").split("Row")[0];
    
    window.location.href = "?p=" + id;
});

//Delete Banner
$(".content table").on("click", ".contentRow #delete", deleteContent);
$(".actions #delete").click(deleteContent);

function deleteContent() {
    var icon = $(this);
    var row = icon.closest(".contentRow");
    
    if(row.length <= 0) {
        var id = $(".details .id").text();
    }
    else {
        var id = row.find(".id").text();
    }
    
    if(confirm("Are you sure you want to delete this banner?")) {
        $.ajax({
            url: "/admin/settings/scripts/deleteBanner.php",
            method: "GET",
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
}

//Update Banner
$("#editContent .actions #apply").click(function() {
    tinyMCE.triggerSave();
    
    var id = $.trim($("#editContent .id").text());
    var name = $("#editContent input[name='title']").val();
    var postType = $("#editContent select[name='postType']").val();
    var postName = $("#editContent select[name='postName']").val();
    var animIn = $("#editContent input[name='animationIn']").val();
    var animOut = $("#editContent input[name='animationOut']").val();
    var speed = $("#editContent input[name='speed']").val() * 1000;
    
    if(name == null || name == "") {
        $("#editContent .message").text("You must enter a name for this banner.");
        
        return;
    }
    
    if(postType == null || postType == "") {
        $("#editContent .message").text("You must select a post type.");
        
        return;
    }
    
    if(postName == null || postName == "") {
        $("#editContent .message").text("You must select a post name.");
        
        return;
    }
    
    if(speed < 0 || speed > 60000) {
        $("#editContent .message").text("Speed must be between 1 and 60 seconds. Or 0 for no autoplay.");
        
        return;
    }
    
    $("#editContent .message").text("");
    
    $.ajax({
        url: "/admin/settings/scripts/editBanner.php",
        method: "POST",
        dataType: "json",
        data: ({id, name, postType, postName, animIn, animOut, speed}),
        success: function(data) {
            $("#editContent .message").text(data);
        }
    });
});

//Update Slides
$("#editContent .actions #apply").click(updateSlides);
$("#editContent input[name='preview']").click(updateSlides);

function updateSlides() {
    tinyMCE.triggerSave();
    
    if($(this).attr("id") == "apply") {
        var live = true;
        var reloadPage = false;
    }
    else {
        var live = false;
        var reloadPage = true;
    }
    
    var id = $.trim($("#editContent .id").text());
    
    $(".bannerSlides tr:not(.headers)").each(function() {
        var position = $.trim($(this).find("#position").text());
        var image = $(this).find("input[name='bannerImage']").val();
        var content = $(this).find(".tinyBanner").val();
        
        $.ajax({
            url: "/admin/settings/scripts/editBannerSlides.php",
            method: "POST",
            dataType: "json",
            data: ({live, id, position, image, content}),
            success: function(data) {
                console.log(data);
                
                if(reloadPage == true) {
                    window.location.reload();
                }
            }
        });
    });
}

//Update Post Names Dropdown
$("#editContent select[name='postType']").on("change", function() {
    postType = $(this).val();
    
    if(postType != null && postType != "") {
        $.ajax({
            url: "/admin/settings/scripts/updateBannerPosts.php",
            method: "POST",
            dataType: "json",
            data: ({postType}),
            success: function(data) {
                $("#editContent select[name='postName']").html(data);
            }
        });
    }
});

//Add Slides
$("#editContent input[name='addSlide']").click(function() {
    if($(".slide").length > 0) {
        var slideNumber = parseInt($(".slide").last().attr("id").split("slide")[1]) + 1;
    }
    else {
        var slideNumber = 1;
    }
    
    
    $("#editContent .bannerSlides").append(
        "<tr class='slide' id='slide" + slideNumber + "'>" +
            "<td id='position'>" + slideNumber + "</td>" +
            "<td id='backgroundImage'><input type='text' id='bannerImage' name='bannerImage'><input type='button' name='bannerBrowse' value='Browse'></td>" +
            "<td id='content'><textarea class='tinyBanner'></textarea></td>" +
            "<td><input type='button' name='deleteSlide' class='badButton' value='Delete Slide'></td>" +
        "</tr>"
    );
    
    tinymce.init({
        selector:'.tinyBanner',
        plugins: 'paste image imagetools table code save link moxiemanager media fullscreen',
        menubar: '',
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tabledelete | fontsizeselect | link insert | code fullscreen',
        relative_urls: false,
        remove_script_host: false,
        image_title: true,
        height: 100
    });
});

//Delete Slides
$("#editContent").on("click", "input[name='deleteSlide']", function() {
    var bannerId = $.trim($("#editContent .id").text());
    var position = $.trim($(this).closest("tr").find("#position").text());
    var row = $(this).closest("tr");
    
    if(confirm("Are you sure you want to delete this slide?")) {
        $.ajax({
            url: "/admin/settings/scripts/deleteBannerSlides.php",
            method: "POST",
            dataType: "json",
            data: ({bannerId, position}), 
            success: function(data) {
                if(data[0] == 1) {
                    row.remove();
                }
                else {
                    alert(data[1]);
                }
            }
        });
    }
});

//Image Browser
$(".bannerSlides").on("click", ".slide input[name='bannerBrowse']", function() {
    moxman.browse({fields: 'bannerImage'});
});