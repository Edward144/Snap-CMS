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

//Upload Gallery Items New
$("input[name='galleryOptionNew']").on("click", function() {
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        no_host: true,
        oninsert: function(args) {
            var file = $("input[name='galleryOptionNew']").val();
            var images = args.files;
            var imageUrls = [];
            
            $.each(images, function(index, value) {
                imageUrls.push(value.url);
            });
            
            console.log(imageUrls);
            
            $.ajax({
                url: "/admin/settings/scripts/galleryUploadNew.php",
                method: "POST",
                dataType: "json",
                data: ({imageUrls}),
                success: function(data){
                    $(".galleryMessage").html(data);

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