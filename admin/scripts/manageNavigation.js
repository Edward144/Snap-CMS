//Change Menu
$("select[name='selectMenu']").change(function() {
	window.location.href = root_dir + "admin/navigation/" + $(this).val();
});

//Create Menu
$("#createMenu").submit(function() {
	var valid = true;
	var name = $(this).find("input[name='newMenu']");
	
	$(this).find(".is-invalid").removeClass("is-invalid");
	$(this).find(".invalid-feedback").remove();
	
	//Validate Name
	if(/^[a-zA-Z0-9\s]+$/.test(name.val()) == false) {
		$("<div class='invalid-feedback'>Only use letters, numbers and spaces</div>").insertAfter(name);
		name.addClass("is-invalid");
		valid = false;
	}
	
	if(valid == true) {
		$(this).find(":submit").prop("disabled", true);
		$("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</div>").insertAfter($(this).find(":submit"));
		
		$.ajax({
			url: root_dir + ""
		});
	}
	else {
		event.preventDefault();
	}
});

//Delete Menu
$("#deleteMenu").submit(function() {
    if(!confirm("Are you sure you want to delete this menu?")) {
        event.preventDefault();
    }
    else {
        $(this).find(":submit").prop("disabled", true);
        $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit"));
    }
});

//Choose Existing Page
$("select[name='existing']").change(function() {
	var id = $(this).val();
	var form = $(this).parents("form").first();
    
	$.ajax({
		url: root_dir + "admin/scripts/manageNavigation.php",
        method: "POST",
        dataType: "json",
        data: ({id, method: "pullPage"}),
        success: function(data) {
            form.find("input[name='name']").val(data['name']);
            form.find("input[name='url']").val(data['url']);
        }
	});
});

//Choose Image
$("input[name='selectImage']").click(function() {
    var btn = $(this);
    
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        skin: "snapcms",
        relative_urls: false,
        remove_script_host: true,
        oninsert: function(args) {
            var image = args.files[0].url;
            $("input[name='logo']").val(image);
            
            if(btn.siblings("img").length) {
                btn.siblings("img").attr("src", image);
            }
            else {
                $("<img src='" + image + "' class='img-fluid'>").insertAfter($(".imageUrl label"));
            }
            
            $("input[name='clearImage']").show();
        }
    });
});

//Delete Image
$("input[name='clearImage']").click(function() {
    $(".imageUrl input[name='imageUrl']").val("");
    $(".imageUrl img").remove();
    $(this).hide();
});

//Insert To Menu
$("#updateMenu").submit(function() {
    var valid = true;
    var url = $(this).find("input[name='url']");
    
    if(/^[a-zA-Z0-9\-\/\#\?\=]+$/.test(url.val()) == false) {
		url.addClass("is-invalid");
		$("<div class='invalid-feedback'>URL can only contain letters, numbers, hyphens and forward slashes</div>").insertAfter(url);
		valid = false;
	}
    
    if(valid == true) {
        $(this).find(":submit").prop("disabled", true);
        $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit"));
    }
});