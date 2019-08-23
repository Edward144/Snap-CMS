productOptions = true;

//Add Spec Row
$("input[name='addSpec']").click(function() {
    var row = 0;

    $(".specificationOption tr:not(.headers)").each(function() {
        if($(this).attr("id").split("spec")[1] > row) {
            row = $(this).attr("id").split("spec")[1];
        }
    });

    row = parseInt(row) + 1;

    $(".specificationOption").append(
        "<tr id='spec" + row + "'>" + 
            "<td><input type='text' name='specName'></td>" +
            "<td><input type='text' name='specValue'></td>" + 
            "<td><input class='badButton' type='button' name='deleteSpec' value='Delete Spec'></td>" +
        "</tr>"
    );
});

//Delete Spec Row
$(".specificationOption").on("click", "input[name='deleteSpec']", function() {
    $(this).closest("tr").remove();
});

//Delete Temp Gallery Items
$(document).ready(function() {    
    $.ajax({
        url: "/admin/settings/scripts/deleteTempGallery.php",
        method: "POST"
    });
});

//Upload Gallery Items
$("input[name='galleryOption']").on("change", function() {
    var file = $("input[name='galleryOption']").val();
    var file_data = $("input[name='galleryOption']").prop('files');
    var form_data = new FormData();

    if(file == "") {
        $(".galleryMessage").text("No file selected.");

        return;
    }
    else {
        $(".galleryMessage").text("");
    }

    $.each(file_data, function(index, fdata) {
        form_data.append("file[" + index + "]", fdata); 
    });

    $.ajax({
        url: "/admin/settings/scripts/galleryUpload.php",
        method: "POST",
        dataType: "json",
        contentType: false,
        processData: false,
        data: form_data,
        success: function(data){
            $(".galleryMessage").append(data);

            $.ajax({
                url: "/admin/settings/scripts/galleryList.php",
                method: "POST",
                dataType: "json",
                success: function(data) {
                    $(".galleryItems.uploaded").html(data);
                }
            });
        }
    });
});

//Delete Gallery Items
$(".galleryItems.current").on("click", ".galleryItem .galleryDelete", function() {
    $(this).closest(".galleryItem").remove();
});

$(".galleryItems.uploaded").on("click", ".galleryDelete", function() {
    var file = $(this).closest(".galleryItem").find("img").attr("alt");
    var galleryItem = $(this).closest(".galleryItem");
    
    galleryItem.remove();
    
    $("input[name='galleryOption']").val("");
    
    $.ajax({
        url: "/admin/settings/scripts/deleteTempGallery.php",
        method: "POST",
        data: ({file})
    })
});

//Set Main Gallery Item
$(".galleryItems").on("click", ".galleryItem", function() {
    $(".galleryItem").attr("id", "");
    
    $(this).attr("id", "galleryMain");
});