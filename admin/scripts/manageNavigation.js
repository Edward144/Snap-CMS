//Change Menu
$("select[name='selectMenu']").change(function() {
	window.location.href = "admin/navigation/" + $(this).val();
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
		url: "admin/scripts/manageNavigation.php",
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
		document_base_url: http_host + server_name + root_dir,
        relative_urls: true,
        remove_script_host: true,
        oninsert: function(args) {
            var image = args.files[0].url;
            btn.parent(".imageUrl").find("input[type='hidden']").val(image);
            
            if(btn.siblings("img").length) {
                btn.siblings("img").attr("src", image);
            }
            else {
                $("<img src='" + image + "' class='img-fluid'>").insertAfter(btn.parent(".imageUrl").find("label"));
            }
            
            $("input[name='clearImage']").show();
        }
    });
});

//Delete Image
$("input[name='clearImage']").click(function() {
    $(this).parent(".imageUrl").find("input[type='hidden']").val("");
    $(this).parent(".imageUrl").find("img").remove();
    $(this).hide();
});

//Insert To Menu
$("#updateMenu").submit(function() {
    var valid = true;
    var url = $(this).find("input[name='url']");
    
    if(/^[a-zA-Z0-9\-\/\#\?\=\.\_\s]+$/.test(url.val()) == false) {
		url.addClass("is-invalid");
		$("<div class='invalid-feedback'>URL can only contain letters, numbers, hyphens and forward slashes</div>").insertAfter(url);
		valid = false;
	}
    
    if(valid == true) {
        $(this).find(":submit").prop("disabled", true);
        $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit"));
    }
	else {
		event.preventDefault();
	}
});

//Open Item Edit
$(".navigationTree input[name='edit']").click(function() {
    var item = $(this).parents("li").first();
    
    $("body").append("<div class='modal-backdrop fade show'></div>");
    item.find(".modal").first().show();
    item.find(".modal").first().find("input").first().focus(); 
});

//Close Item Edit
$("body").on("click", ".modal-backdrop, .modal .close", function() {
    $(".invalid-feedback").remove();
    $(".is-invalid").removeClass("is-invalid");
    
    var valid = true;
    var url = $(".modal:visible").find("input[name='hUrl']");
    
    if(/^[a-zA-Z0-9\-\/\#\?\=\.\_\s]+$/.test(url.val()) == false) {
		url.addClass("is-invalid");
		$("<div class='invalid-feedback'>URL can only contain letters, numbers, hyphens and forward slashes</div>").insertAfter(url);
		valid = false;
	}
    
    if(valid == true) {        
        $(".modal, .modal-backdrop").animate({
            "opacity": 0
        }, 500, function() {
            $(".modal-backdrop").remove();
            $(".modal").hide();
            $(".modal").css("opacity", "1");
        });
    }
});

//Change Icon
$(".modal input[name='hIcon']").on("keyup", function() {
    $(this).siblings("label").children(".iconExample").attr("class", "iconExample " + $(this).val());
});

//Delete Item
$(".navigationTree input[name='delete']").click(function() {
    if(confirm("Are you sure you want to delete this item and any sub-items?")) {
        $(this).parents("li").first().css("display", "none");
        $(this).parents("li").first().find(".modal").first().find("input[name='hDelete']").val(1);
    }
});

//Re-Order Items
$(".navigationTree").sortable({
    items: "li",
    connectWith: ".navigationTree",
    dropOnEmpty: true,
    stop: function() {
        $(".navigationTree").each(function() {
            var position = 0;
            
            if($(this).parent().is("div")) {
                var parent = 0;
            }
            else {
                var parent = $(this).parents("li").first().attr("id").split("navigation")[1];
            }
            
            $(this).children("li").each(function() {
                var edit = $(this).find(".modal").first();
                
                edit.find("input[name='hParent']").val(parent);
                edit.find("input[name='hLevel']").val(0);
                edit.find("input[name='hPosition']").val(position++);
            });
        });
    }
});

//Save Tree
$("input[name='saveTree']").click(function() {
    event.preventDefault();
    
    var menuId = $(this).attr("data-menu");
    var btn = $(this);
    var tree = [];
    var i = 0;    
    
    btn.parents(".col").find(".alert-danger").remove();
    $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</div>").insertAfter($(this));
    $(this).prop("disabled", true);
    
    $(".navigationTree li").each(function() {
        var edit = $(this).find(".modal").first();
        
        tree[i] = {
            id: edit.find("input[name='hId']").val(),
            parentId: edit.find("input[name='hParent']").val(),
            position: edit.find("input[name='hPosition']").val(),
            level: edit.find("input[name='hLevel']").val(),
            name: edit.find("input[name='hName']").val(),
            url: edit.find("input[name='hUrl']").val(),
            image: edit.find("input[name='hImage']").val(),
			visible: edit.find("input[name='hVisible']:checked").val(),
            icon: edit.find("input[name='hIcon']").val(),
            delete: edit.find("input[name='hDelete']").val()
        }
        
        i++;
    });
	
	console.log(tree);
	
	//return;
    
    $.ajax({
        url: "admin/scripts/manageNavigation.php",
        method: "POST",
        dataType: "json",
        data: ({tree: JSON.stringify(tree), menuId, method: "saveTree"}),
        success: function(data) {            
            if(data[0] == 1) {
                window.location.reload();
            }
            else {
                $("<div class='alert alert-danger'>" + data[1] + "</div>").insertAfter(btn.parent("div"));
                btn.prop("disabled", false);
                btn.siblings(".spinner-border").remove();
            }
        }
    });
});