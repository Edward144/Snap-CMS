//Change Post Type
$("#changeCategory select[name='postType']").on("change", function() {
    window.location.href = root_dir + "admin/categories/post-type/" + $(this).val();
});

//Validate Create Form
$("#createCategory").submit(function() {
    if($("#createCategory input[name='catName']").val() == "") {
        $("#createCategory #message").text("Category name is missing");
        
        event.preventDefault();
        return;
    }
});

//Delete Category
$(".categoryTreeWrap").on("click", "input[name='delete']", function() {
    if(confirm("This category and all sub categories will be deleted.")) {
        $(this).closest("li").find("input[name='hDelete']").val(1);
        $(this).closest("li").css("display", "none");
    }
}); 

//Edit Category
$(".categoryTreeWrap").on("click", "input[name='edit']", function() {
    var id = $(this).closest("li").find("input[name='hId']").first().val();
    
    $("input[name='edit']").attr("disabled", true);
    $("input[name='saveTree']").attr("disabled", true);
    
    $("body").prepend(
        "<div class='editor formBlock column' data-id='" + id + "' style='width: 50%; max-height: 50%; position: absolute; top: 50%; transform: translateY(-50%); background: #fff; z-index: 9999; margin: auto; left: 0; right: 0; box-shadow: 1px 1px 10px -5px #000;'>" +
            "<h2 class='greyHeader'>Edit Category <span id='editorClose' style='height: 29px; width: 29px; text-align: center; cursor: pointer; float: right; background: #fff;  border-radius: 100%;'>X</span></h2>" +
            "<div style='height: 100%;'>" +
                "<form id='editCategory'>" +
                    "<p>" +
                        "<label>Name</label>" +
                        "<input type='text' name='sName' value='" + $(this).closest("li").find("input[name='hName']").first().val() + "'>" +
                    "</p>" + 
                    "<p>" +
                        "<label>Image</label>" +
                        "<input type='text' name='sImage' value='" + $(this).closest("li").find("input[name='hImage']").first().val() + "' style='width: calc(100% - 340px) !important;'>" +
                        "<input type='button' name='imageSelector' value='Choose File' style='padding: 0.5em; margin-left: 4px;'>" +
                    "</p>" + 
                    "<p>" +
                        "<label>Description</label>" +
                        "<input type='text' name='sDesc' value='" + $(this).closest("li").find("input[name='hDesc']").first().val() + "'>" +
                    "</p>" + 
                "</form>" +
            "</div>" +
        "</div>"
    );
});

//Save Changes on Editor Close
$("body").on("click", "#editorClose", function() {    
    var id = $(".editor").attr("data-id");
    
    if($(".editor input[name='sName']").val() == "") {
        $(".editor input[name='sName']").val($("div#edit" + id + " input[name='hName']").val());
    }
    
    $("div#edit" + id + " input[name='hName']").val($(".editor input[name='sName']").val());
    $("div#edit" + id + " input[name='hDesc']").val($(".editor input[name='sDesc']").val());
    $("div#edit" + id + " input[name='hImage']").val($(".editor input[name='sImage']").val());
    
    $("input[name='edit']").attr("disabled", false);
    $("input[name='saveTree']").attr("disabled", false);
    $(".editor").remove();
});

//Choose Image
$("body").on("click", ".editor input[name='imageSelector']", function() {
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        skin: "snapcms",
        oninsert: function(args) {
            var image = args.files[0].url;

            $(".editor input[name='sImage']").val(image);
        }
    });
});

//Save Tree
$("input[name='saveTree']").click(function() {
    var catTree = [];
    var index = 0;
    var postTypeId = $("#createCategory input[name='postTypeId']").val();
    
    $(".categoryTree li").each(function() {
        catTree[index] = {
            id: $(this).find("input[name='hId']").first().val(),
            name: $(this).find("input[name='hName']").first().val(),
            image: $(this).find("input[name='hImage']").first().val(),
            description: $(this).find("input[name='hDesc']").first().val(),
            parent: $(this).find("input[name='hParent']").first().val(),
            delete: $(this).find("input[name='hDelete']").first().val(),
            level: $(this).find("input[name='hLevel']").first().val()
        };
        
        index++;
    });
    
    $.ajax({
        url: root_dir + "admin/scripts/saveCategories.php",
        method: "POST",
        dataType: "json",
        data: ({postTypeId, catTree}),
        success: function(data) {
            if(data[0] == 1) {
                window.location.reload();
            }
            else {
                $(".treemessage").text(data[1]);
            }
        }
    })
});