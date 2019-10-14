var tableName = "sliders"

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

//Add New Slide
$("input[name='addSlide']").click(function() {
    var position = 0;

    if($("#noSlides").length > 0) {
        $("#noSlides").remove();
    }

    $(".slideRow").each(function() {
        if($(this).find("input[name='position']").val() > position) {
            position = $(this).find("input[name='position']").val();
        }
    });

    position++;

    $.ajax({
        url: "../scripts/addSlide.php",
        method: "POST",
        dataType: "json",
        data: ({sliderId: $(this).attr("data-slider"), position}),
        success: function(data) {
            if(data == 1) {
                $("#slidesTable").append(
                    "<tr class='slideRow'>" + 
                        "<td style='width: 80px; min-width: 80px;'>" +
                            "<input type='number' step='1' name='position' value='" + position + "' style='text-align: center;'>" +
                        "</td>" + 
                        "<td style='width: 300px; min-width: 300px;'>" + 
                            "<input type='text' name='backgroundImage' class='hasButton'>" +
                            "<input type='button' name='imageSelector' value='Choose File' style='margin-left: 4px;'>" +
                        "</td>" + 
                        "<td>" + 
                            "<textarea class='tinySlider'></textarea>" +
                        "</td>" + 
                        "<td style='width: 80px; min-width: 80px;'>" +
                            "<input type='button' name='deleteSlide' value='Delete' class='redButton'>" +
                        "</td>" + 
                    "</tr>"
                );

                tinymce.init({
                    selector:'.tinySlider',
                    plugins: 'paste image imagetools table code save link moxiemanager media fullscreen',
                    menubar: '',
                    toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table tabledelete | fontsizeselect | link insert | code fullscreen',
                    relative_urls: false,
                    remove_script_host: false,
                    image_title: true,
                    height: 100
                });
            }
            else {
                $(".slidesMessage").text("Error: Could not add slide");
            }
        }
    });
});

//Select Image
$("#slidesTable").on("click", "input[name='imageSelector']", function() {
    var textbox = $(this).closest("tr").find("input[name='backgroundImage']").first();

    moxman.browse({
        extensions: 'png, jpg, jpeg, gif, webp, svg',
        skin: "snapcms",
        oninsert: function(args) {
            var image = args.files[0].url;

            textbox.val(image);
        }
    });
});

//Delete Slide
$(".sliderContent").on("click", "input[name='deleteSlide']", function() {
    $(this).closest("tr").remove();
});

//Find Posts With Selected Post Type
$("select[name='postType']").on("change", function() {
    var postType = $(this).val();

    $.ajax({
        url: "../scripts/findPosts.php",
        method: "POST",
        dataType: "json",
        data: ({postType}),
        success: function(data) {
            $("select[name='postName']").html(data);
        }
    });
});

//Save Slider
$("#sliderManage input[type='submit']").click(function() {
    event.preventDefault();
    tinyMCE.triggerSave();

    var id = $("#sliderManage input[name='sliderId']").val(); 
    var name = $("#sliderManage input[name='sliderName']").val(); 
    var postType = $("#sliderManage select[name='postType']").val(); 
    var postName = $("#sliderManage select[name='postName']").val();
    var animationIn = $("#sliderManage input[name='animationIn']").val(); 
    var animationOut = $("#sliderManage input[name='animationOut']").val(); 
    var speed = $("#sliderManage input[name='speed']").val(); 
    var slides = [];
    var index = 0;

    $("#slidesTable .slideRow").each(function() {
        slides[index] = {
            position: $(this).find("input[name='position']").val(),
            image: $(this).find("input[name='backgroundImage']").val(),
            content: $(this).find("textarea.tinySlider").val()
        };

        index++;
    });

    if(name == "") {
        $(".sliderMessage").text("Name is missing");

        return;
    }

    if(postType == 0 || postType == null) {
        $(".sliderMessage").text("Post type is missing");

        return;
    }

    if(postName == 0 || postName == null) {
        $(".sliderMessage").text("Post name is missing");

        return;
    }

    if(speed < 0 || speed > 30000) {
        $(".sliderMessage").text("Speed must be between 0 and 30000");

        return;
    }

    $.ajax({
        url: "../scripts/sliderManage.php",
        method: "POST",
        dataType: "json",
        data: ({id, name, postType, postName, animationIn, animationOut, speed, slides}),
        success: function(data) {
            $(".sliderMessage").text(data);
        }
    });
});