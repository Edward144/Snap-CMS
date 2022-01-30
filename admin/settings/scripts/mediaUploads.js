//Upload File
$("#mediaUpload input[type='submit']").click(function() {
    event.preventDefault();
    
    var url = window.location.href;
    var file = $("#mediaUpload input[name='mediaFile']").val();
    var file_data = $("#mediaUpload input[name='mediaFile']").prop('files')[0];
    var form_data = new FormData();
    
    if(file == "") {
        $("#mediaUpload .message").text("No file selected.");
        
        return;
    }
    
    if(url.indexOf('?f=') >= 0) {
        var directory = url.split('?f=')[1];
    }
    else {
        var directory = 'useruploads';
    }
    
    if(directory.indexOf('&?') >= 0) {
        directory = directory.split('&?')[0];
    }
    
    form_data.append("file", file_data);
    form_data.append("directory", directory);
    
    $.ajax({
        url: "settings/scripts/mediaUpload.php",
        method: "POST",
        dataType: "json",
        contentType: false,
        processData: false,
        data: form_data,
        success: function(data){
            if(data == 1) {
                location.reload();
            }
            else {
                $("#mediaUpload .message").text(data);
            }
        }
     });
});

//Create Folder Input
$("#mediaAddDirectory").click(function() {
    $("#mediaAddOverlay").css("display" , "block");
    $("#mediaAddHidden").attr("id", "mediaAddVisible");
});

$("#mediaAddClose span").click(function() {
    $("#mediaAddOverlay").css("display" , "");
    $("#mediaAddVisible").attr("id", "mediaAddHidden");
    
    $("#mediaAddInput input[name='directoryName']").val("");
});

//Create New Folder
$("#mediaAddInput input[type='submit']").click(function() {
    event.preventDefault();
    
    var url = window.location.href;
    var dirName = $("#mediaAddInput input[name='directoryName']").val();
    
    if(url.indexOf('?f=') >= 0) {
        var directory = url.split('?f=')[1];
    }
    else {
        var directory = 'useruploads';
    }
    
    if(directory.indexOf('&?') >= 0) {
        directory = directory.split('&?')[0];
    }
    
    $.ajax({
        url: "settings/scripts/mediaAddDirectory.php",
        method: "POST",
        json: "json",
        data: ({directory, dirName}),
        success: function(data) {
            if(data == 1) {                
                location.reload();
            }
            else {
                $("#mediaAddInput .message").text(data);
            }
        }
    });
});

//Rename Directory
var existingUrl;

$(".mediaDir .mediaEdit").click(function() {
    existingUrl = $(this).parents(".mediaDir").find("a").attr("href").split("?f=")[1];
    
    $("#mediaEditOverlay").css("display", "block");
});

$(".mediaFile .mediaEdit").click(function() {
    var url = window.location.href;
    
    if(url.indexOf('?f=') >= 0) {
        var currUrl = url.split('?f=')[1];
    }
    else {
        var currUrl = 'useruploads';
    }
    
    existingUrl = currUrl + "/" + $(this).parents(".mediaFile").find(".mediaName").text();
    
    $("#mediaEditOverlay").css("display", "block");
});

$("#mediaEditClose").click(function() {
    $("#mediaEditOverlay").css("display", "");
    $("#mediaEditInput input[name='newName']").val("");
    $("#mediaEditInput .message").text("");
    
    existingUrl = undefined;
});

$("#mediaEditInput input[type='submit']").click(function() {
    event.preventDefault();
    
    var url = window.location.href;
    
    if(url.indexOf('?f=') >= 0) {
        var currUrl = url.split('?f=')[1];
    }
    else {
        var currUrl = 'useruploads';
    }
    
    newName = $("#mediaEditInput input[name='newName']").val();
    
    if(newName == "") {
        $("#mediaEditInput .message").text("New Name is missing.");
        
        return;
    }
    
    $.ajax({
        url: "settings/scripts/mediaRename.php",
        method: "POST",
        dataType: "json",
        data: ({newName, existingUrl, currUrl}),
        success: function(data) {
            if(data == 1) {
                location.reload();
            }
            else {
                $("#mediaEditInput .message").text(data);
            }
        }
    });
});

