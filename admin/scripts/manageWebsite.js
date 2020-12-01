//Add Spinners to Submit Buttons
$("form").submit(function() {
    $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit"));
    $(this).find(":submit").prop("disabled", true);
});

//Choose Image
$("input[name='selectImage']").click(function() {
    var btn = $(this);
    
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        skin: "snapcms",
		document_base_url: http_host + server_name + root_dir,
        relative_urls: true,
        remove_script_host: true,
        oninsert: function(args) {
            var image = args.files[0].url;
            $("input[name='logo']").val(image);
            
            if(btn.siblings("img").length) {
                btn.siblings("img").attr("src", image);
            }
            else {
                $(".logo").prepend("<img src='" + image + "' class='img-fluid'>");
            }
            
            $("input[name='clearImage']").show();
        }
    });
});

//Delete Image
$("input[name='clearImage']").click(function() {
    $(".logo input[name='logo']").val("");
    $(".logo img").remove();
    $(this).hide();
});

