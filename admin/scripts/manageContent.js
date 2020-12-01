//Clear Search
$("input[name='clearSearch']").click(function() {
    window.location.href = window.location.href.split("?")[0];
});

//Create Content
$("#createContent").submit(function() {
    $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit")).parent(".form-group")
    $(this).find(":submit").prop("disabled", true);
});

//Update Landing
$("#updateLanding").submit(function() {
    tinyMCE.triggerSave();
    $("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit")).parent(".form-group")
    $(this).find(":submit").prop("disabled", true);
})

//Change Visibility
$("input[name='visibility']").click(function() {
    var btn = $(this);
    var action = (btn.val() == "Hidden" ? "1" : "0");
    
    $.ajax({
        url: "admin/scripts/manageContent.php",
        method: "POST",
        dataType: "json",
        data: ({id: btn.attr("data-id"), action, method: "changeVisibility"}),
        success: function(data) {
            btn.val((data == 1 ? "Visible" : "Hidden"));
        }
    });
});

//Delete Content
$("input[name='delete']").click(function() {
    var btn = $(this);
    
    $(".alert").remove();
    
    if(confirm("Are you sure you want to delete this content?")) {
        $.ajax({
            url: "admin/scripts/manageContent.php",
            method: "POST",
            dataType: "json",
            data: ({id: btn.attr("data-id"), method: "deleteContent"}),
            success: function(data) {
                if(data[0] == 1) {
                    window.location.reload();
                }
                else {
                    $(btn).parents(".col").first().append(
                        `<div class="alert alert-` + (data[0] == 0 ? 'danger' : 'success') + ` mt-3">
                            ` + data[1] + `
                        </div>`
                    );
                }
            }
        });
    }
});

//Edit Content
$("#contentList input[name='edit']").click(function() {
    window.location.href = window.location.href.split("?")[0] + "?id=" + $(this).attr("data-id");
});

//Return to List
$("input[name='return']").click(function() {
    window.location.href = window.location.href.split("?")[0];
});

//Save Content
$("#managePost").submit(function() {
	event.preventDefault();
	tinyMCE.triggerSave();
	
	$(this).find(".alert, .invalid-feedback, .spinner-border").remove();
	$(this).find(".is-invalid").removeClass("is-invalid");
	$(this).find(":submit:not(button)").prop("disabled", false);
	
	var form = $(this);
	var valid = true;
	var url = $(this).find("input[name='url']");
	
	//Validate URL
	if(url.val() == null || url.val() == "") {
		url.addClass("is-invalid");
		$("<div class='invalid-feedback'>A unique URL is required</div>").insertAfter(url);
		valid = false;
	}
	else if(/^[a-zA-Z0-9\-\/\#\?\=]+$/.test(url.val()) == false) {
		url.addClass("is-invalid");
		$("<div class='invalid-feedback'>URL can only contain letters, numbers, hyphens and forward slashes</div>").insertAfter(url);
		valid = false;
	}
	
	//Get Slider Data
	var carouselData = {};
	
	$(".carouselPreview .carousel-item").each(function() {
		carouselData[$(this).attr("data-item")] = {
			imageUrl: $(this).find("img").attr("src"),
			title: $(this).find("input[name='slideTitle']").val(),
			small: $(this).find("input[name='slideSmall']").val(),
			captionPosition: ($(this).find(".btn-group button[data-active='true']").length ? $(this).find(".btn-group button[data-active='true']").attr("id") : 'bottom')
		}
	});
	
	if(valid == true) {
		$("<div class='spinner-border ml-1'><span class='sr-only'>Processing...</span></div>").insertAfter($(this).find(":submit:not(button)"));
		$(this).find(":submit:not(button)").prop("disabled", true);
		
		var btn = $(this).find(":submit:not(button)");
		var formData = $(this).serialize();
		
		$.ajax({
			url: "admin/scripts/manageContent.php",
			method: "POST",
			dataType: "json",
			data: ({formData, carouselData, method: "saveContent"}),
			success: function(data) {
				$("<div class='alert alert-" + (data[0] == 0 ? "danger" : "success") + "'>" + data[1] + "</div>").insertAfter(btn.parents(".form-group"));
				form.find(":submit:not(button)").prop("disabled", false);
				form.find(".spinner-border").remove();
			},
			error: function() {
				$("<div class='alert alert-danger'>An unknown error has occurred</div>").insertAfter(btn.parents(".form-group"));
				form.find(":submit:not(button)").prop("disabled", false);
				form.find(".spinner-border").remove();
			}
		});
	}
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
                $("<img src='" + image + "' class='img-fluid'>").insertAfter($(".coverImage label"));
            }
            
			$(".coverImage input[name='coverImage']").val(image);
            $("input[name='clearImage']").show();
        }
    });
});