//Delete Media
$(".mediaDir .mediaDelete").click(function() {
    var url = $(this).parents(".mediaDir").find("a").attr("href").split("?f=")[1];
    
    if(confirm("Are you sure you want to delete this folder? All files will also be deleted.")) {
        $.ajax({
            url: "settings/scripts/mediaDelete.php",
            method: "POST",
            dataType: "json",
            data: ({type: "directory", url}),
            success: function(data) {
                if(data == 1) {
                    location.reload();
                }
                else {
                    alert(data);
                }
            }
        });
    }
});

$(".mediaFile .mediaDelete").click(function() {
    var file = $(this).parents(".mediaFile").find(".mediaName").text();
    var url = window.location.href;
    
    if(url.indexOf('?f=') >= 0) {
        var directory = url.split('?f=')[1];
    }
    else {
        var directory = 'useruploads';
    }
    
    url = directory + "/" + file;
    
    if(confirm("Are you sure you want to delete this file?")) {
        $.ajax({
            url: "settings/scripts/mediaDelete.php",
            method: "POST",
            dataType: "json",
            data: ({type: "file", url}),
            success: function(data) {
                if(data == 1) {
                    location.reload();
                }
                else {
                    alert(data);
                }
            }
        });
    }
});

//Find Details
$(".mediaFile .mediaImage").click(findDetails);
$(".mediaFile .mediaName").click(findDetails);

function findDetails() {
    var filename = $(this).parents(".mediaFile").find(".mediaName").text();
    var url = window.location.href;
    
    if(url.indexOf('?f=') >= 0) {
        var directory = url.split('?f=')[1];
    }
    else {
        var directory = 'useruploads';
    }
    
    $(".mediaInfo h2").text(filename);
    
    $(".mediaInfo #imageLink").css("display", "");
    $(".mediaInfo #dimensions").css("display", "");
            
    $(".mediaInfo #imageLink a").attr("href", "#");
    
    imgs = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
    
    $.each(imgs, function(extension) {       
        if(filename.indexOf(imgs[extension]) >= 0) {
            $(".mediaInfo #imageLink").css("display", "block");
            $(".mediaInfo #dimensions").css("display", "block");
            
            $(".mediaInfo #imageLink a").attr("href", "/admin/" + directory + "/" + filename);
        }
    });
    
    $(".mediaInfo #download a").attr("href", "/admin/" + directory + "/" + filename);
    $(".mediaInfo #url").text("/admin/" + directory + "/" + filename);
    
    var file = "/admin/" + directory + "/" + filename;
    var filesize;
    var dimensions;
    
    $.ajax({
        url: "settings/scripts/mediaInfo.php",
        method: "POST", 
        dataType: "json",
        data: ({file}),
        success: function(data) {
            if(data[0] == 1) {
                filesize = data[1];
                dimensions = data[2];
                
                $(".mediaInfo #filesize").text(filesize);
    
                $(".mediaInfo #dimensions span").text(dimensions);
            }
            
            $(".mediaInfo").css("display", "block");
        }
    });
}

//Close Popup
function clearUrl() {
    var url = window.location.href.split('&f=')[0];
    
    window.history.pushState(window.location.pathname, "", url);
}

$(".mediaList.popup .mediaClose").click(function() {
    if($(this).closest(".mediaList.popup").attr("id") != "hidden") {
        $(this).closest(".mediaList.popup").attr("id", "hidden");
    }
    
    clearUrl();
});

//Copy Popup Url
var copyFile;

$(".mediaList.popup .mediaFile").click(function() {
    var filename = $(this).find(".mediaName").text();
    var url = window.location.href;
    
    if(url.indexOf('?f=') >= 0) {
        var directory = url.split('?f=')[1];
    }
    else {
        var directory = 'useruploads';
    }
    
    copyFile = "/admin/" + directory + "/" + filename;
    
    $(".mediaList.popup").attr("id", "hidden");
    
    clearUrl();
});