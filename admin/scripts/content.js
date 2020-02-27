var tableName = "posts"

//Update Visibility
$("input[name='hide']").click(function() {
    var btn = $(this);
    changeVisibility(btn, tableName);
});

$("input[name='show']").click(function() {
    var btn = $(this);
    changeVisibility(btn, tableName);
});

//Delete Content
$("input[name='delete']").click(function() {
    var btn = $(this);
    
    deleteContent(btn, tableName);
});

//Edit Content
$("input[name='edit']").click(function() {
    var btn = $(this);
    
    editContent(btn, tableName);
});

//Save Content
$("#contentManage input[type='submit']").click(function() {
    event.preventDefault();
    tinyMCE.triggerSave();

    var hasOptions = 0;
    var id = $("#contentManage input[name='postId']").val();
    var name = $("#contentManage input[name='postName']").val();
    var url = $("#contentManage input[name='postUrl']").val();
    var category = $("#contentManage select[name='postCategory']").val();
    var short = $("#contentManage textarea[name='postDesc']").val();
    var posted = $("#contentManage input[name='postDate']").val();
    var author = $("#contentManage input[name='postAuthor']").val();
    var customFile = $("#contentManage input[name='postCustom']").val();
    var content = $("#contentManage textarea[name='postContent']").val();
    var metaTitle = $("#contentManage input[name='metaTitle']").val();
    var metaDesc = $("#contentManage input[name='metaDescription']").val();
    var metaKeywords = $("#contentManage input[name='metaKeywords']").val();
    var metaAuthor = $("#contentManage input[name='metaAuthor']").val();
    
    if(name == "") {
        $("#contentManage #message").text("Title is missing");
        
        return;
    }
    else if(url == "") {
        $("#contentManage #message").text("URL is missing");
        
        return;
    }
    else if(posted == "") {
        $("#contentManage #message").text("Date Posted is missing");
        
        return;
    }
       
    var images = [];
    var i = 0;
    
    $(".imageUploader .image:not(.addImage)").each(function() {
        var image = $(this).find("img").attr("src");
        var alt = $(this).find("input[name='imageAlt']").val();
        var deleted = 0;
        
        images[i] = {
            alt: alt,
            url: image,
            main: ($(this).attr("id") == "main" ? 1 : 0),
            delete: deleted
        };
        
        i++;
    });
    
    if($("#additionalOptions").length) {
        hasOptions = 1;
        var specs = [];
        var i = 0;
        
        $(".specifications tr:not(#headers):not(#add)").each(function() {
            var specName = $(this).find("input[name='specName']").val();
            var specValue = $(this).find("input[name='specValue']").val();
            
            specs[i] = {
                name: specName,
                value: specValue
            };
            
            i++;
        });
    }
    
    $.ajax({
        url: "../scripts/contentManage.php",
        method: "POST",
        dataType: "json",
        data: ({id, name, url, category, short, posted, author, customFile, content, images, hasOptions, metaTitle, metaDesc, metaKeywords, metaAuthor, specs}),
        success: function(data) {
            $("#contentManage #message").text(data);
        },
        error: function(a, b, c) {
            console.log(a);
        }
    });
});

//Clear Search
$("input[name='clearSearch']").click(function() {
    window.location.href = window.location.href.split("&")[0];
});

//Upload Image
$(".addImage").click(function() {
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        skin: "snapcms",
        relative_urls: false,
        remove_script_host: true,
        oninsert: function(args) {
            var images = args.files;
            var i = 0;
            
            $(images).each(function() {
                var image = $(this)[0].url;
                var noUpload = false;
                var newSrc = image
                newSrc = newSrc.substring(newSrc.lastIndexOf("/") + 1, newSrc.length);
                
                $(".imageUploader .image:not(.addImage)").each(function() {
                    var existingSrc = $(this).find("img").attr("src");
                    existingSrc = existingSrc.substring(existingSrc.lastIndexOf("/") + 1, existingSrc.length);
                    
                    if(newSrc == existingSrc) {                    
                        noUpload = true;
                    }
                });
                
                if(noUpload == false) {
                    $(".imageUploader .addImage").before(
                        "<div class='image newImage'" + (i == 0 && $(".imageUploader #main").length <= 0 ? " id='main'" : "") + ">" + 
                            "<span id='deleteImage'>X</span>" +
                            "<div class='imageWrap'>" +
                                "<img src='" + image + "'>" +
                            "</div>" +
                            "<p id='imageAlt'><input type='text' name='imageAlt' placeholder='Enter Title...'></p>" +
                        "</div>"
                    );
                }
                else {
                    alert(newSrc + " already exists");

                    return;
                }
                
                i++;
            });
        }
    });
});

//Upload Image Post Type
$("#postTypeDetails input[name='imageSelector']").click(function() {
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        skin: "snapcms",
        relative_urls: false,
        remove_script_host: true,
        oninsert: function(args) {
            var image = args.files[0].url;
            
            $("#postTypeDetails input[name='imageUrl']").val(image);
        }
    });
});

//Remove Image
$(".imageUploader").on("click", "#deleteImage", function() {
    $(this).closest(".image:not(.addImage)").remove();
});

//Select Main Image
$(".imageUploader").on("click", ".imageWrap", function() {
    $(".imageUploader .image").attr("id", "");
    $(this).closest(".image:not(.addImage)").attr("id", "main");
});

//Add Spec
$(".specifications").on("click", "input[name='addSpec']", function() {
    $(".specifications #add").before(
        "<tr>" +
            "<td><input type='text' name='specName'></td>" +
            "<td><input type='text' name='specValue'></td>" +
            "<td><input type='button' name='deleteSpec' value='Delete' class='redButton'></td>" +
        "</tr>"
    );
});
    
//Delete Spec
$(".specifications").on("click", "input[name='deleteSpec']", function() {
    $(this).closest("tr").remove();
});

//Revert To Previous
$("input[name='revert']").click(function() {
    var revisionId = $("select[name='revisions']").val();
    var postId = $("input[name='postId']").val();
    
    if(confirm("Are you sure you want to revert to this version?")) {
        $.ajax({
            url: "../scripts/revertContent.php",
            method: "POST",
            dataType: "json",
            data: ({revisionId, postId}),
            success: function(data) {
                if(data[0] == 1) {
                window.location.reload();
                }
                else {
                    alert(data[1]);
                }
            }
        });
    }
});