//Delete Image
$("input[name='clearImage']").click(function() {
    $(".coverImage input[name='coverImage']").val("");
    $(".coverImage img").remove();
    $(this).hide();
});

//Add Carousel Images
$("#addImage").click(function() {
	event.preventDefault();
	
	var btn = $(this);
    
	btn.parents(".col").find(".alert").remove();
	
    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        skin: "snapcms",
		document_base_url: http_host + server_name + root_dir,
        relative_urls: true,
        remove_script_host: true,
        oninsert: function(args) {
            var images = args.files;
			
			$.each(images, function() {
				if($("#carouselImages").find("img[src='" + $(this)[0].url + "']").length == 0) {
					$("<div class='imageWrap mr-2'><button class='btn btn-danger border-0 close' style='position: absolute; top: 0; right: 0; padding: 0 4px 4px 4px;'>&times;</button><img src='" + $(this)[0].url + "'></div>").insertBefore(btn);
				}
				else {
					$("<div class='alert alert-danger'>This image already exists</div>").insertAfter(btn.parents(".form-group").first());
				}
			});
			
			$("#carouselImages").sortable({items: ">.imageWrap", stop: function() { generateCarousel(); } });
			
			generateCarousel();
        }
    });
});

function generateCarousel() {
	var cImages = [];
	
	$("#carouselImages > .imageWrap img").each(function() {
		cImages.push($(this).attr("src"));
	});
	
	if(cImages.length > 0) {
		var items = '';
		var i = 0;
		
		$.each(cImages, function(index, url) {
			items += 
				`<div class="carousel-item h-100 ` + (i == 0 ? `active` : ``) + `" data-interval="10000" data-item="` + i + `">
					<img src="` + url + `" class="h-100 w-100" style="object-fit: cover;" alt="slide` + (i + 1) + `">
					
					<div class="carousel-caption d-none d-md-block">
						<div class="btn-group mb-2">
							<button type="button" class="btn btn-secondary" id="top"><span class="fa fa-long-arrow-alt-up"></span></button>
							<button type="button" class="btn btn-secondary" id="center"><span class="fa fa-arrows-alt-h"></span></button>
							<button type="button" class="btn btn-secondary" id="bottom" data-active="true"><span class="fa fa-long-arrow-alt-down"></span></button>
						</div>

						<h5><input type="text" placeholder="Slide Title" value="" name="slideTitle"></h5>
        				<p><input type="text" placeholder="Slide Small Text" value="" name="slideSmall"></p>
					</div>
				</div>`;
			
			i++;
		});
		
		var carousel = 
			`<div id="carouselPrev" class="carousel slide d-flex align-items-center" data-ride="carousel">
				<div class="carousel-inner" style="height: 400px">
					` + items + `
				</div>

				<a class="carousel-control-prev" href="#carouselPrev" role="button" data-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="sr-only">Previous</span>
				</a>

				<a class="carousel-control-next" href="#carouselPrev" role="button" data-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="sr-only">Next</span>
				</a>
			</div>`;

		$(".carouselPreview").html(carousel);
	}
	else {
		$(".carouselPreview").html("");
	}
}

$(".carouselPreview").on("click", ".btn-group button", function() {
	var placement = $(this).attr("id").toLowerCase();
	
	$(this).parents(".btn-group").first().find("button").attr("data-active", "");
	$(this).attr("data-active", "true");
	
	switch(placement) {
		case "top": 
			$(this).parents(".carousel-caption").css({
				"top": "0",
				"bottom": "",
				"transform": ""
			});
			
			break;
		case "bottom": 
			$(this).parents(".carousel-caption").css({
				"top": "",
				"bottom": "0",
				"transform": ""
			});
			
			break;
		case "center": 
			$(this).parents(".carousel-caption").css({
				"top": "50%",
				"bottom": "",
				"transform": "translateY(-50%)"
			});
			
			break;
	}
});

$("#carouselImages").on("click", ".imageWrap .close", function() {
	if(confirm("Remove this image?")) {
		$(this).parents(".imageWrap").first().remove();
		
		generateCarousel();
	}
})

$(document).ready(function() {
	$("#carouselImages").sortable({items: ">.imageWrap", stop: function() { generateCarousel(); } });
});